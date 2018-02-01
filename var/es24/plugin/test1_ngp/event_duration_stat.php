<?PHP
//print_r($_SESSION);
//var_dump($_SESSION['__mylog']);
	
	require_once( 'env.php' );
	require_once( 'lib/common.php' );
	
	////////////////////////////////////////////////////////////////////////////
	// パーツのインクルード
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	
	$user_vars = user_vars_load();
	$event_id_list = $user_vars['event_id'];
	
	$stat_daycount_list = array(
		0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 30, 60
	);
	
	$event_id = (isset($LOCAL_SESSION['event_id']) ? $LOCAL_SESSION['event_id'] : 0);
	
	////////////////////////////////////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// 検索条件の変更
	if (isset($_POST['f1_submit'])) {
		
		// 検索条件の確保
		if (isset($_POST['event_id'])) {
			$LOCAL_SESSION['event_id'] = $_POST['event_id'];
			$LOCAL_SESSION['f1_search'] = 1;
		}
		else {
			unset($LOCAL_SESSION['event_id']);
			unset($LOCAL_SESSION['f1_search']);
		}
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、検索・抽出条件フォーム
	
	$psmarty->assign('event_id_list', $event_id_list);
	$psmarty->assign('event_id', $event_id);
	
	// 検索条件が指定されていない場合はここで終わらせる
	if (! isset($LOCAL_SESSION['f1_search'])) {
		return ;
	}
	$psmarty->assign('is_f1_search', 1);
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成
	$admin_con = admin_con();
	
	$wa = array();
	$wa[] = sprintf(' event_id = %d ', $event_id);
	$wa[] = " '0000-00-00 00:00:00' < begin_date ";
	$where = ($wa ? (' WHERE ' . implode(' AND ', $wa)) : '');
	$sql = "SELECT count(user_id) AS cnt, date_format(begin_date, '%Y-%m-%d') AS d, DATEDIFF(last_login_date, begin_date) + 1 AS t FROM d_event_duration {$where} GROUP BY d, t ORDER BY d, t";
	$arr = db_select($admin_con, $sql);
	
	if (! $arr) {
		return;
	}
	
	// 内容の前に最初に空の表を作成
	$logs = array(
		/*
		date => array(
			-1 => n, 
			0 => n, 
			...
		), 
		*/
	);
	
	$r = array_shift($arr); $begin_date = $r['d']; array_unshift($arr, $r);
	$r = array_pop($arr);   $end_date   = $r['d']; array_push($arr, $r);
	
	$bt = strtotime($begin_date) + 43200;  // うるう秒 対策に12時を起点としてしまう
	$et = strtotime($end_date);
	for ($t = $bt;$t <= $et;$t += 86400) {
		$dt = date('Y-m-d', $t);
		$a = array();
		foreach ($stat_daycount_list As $v) {
			$a[$v] = 0;
		}
		$logs[$dt] = $a;
	}
	
	// 元データを生成
//var_dump($arr);
	foreach ($arr AS $rec) {
		if ($rec['d']) {
			if (is_null($rec['t'])) {
				// $rec['t'] が NULL の場合はログインが無いと想定
				$logs[$rec['d']][0] = $rec['cnt'];
			}
			elseif (in_array($rec['t'], $stat_daycount_list)) {
				// 対象日リストにある場合
				$logs[$rec['d']][$rec['t']] += $rec['cnt'];
			}
			else {
				// １５～、とかのあたりの範囲で集計するところにいる人
				$t = 0;
				foreach ($stat_daycount_list AS $k) {
					if ($rec['t'] < $k) {
						break;
					}
					$t = $k;
				}
				$logs[$rec['d']][$t] += $rec['cnt'];
			}
		}
		else {
			// 会員登録日の無いレコードは無視
			
		}
	}
	
	// 各行の合計
	foreach ($logs AS $d => $rec) {
		$s = 0;
		foreach ($rec As $k => $v) {
			$s += $v;
		}
		$logs[$d]['new'] = $s;
	}
	
	// 脱落式に表を直す
	foreach ($logs AS $date => $log) {
		for ($i = 1;$i < sizeof($stat_daycount_list);$i++) {
			for ($j = ($i+1);$j < sizeof($stat_daycount_list);$j++) {
				$logs[$date][$stat_daycount_list[$i]] += $logs[$date][$stat_daycount_list[$j]];
			}
		}
	}
	
	// ログイン数を出す
	$tpl = "SELECT date_format(log_date, '%%Y-%%m-%%d') AS d, login_count AS c FROM l_regist WHERE '%s' <= log_date AND log_date <= '%s' ORDER BY log_date";
	$sql = sprintf($tpl, date('Y-m-d', $bt), date('Y-m-d', $et));
	$arr = db_select($admin_con, $sql);
	if ($arr) {
		$l = array();
		foreach ($arr AS $rec) {
			$l[$rec['d']] = $rec['c'];
		}
		foreach ($logs AS $d => $a) {
			$logs[$d]['login'] = (isset($l[$d]) ? $l[$d] : 0);
		}
	}
	
	// イベント参加数を出す
	$tpl = "SELECT date_format(log_date, '%%Y-%%m-%%d') AS d, user_count AS c FROM l_event_usercount WHERE event_id = %d ORDER BY log_date";
	$sql = sprintf($tpl, $event_id);
	$arr = db_select($admin_con, $sql);
	if ($arr) {
		$j = array();
		foreach ($arr AS $rec) {
			$j[$rec['d']] = $rec['c'];
		}
		foreach ($logs AS $d => $a) {
			$logs[$d]['sum'] = (isset($j[$d]) ? $j[$d] : 0);
			$logs[$d]['rate'] = 0;
			if ($logs[$d]['login']  > 0) {
				$logs[$d]['rate'] = sprintf('%2.2f %%', ($logs[$d]['sum'] / $logs[$d]['login'] * 100));
			}
		}
	}
	
	// 継続数をテンプレートに代入
	$psmarty->assign('logs', array_reverse($logs));
	
	// 割合一覧も出す
	$ratios = array();
	foreach ($logs AS $d => $rec) {
		$a = array();
		foreach ($stat_daycount_list AS $k) {
			$a[$k] = ($logs[$d]['new'] > 0 ? $logs[$d][$k] / $logs[$d]['new'] : 0) * 100;
		}
		$ratios[$d] = $a;
	}
	$psmarty->assign('ratios', $ratios);
?>
