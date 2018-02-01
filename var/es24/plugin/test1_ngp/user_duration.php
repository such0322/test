<?PHP
//print_r($_SESSION);
//var_dump($_SESSION['__mylog']);
	
	require_once( 'env.php' );
	require_once( 'lib/common.php' );
	
	////////////////////////////////////////////////////////////////////////////
	// パーツのインクルード
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	$con = admin_con();
	
	// グラフの最大横幅
	$graph_max_width = 300;
	
	$stat_cols = array(
		'regist_date'     => 'regist_date'    , 
		'last_login_date' => 'last_login_date', 
		'unregist_date'   => 'unregist_date'  , 
		'play_term'       => 'DATEDIFF(last_login_date, regist_date)', 
	);
	$stat_col = (isset($stat_cols[$LOCAL_SESSION['stat_col']]) ? $stat_cols[$LOCAL_SESSION['stat_col']] : 'regist_date');
	
	$where_cols = array(
		'regist_begin_date', 
		'regist_end_date', 
		'login_begin_date', 
		'login_end_date', 
		'unregist_begin_date', 
		'unregist_end_date', 
	);
	
	$where_vals = (isset($LOCAL_SESSION['where_vals']) ? $LOCAL_SESSION['where_vals'] : array());
	
	////////////////////////////////////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// 検索条件の変更
	if (isset($_POST['f1_post'])) {
		
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
		
		// 集計用項目の設定
		if (isset($stat_cols[$_POST['stat_col']])) {
			$LOCAL_SESSION['stat_col'] = $_POST['stat_col'];
		}
		
		$LOCAL_SESSION['f1_search'] = 1;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、検索・抽出条件フォーム
	
	$psmarty->assign('stat_col', $LOCAL_SESSION['stat_col']);
	$psmarty->assign('where_vals', $where_vals);
	
	// 検索条件が指定されていない場合はここで終わらせる
	if (! isset($LOCAL_SESSION['f1_search'])) {
		return ;
	}
	$psmarty->assign('post_data', $post_data);
	$psmarty->assign('is_f1_search', 1);
	
	////////////////////////////////////////////////////////////////////////////
	// 検索条件の生成
	$admin_con = admin_con();
	
	$wa = array();
	if (isset($where_vals['regist_begin_date'])) {   $wa[] = "'{$where_vals['regist_begin_date']}' <= regist_date";}
	if (isset($where_vals['regist_end_date'])) {     $wa[] = "regist_date <= '{$where_vals['regist_end_date']}'";}
	if (isset($where_vals['login_begin_date'])) {    $wa[] = "'{$where_vals['login_begin_date']}' <= last_login_date";}
	if (isset($where_vals['login_end_date'])) {      $wa[] = "last_login_date <= '{$where_vals['login_end_date']}'";}
	if (isset($where_vals['unregist_begin_date'])) { $wa[] = "'{$where_vals['unregist_begin_date']}' <= unregist_date";}
	if (isset($where_vals['unregist_end_date'])) {   $wa[] = "unregist_date <= '{$where_vals['unregist_end_date']}'";}
	$where = ($wa ? (' WHERE ' . implode(' AND ', $wa)) : '');
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成
	
	
	// ログの生成
	$logs = array();
	$count_max = 1;
	$count_sum = 0;
	//$arr_count = array();
	//$arr_date = array();
	//$gs = array();
	$sql = "SELECT count(user_id) AS cnt, {$stat_col} AS d FROM d_regist_date {$where} GROUP BY d ORDER BY d";
	$arr = db_select($admin_con, $sql);
	foreach ($arr AS $rec) {
		$count_sum += $rec['cnt'];
		if ($count_max < $rec['cnt']) {
			$count_max = $rec['cnt'];
		}
	}
	$rate = $graph_max_width / $count_max;
	foreach ($arr AS $rec) {
		$logs[] = array(
			'date'  => $rec['d'], 
			'count' => $rec['cnt'], 
			'ratio' => sprintf('%2.2f', ($rec['cnt'] * 100 / $count_sum)), 
			'width' => intval($rec['cnt'] * $rate), 
		);
		
		//$arr_count[] = intval($rec['cnt']);
		//$arr_date[] = $rec['d'];
		//$gs[] = array($rec['d'], intval($rec['cnt']));
	}
	
	$psmarty->assign('logs', $logs);
	
	//$psmarty->assign('gs', json_encode($gs));
	//$graph_src = json_encode(array(array_reverse($arr_count)));
	//$psmarty->assign('graph_src', $graph_src);
	//$graph_tickers = json_encode(array_reverse($arr_date));
	//$psmarty->assign('graph_tickers', $graph_tickers);
?>
