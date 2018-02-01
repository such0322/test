<?PHP
//print_r($_SESSION);
//var_dump($_SESSION['__mylog']);
	
	require_once( 'env.php' );
//	require_once( 'env/env.live.php' );
	require_once( 'lib/common.php' );
	
	////////////////////////////////////////////////////////////////////////////
	// パーツのインクルード
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	$con = admin_con();
	
	$order_list = array('step', 'count', 'total');
	$default_order = $order_list[0];
	
	// グラフの最大横幅
	$graph_max_width = 400;
	
	$user_vars = user_vars_load();
	$step_names = $user_vars['steps'];
	
	$begin_date = (isset($LOCAL_SESSION['begin_date']) ? $LOCAL_SESSION['begin_date'] : date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d") - 7, date("Y"))));
	$end_date   = (isset($LOCAL_SESSION['end_date'])   ? $LOCAL_SESSION['end_date']   : date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d") - 0, date("Y"))));
	$is_newchara = (isset($LOCAL_SESSION['is_newchara']) ? $LOCAL_SESSION['is_newchara'] : 0);
	
	$order = (in_array($LOCAL_SESSION['order'], $order_list) ? $LOCAL_SESSION['order'] : $default_order);
	$desc  = ($LOCAL_SESSION['desc'] == 'desc' ? 'desc' : 'asc');
	
	////////////////////////////////////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// 検索条件の変更
	if (isset($_POST['f1_submit'])) {
		
		unset($LOCAL_SESSION['begin_date'] );
		if (isset($_POST['begin_date'])) {
			list($y, $m, $d) = explode('-', $_POST['begin_date']);
			if (checkdate($m, $d, $y)) {
				$LOCAL_SESSION['begin_date'] = $_POST['begin_date'];
			}
		}
		unset($LOCAL_SESSION['end_date'] );
		if (isset($_POST['end_date'])) {
			list($y, $m, $d) = explode('-', $_POST['end_date']);
			if (checkdate($m, $d, $y)) {
				$LOCAL_SESSION['end_date'] = $_POST['end_date'];
			}
		}
		
		$LOCAL_SESSION['is_newchara'] = ($_POST['is_newchara'] ? 1 : 0);
		
		$LOCAL_SESSION['f1_search'] = 1;
	}
	if (isset($_POST['f2_submit'])) {
		if (in_array($_POST['order'], $order_list)) {
			$LOCAL_SESSION['order'] = $_POST['order'];
		}
		if (in_array($_POST['desc'], array('asc', 'desc'))) {
			$LOCAL_SESSION['desc'] = $_POST['desc'];
		}
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、検索・抽出条件フォーム
	
	$psmarty->assign('begin_date', $begin_date);
	$psmarty->assign('end_date',   $end_date);
	$psmarty->assign('is_newchara', $is_newchara);
	
	// 検索条件が指定されていない場合はここで終わらせる
	if (! isset($LOCAL_SESSION['f1_search'])) {
		return ;
	}
	$psmarty->assign('is_f1_search', 1);
	$psmarty->assign('order', $order);
	$psmarty->assign('desc', $desc);
	$psmarty->assign('is_newchara', $is_newchara);
	
	////////////////////////////////////////////////////////////////////////////
	// 検索条件の生成
	$admin_con = admin_con();
	
	$where_values = array();
	
	if ($begin_date) {
		list($y, $m, $d) = explode('-', $begin_date);
		if (checkdate($m, $d, $y)) {
			$where_values['begin_date'] = $begin_date;
		}
	}
	if ($end_date) {
		list($y, $m, $d) = explode('-', $end_date);
		if (checkdate($m, $d, $y)) {
			$where_values['end_date'] = $end_date;
		}
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成
	
	// ログの生成
	$logs = array();
	
	if ($is_newchara) {
		// 指定期間内新規キャラクターのみ
		
		// 積み上げ進行状況
		$where = '';
		if (sizeof($where_values) > 0) {
			$wa = array();
			if (isset($where_values['begin_date'])) {
				$wa[] = sprintf(" '%s' <= chara_create_date ", $where_values['begin_date']);
			}
			if (isset($where_values['end_date'])) {
				$wa[] = sprintf(" chara_create_date <= '%s' ", $where_values['end_date']);
			}
			$where = ' WHERE ' . implode(' AND ', $wa);
		}
		$sql = "SELECT step, count(*) AS t, count(begin_date) AS b, count(end_date) AS e, count(cancel_date) AS c FROM d_step_history {$where} GROUP BY step ORDER BY step";
		
		$arr = db_select($con, $sql);
		foreach ($arr AS $rec) {
			$logs[$rec['step']] = array(
				'step'      => $rec['step'], 
				'step_name' => $step_names[$rec['step']], 
				'total'     => $rec['t'], 
				'begin'     => $rec['b'], 
				'end'       => $rec['e'], 
				'cancel'    => $rec['c'], 
			);
		}
		
		// 現在の進捗を取得
		$sql = "SELECT step, count(*) AS cnt FROM d_steps {$where} GROUP BY step ORDER BY step";
		$arr = db_select($con, $sql);
		foreach ($arr AS $rec) {
			$logs[$rec['step']]['count'] = $rec['cnt'];
		}
	}
	else {
		// 対象期間に変動のあった進捗から出す
		
		// 積み上げ進行状況
		$cols = array(
			'begin_date' => 'begin', 
			'end_date' => 'end', 
			'cancel_date' => 'cancel', 
			'log_date' => 'total', 
		);
		foreach ($cols as $col => $name) {
			
			$sql = 'SELECT step,count(*) AS c FROM d_step_history ';
			if (sizeof($where_values) > 0) {
				$wa = array();
				if (isset($where_values['begin_date'])) {
					$wa[] = sprintf(" '%s' <= %s ", $where_values['begin_date'], $col);
				}
				if (isset($where_values['end_date'])) {
					$wa[] = sprintf(" %s <= '%s' ", $col, $where_values['end_date']);
				}
				$sql .= ' WHERE ' . implode(' AND ', $wa);
			}
			$sql .= ' GROUP BY step ORDER BY step';
			$arr = db_select($con, $sql);
			
			foreach ($arr AS $rec) {
				if (! isset($logs[$rec['step']])) {
					$logs[$rec['step']] = array(
						'step'      => $rec['step'], 
						'step_name' => $step_names[$rec['step']], 
						'total'     => 0, 
						'begin'     => 0, 
						'end'       => 0, 
						'cancel'    => 0, 
					);
				}
				$logs[$rec['step']][$name] = $rec['c'];
			}
		}
		
		// 現在の進捗を取得
		$where = '';
		if (sizeof($where_values) > 0) {
			$wa = array();
			if (isset($where_values['begin_date'])) {
				$wa[] = sprintf(" '%s' <= log_date ", $where_values['begin_date']);
			}
			if (isset($where_values['end_date'])) {
				$wa[] = sprintf(" log_date <= '%s' ", $where_values['end_date']);
			}
			$where = ' WHERE ' . implode(' AND ', $wa);
		}
		$sql = "SELECT step, count(*) AS cnt FROM d_steps {$where} GROUP BY step ORDER BY step";
		$arr = db_select($con, $sql);
//var_dump($sql);var_dump($arr);
		foreach ($arr AS $rec) {
			if (! isset($logs[$rec['step']])) {
				$logs[$rec['step']] = array(
					'step'      => $rec['step'], 
					'step_name' => $step_names[$rec['step']], 
					'total'     => 0, 
					'begin'     => 0, 
					'end'       => 0, 
					'cancel'    => 0, 
				);
			}
			$logs[$rec['step']]['count'] = $rec['cnt'];
		}
	}
	
	
	
	
	
	
/*
	// 積み上げ進行状況を出す
	$sql = "SELECT count(chara_id) AS cnt, step FROM d_step_history {$where} GROUP BY step ORDER BY step";
	$arr = db_select($con, $sql);
	$graph_tick = array();
	$graph_data = array();
	foreach ($arr AS $rec) {
		if ($rec['step']) {
			$logs[$rec['step']] = array(
				'step'      => $rec['step'], 
				'step_name' => $step_names[$rec['step']], 
				'total'     => $rec['cnt'], 
				'width_t'   => $rec['cnt'], 
			);
		}
	}
	
	// 進捗状況を出す
	$sql = "SELECT count(chara_id) AS cnt, step FROM d_steps {$where} GROUP BY step ORDER BY step";
	$arr = db_select($con, $sql);
	$graph_tick = array();
	$graph_data = array();
	foreach ($arr AS $rec) {
		if ($rec['step']) {
			if (isset($logs[$rec['step']])) {
				$logs[$rec['step']]['count'] = $rec['cnt'];
				$logs[$rec['step']]['width_c'] = $rec['cnt'];
				$logs[$rec['step']]['width_t'] -= $rec['cnt'];
			}
			else {
				$logs[$rec['step']] = array(
					'step'      => $rec['step'], 
					'step_name' => $step_names[$rec['step']], 
					'total'     => 0, 
					'width_t'   => 0, 
					'count'     => $rec['cnt'], 
					'width_c'   => $rec['cnt'], 
				);
			}
		}
	}
*/
	
	// 必要ならここでソート
	function mycmp($a,$b) {
		if ($GLOBALS['desc'] == 'desc') {
			return ($a[$GLOBALS['order']] < $b[$GLOBALS['order']] ? 1 : -1);
		}
		else {
			return ($a[$GLOBALS['order']] < $b[$GLOBALS['order']] ? -1 : 1);
		}
	}
	uasort($logs, mycmp);
	
	
	// グラフの幅を計算
	$max = 1;
	$max_be = 1;
	foreach ($logs As $k => $v) {
//		if ($max < ($v['total'] + $v['count'])) {
//			$max = ($v['total'] + $v['count']);
//		}
		if ($max < ($v['count'])) {
			$max = ($v['count']);
		}
		if ($max < ($v['total'])) {
			$max = ($v['total']);
		}
		if ($max < ($v['begin'])) {
			$max = ($v['begin']);
		}
		if ($max < ($v['end'])) {
			$max = ($v['end']);
		}
		
		if ($max_be < ($v['begin'] - $v['end'])) {
			$max_be = ($v['begin'] - $v['end']);
		}
	}
	$rate = $graph_max_width / $max;
	$rate_be = $graph_max_width / $max_be;
	foreach ($logs As $k => $v) {
		$width = array(
			'b'  => intval($v['begin'] * $rate), 
			'e'  => intval($v['end'] * $rate), 
			'c'  => intval($v['count'] * $rate), 
			't'  => intval($v['total'] * $rate), 
			'ct' => intval(($v['total'] - $v['count']) * $rate), 
			'be' => intval(($v['begin'] - $v['end']) * $rate_be), 
		);
		$logs[$k]['width'] = $width;
	}
	
	// テンプレートに入れる
	$psmarty->assign('logs', $logs);
