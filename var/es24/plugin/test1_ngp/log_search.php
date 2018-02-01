<?PHP
//print_r($_SESSION);
//var_dump($_SESSION['__mylog']);
	
	require_once( 'env.php' );
	require_once( 'lib/common.php' );
	
	////////////////////////////////////////////////////////////////////////////
	// パーツのインクルード
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	$env = env();
	
	$post_data = $LOCAL_SESSION['post_data'];
	$select_size_max = 5;
	
	$limit_list = array(10, 50, 100, 200, 500);
	$default_limit  = 100;
	$default_offset = 0;
	$default_display_cols = array('1', '2', '3', '4', '5', '6', '7', '8', '9', );
	
	
	// 設定ファイルの読み込み
	$loglist = log_search_conf();
	
	$LOCAL_SESSION['desc'] = $desc;  // セッション変数に null が入って warnning が出ることがあるので暫定処置
	$display_cols = (is_array($LOCAL_SESSION['display_cols'])?$LOCAL_SESSION['display_cols']:$default_display_cols);
	
	$current_log = (isset($LOCAL_SESSION['current_log']) ? $LOCAL_SESSION['current_log'] : '');
	$select_columns = (isset($loglist[$current_log]) ? $loglist[$current_log]['cols'] : array());
	$logdate = (preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $LOCAL_SESSION['logdate']) ? $LOCAL_SESSION['logdate'] : date('Y-m-d'));
	
	$limit  = (intval($LOCAL_SESSION['limit']) > 0 ? intval($LOCAL_SESSION['limit']) : $default_limit);
	$offset = (intval($LOCAL_SESSION['offset']) >= 0 ? intval($LOCAL_SESSION['offset']) : $default_offset);
	
	////////////////////////////////////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// ログ種別の選択
	if (isset($_POST['f0_submit'])) {
		$current_log = $_POST['current_log'];
		if (isset($loglist[$current_log])) {
			$LOCAL_SESSION['current_log'] = $current_log;
		}
	}
	
	// 検索条件の変更
	if (isset($_POST['f1_submit'])) {
		
		// 基本的に POST データは全て使うときにチェックすることになってしまった
		$LOCAL_SESSION['post_data'] = $_POST;
		
		$LOCAL_SESSION['f1_search'] = 1;
		
		// 表示対象カラムの変更
		$LOCAL_SESSION['display_cols'] = $_POST['display_cols'];
		
		// ログ対象日の設定
		$LOCAL_SESSION['logdate'] = $_POST['logdate'];
	}
	
	// オーダリミットオフセットの変更
	if ($_POST['f2_post'] == 1) {
		if (in_array($_POST['limit'], $limit_list)) {
			$LOCAL_SESSION['limit'] = intval($_POST['limit']);
		}
		if (intval($_POST['offset']) >= 0) {
			$LOCAL_SESSION['offset'] = intval($_POST['offset']);
		}
		if (array_key_exists($_POST['order'], $select_columns)) {
			$LOCAL_SESSION['order'] = $_POST['order'];
		}
		if (in_array($_POST['desc'], array('DESC', ''))) {
			$LOCAL_SESSION['desc'] = $_POST['desc'];
		}
	}
	
	if ($_POST) {
		return;
	}
	else {
		unset($_SESSION['__mylog']);
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、対象ログ選択フォーム
	$psmarty->assign('loglist', $loglist);
	
	// 対象ログを選んでなければここまで
	if (! $select_columns) {
		return;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、検索・抽出条件フォーム
	$psmarty->assign('current_log', $current_log);
	
	$psmarty->assign('menukey', $menukey);
	
	$psmarty->assign('select_columns', $select_columns);
	$psmarty->assign('display_cols', $display_cols);
	
	$psmarty->assign('logdate', $logdate);
	$psmarty->assign('logdir', $LOCAL_SESSION['logdir']);
	
	$psmarty->assign('env', $env);
	
	// 検索条件が指定されていない場合はここで終わらせる
	if (! isset($LOCAL_SESSION['f1_search'])) {
		return ;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、結果リスト
	$psmarty->assign('is_f1_search', 1);
	$psmarty->assign('post_data', $post_data);
	
	// とりあえず開く予定のファイル名リストを生成
	$log_files = array();
	
	// 読み込み対象ログファイルを確保
	if (preg_match('/^20[0-9]{2}\-[0-9]{1,2}\-[0-9]{1,2}$/', $logdate)) {
		list($y, $m, $d) = explode('-', $logdate);
		$log_filename_tpl = sprintf('%s/%s_%04d%02d%02d.log', $env['log_bak'], $loglist[$current_log]['prefix'], $y, $m, $d);
		$log_files = glob($log_filename_tpl);
	}
	$log_files = array_reverse($log_files);
	
	
	if (isset($_GET['json'])) {
		// ファイルを順に開いていく
//mylog($_GET);
		$ret = array(
			'rows' => array(), 
			'total' => 1, 
			'page' => 1, 
		);
		$all_logs = array();
		$log_count = 0;
		$row = 0;
		
		// リミット、オフセットを用意
		if (in_array($_GET['rp'], $limit_list)) {
			$limit = $_GET['rp'];
		}
		if (is_numeric($_GET['page'])) {
			$offset = $limit * ($_GET['page'] - 1);
			$ret['page'] = $_GET['page'];
		}
		
//var_dump($post_data);
		foreach ($log_files AS $fn) {
			if (file_exists($fn)) {
				
/*
				$a = array_reverse(explode("\n", file_get_contents($fn)));
var_dump($a);
				foreach ($a AS $rec) {
					$cols = explode("\t", $rec);
					
				}
*/
				
				// 
				$fp = fopen($fn, 'r');
				$logs = array();
				if ($fp) {
					while (! feof($fp)) {
						$rec = fgets($fp);
						
						if (! trim($rec)) {
							continue;
						}
						
						$records = explode("\t", trim($rec));
						$r = $records;
						
						$is_filter = 0;
						
						foreach ($select_columns AS $k => $info) {
							$col = $k;
							$val = array_shift($r);
							
							// CHAR(12) とかの場合は char にしたい
							list($t) = explode('(', $info['type']);
							$type = strtolower($t);
							
							$is_filter = 1;
							
//mylog(__LINE__);
							switch ($type) {
								case 'primary' :
								case 'key' :
									if (preg_match('/^[0-9]+$/', $post_data[$col])) {
										if ($val != $post_data[$col]) {
//mylog(__LINE__);
											break 2;
										}
									}
									break;
								case 'keystr' :
									if ($val != $post_data[$col]) {
//mylog(__LINE__);
										break 2;
									}
								case 'num' :
								case 'int' :
								case 'integer' :
									if (is_numeric($post_data[$col]['min'])) {
										if ($val < $post_data[$col]['min']) {
//mylog(__LINE__);
											break 2;
										}
									}
									if (is_numeric($post_data[$col]['max'])) {
										if ($val > $post_data[$col]['max']) {
//mylog(__LINE__);
											break 2;
										}
									}
									break;
								case 'enum' :
									if (is_array($post_data[$col]) && ! array_diff($post_data[$col], array_keys($masters[$info['master']]))) {
										if (! in_array($val, $post_data[$col])) {
//mylog(__LINE__);
											break 2;
										}
									}
									
									break;
								case 'char' :
								case 'varchar' :
								case 'text' :
								case 'string' :
									if (strlen($post_data[$col]) > 0) {
										if (strpos($post_data[$col], $val) === false) {
//mylog(__LINE__);
											break 2;
										}
									}
									break;
								case 'datetime' :
								case 'unixtime' :
									
									$begin_datetime = (preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/', $post_data[$col]['begin']) ? $post_data[$col]['begin'] : '');
									$end_datetime   = (preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/', $post_data[$col]['end'])   ? $post_data[$col]['end']   : '');
									
									// 日付時刻型が変なログ対応
									if (preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}-[0-9]{1,2}-[0-9]{1,2}$/', $val)) {
										$a = preg_split('/[ -]/', $val);
										$val = "{$a[0]}-{$a[1]}-{$a[2]} {$a[3]}:{$a[4]}:{$a[5]}";
									}
									
									if ($begin_datetime) {
										$ut = strtotime($begin_datetime);
										
										if ($type == 'unixtime') {
											if ($ut > $val) {
//mylog(__LINE__);
												break 2;
											}
										}
										else {
											if ($ut > strtotime($val)) {
//mylog(__LINE__);mylog($rec);mylog(sprintf('$ut > strtotime($val), ut:%d, val:%s, strtotime($val):%d', $ut, $val, strtotime($val)));
												break 2;
											}
										}
									}
									if ($end_datetime) {
										$ut = strtotime($end_datetime);
										
										if ($type == 'unixtime') {
											if ($ut < $val) {
//mylog(__LINE__);
												break 2;
											}
										}
										else {
											if ($ut < strtotime($val)) {
//mylog(__LINE__);
												break 2;
											}
										}
									}
									break;
								default:
//mylog(__LINE__);
									break;
							}
							
							$is_filter = 0;
						}
						
						// 検索条件によるフィルタを突破
						if ($is_filter == 0) {
							if (sizeof($logs) <= ($limit + $offset)) {
								array_unshift($records, '');
								$logs[] = $records;
							}
							$log_count++;
						}
					}
					fclose($fp);
				}
				
				// ファイルごとのログとして確保
				$all_logs = array_merge($all_logs, $logs);
			}
		}
		
		if (sizeof($all_logs) > 0) {
			// 全てのログを時刻でソート
			$res = usort($all_logs, create_function('$a,$b', 'return (strcmp($a[1], $b[1]) < 0 ? 1 : -1);'));
			
			//list($arr) = array_chunk($all_logs, $limit);
			
			// 全ログから表示対象レコードを抽出
			$arr = array();
			for ($i = $offset;$i < ($offset + $limit);$i++) {
				if (isset($all_logs[$i])) {
					$arr[] = $all_logs[$i];
				}
			}
			
			// 表示対象ログから表示項目のみ抜き出して出力対象に入れる
//mylog($display_cols);
			foreach ($arr AS $rec) {
//mylog($rec);
				
				$a = array();
				foreach ($select_columns AS $col => $colinfo) {
					if (in_array($col, $display_cols)) {
						if (isset($master[$col]) && isset($master[$col][$rec[$col]])) {
							$a[] = sprintf('%d : %s', $rec[$col], (is_array($master[$col][$rec[$col]]) ? $master[$col][$rec[$col]]['name'] : $master[$col][$rec[$col]]));
						}
						else {
							$a[] = (is_null($rec[$col]) ? '' : $rec[$col]);
						}
					}
				}
				$ret['rows'][] = array('cell' => $a);
			}
			
			$ret['total'] = $log_count;
		}
		
		// json形式で吐き出して終了
		$j = json_encode($ret);
//		header('Content-type: application/json');
//		header('Content-length: ' . strlen($j));
		echo $j;
		
		exit;
/*
*/
		$psmarty->assign('ret', $ret);
		
	}
	
?>
