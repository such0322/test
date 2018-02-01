<?PHP
//print_r($_SESSION);
//var_dump($_SESSION['__mylog']);
	
	require_once( 'env.php' );
	require_once( 'lib/common.php' );
	
	////////////////////////////////////////////////////////////////////////////
	// パーツのインクルード
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	
	$stat_daycount_list = array(
		0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 30, 60, 90
	);
	
	$from_table_list = array(
		'd' => 'd_duration', 
		'r' => 'd_regist_date', 
	);
	
	$graph_max_width = 300;	// グラフの最大横幅
	
	$where_cols = array(
		'regist_begin_date', 
		'regist_end_date', 
	);
	
	
	
	$where_vals = (isset($LOCAL_SESSION['where_vals']) ? $LOCAL_SESSION['where_vals'] : array(
		'regist_begin_date' => date('Y-m-d', mktime(0, 0, 0, date('m') - 2, date('d'), date('Y'))), 
		'regist_end_date'   => date('Y-m-d'), 
	));
	
	$table = (isset($from_table_list[$LOCAL_SESSION['table']]) ? $LOCAL_SESSION['table'] :'d');
	$from_table = $from_table_list[$table];
	
	////////////////////////////////////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// 検索条件の変更
	if (isset($_POST['f1_submit'])) {
		
		// 検索条件の確保
		$where_vals = array();
		foreach ($where_cols AS $col) {
			if (isset($_POST[$col])) {
				list($y, $m, $d) = explode('-', $_POST[$col]);
				if (checkdate($m, $d, $y)) {
					$where_vals[$col] = $_POST[$col];
				}
			}
		}
		$LOCAL_SESSION['where_vals'] = $where_vals;
		
		$LOCAL_SESSION['table'] = $_POST['table'];
		
		$LOCAL_SESSION['f1_search'] = 1;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、検索・抽出条件フォーム
	
	$psmarty->assign('where_vals', $where_vals);
	$psmarty->assign('table', $table);
	
	// 検索条件が指定されていない場合はここで終わらせる
	if (! isset($LOCAL_SESSION['f1_search'])) {
		return ;
	}
	$psmarty->assign('post_data', $post_data);
	$psmarty->assign('is_f1_search', 1);
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成
	$admin_con = admin_con();
	
	$sql = '';
	$wa = array();
	
	if ($from_table == 'd_regist_date' ) {
		$wa[] = " '0000-00-00 00:00:00' < regist_date ";
		
		if (isset($where_vals['regist_begin_date'])) {   $wa[] = "'{$where_vals['regist_begin_date']}' <= regist_date";}
		if (isset($where_vals['regist_end_date'])) {     $wa[] = "regist_date <= '{$where_vals['regist_end_date']}'";}
		$where = ($wa ? (' WHERE ' . implode(' AND ', $wa)) : '');
		
		$sql = "SELECT count(user_id) AS cnt, regist_date AS d, DATEDIFF(last_login_date, regist_date) + 1 AS t FROM d_regist_date {$where} GROUP BY d, t ORDER BY d, t";
	}
	else {
		$wa[] = " '0000-00-00 00:00:00' < launch_date ";
		
		if (isset($where_vals['regist_begin_date'])) {   $wa[] = "'{$where_vals['regist_begin_date']}' <= launch_date";}
		if (isset($where_vals['regist_end_date'])) {     $wa[] = "launch_date <= '{$where_vals['regist_end_date']}'";}
		$where = ($wa ? (' WHERE ' . implode(' AND ', $wa)) : '');
		
		$sql = "SELECT count(user_id) AS cnt, launch_date AS d, DATEDIFF(last_login_date, launch_date) + 1 AS t FROM d_duration {$where} GROUP BY d, t ORDER BY d, t";
	}
	
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
	
	$bt = strtotime($begin_date);
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
		$logs[$d]['sum'] = $s;
	}
	
	// 脱落式に表を直す
	foreach ($logs AS $date => $log) {
		for ($i = 1;$i < sizeof($stat_daycount_list);$i++) {
			for ($j = ($i+1);$j < sizeof($stat_daycount_list);$j++) {
				$logs[$date][$stat_daycount_list[$i]] += $logs[$date][$stat_daycount_list[$j]];
			}
		}
	}
	
	$psmarty->assign('logs', array_reverse($logs));
	
	// 割合一覧も出す
	$ratios = array();
	foreach ($logs AS $d => $rec) {
		$a = array();
		foreach ($stat_daycount_list AS $k) {
			$a[$k] = ($logs[$d]['sum'] > 0 ? $logs[$d][$k] / $logs[$d]['sum'] : 0) * 100;
		}
		$ratios[$d] = $a;
	}
	$psmarty->assign('ratios', $ratios);
?>
