<?PHP
//print_r($_SESSION);
//var_dump($_SESSION["__mylog"]);
	
	require_once( 'env.php' );
	require_once( "lib/common.php" );
	
	////////////////////////////////////////////////////////////////////////////
	// パーツのインクルード
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	$user_vars = user_vars_load();
	
	$select_size_max = 10;
	$stat_unit = (isset($LOCAL_SESSION['stat_unit']) ? $LOCAL_SESSION['stat_unit'] : 'd');
	$max_width = 300;
	
	$limit_list = array(10, 50, 100, 200, 500, 1000);
	$default_limit  = 100;
	$default_offset = 0;
	$default_order  = 'l_kakin_id';
	$default_display_cols = array('log_date', 'l_kakin_id', 'user_id', 'chara_id', 'item_id', 'item_num', 'subtotal', );
	$default_post_data = array(
/*
		'log_date' => array(
			'begin' => date('Y-m-01 00:00:00'), 
			'end'   => date('Y-m-d 00:00:00'), 
		), 
*/
		'display_cols' => $default_display_cols
	);
	
	$table_name = 'l_kakin';
	$ranking_key = 'item_id';
	$ranking_countl = 'item_num';
	$ranking_sum = 'subtotal';
	$stat_key = 'log_date';
	$stat_val = 'subtotal';
	$select_columns = $tables[$table_name];
	
	// マスタデータ、キーの名称は $select_columns の type=enum の場合はそれに合わせる、それ以外はすきに
	$master = $code_master['l_kakin'];
	
	
	
	$limit  = (intval($LOCAL_SESSION['limit']) > 0 ? intval($LOCAL_SESSION['limit']) : $default_limit);
	$offset = (intval($LOCAL_SESSION['offset']) >= 0 ? intval($LOCAL_SESSION['offset']) : $default_offset);
	$order  = (array_key_exists($LOCAL_SESSION['order'], $select_columns) ? $LOCAL_SESSION['order'] : $default_order);
	$desc   = ((isset($LOCAL_SESSION['desc']) && $LOCAL_SESSION['desc'] == 'asc') ? 'asc' : 'desc');
	$LOCAL_SESSION['desc'] = $desc;  // セッション変数に null が入って warnning が出ることがあるので暫定処置
	$display_cols = (is_array($LOCAL_SESSION['display_cols'])?$LOCAL_SESSION['display_cols']:$default_display_cols);
	
	$post_data = (isset($LOCAL_SESSION['post_data']) ? $LOCAL_SESSION['post_data'] : $default_post_data);
	
	////////////////////////////////////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// 検索条件の変更
	if (isset($_POST['f1_submit']) || isset($_GET['f1_submit'])) {
		$post_data = (isset($_POST['f1_submit']) ? $_POST : $_GET);
		$LOCAL_SESSION['post_data'] = $post_data;
		
		if (isset($stat_units[$post_data['stat_unit']])) {
			$LOCAL_SESSION['stat_unit'] = $post_data['stat_unit'];
		}
		
		// 表示対象カラムの変更
		$LOCAL_SESSION['display_cols'] = $post_data['display_cols'];
	}
	if (isset($_POST['f3_submit'])) {
		unset($LOCAL_SESSION['post_data']);
		unset($LOCAL_SESSION['stat_unit']);
		unset($LOCAL_SESSION['display_cols']);
	}
	
	// エクスポートの場合のみ一番したまで回す
	if (isset($_POST['export']) && $_POST['export'] == 1) {
		;
	}
	elseif (isset($_GET['json'])) {
	}
	elseif ($_POST) {
		return;
	}
	else {
		unset($_SESSION["__mylog"]);
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、検索・抽出条件フォーム
	
	$psmarty->assign('menukey', $menukey);
	
	$psmarty->assign('master', $master);
	$psmarty->assign('select_columns', $select_columns);
	$psmarty->assign('display_cols', $display_cols);
	
	$psmarty->assign('stat_units', $stat_units);
	$psmarty->assign('stat_unit', $stat_unit);
	
	$psmarty->assign('yesterday', strtotime('-1 day'));
	$psmarty->assign('tomorrow', strtotime('+1 day'));
	
	$psmarty->assign('post_data', $post_data);
	$psmarty->assign('display_cols', $display_cols);
	
	$psmarty->assign('desc', $desc);
	$psmarty->assign('order', $order);
	
	$psmarty->assign('is_grouped_item_id', (isset($user_vars['item_id_group']) and is_array($user_vars['item_id_group']) ? 1 : 0) );
	
	////////////////////////////////////////////////////////////////////////////
	// 検索条件の生成
	$main_con = admin_con();
	
	$where_array = array();
	foreach ($select_columns AS $col => $info) {
		$wcol = (isset($info['colname'])?$info['colname']:$col);
		switch ($info['type']) {
			case 'primary' :
			case 'key' :
				$v = preg_replace('/(,|\.|\s)/', '', $post_data[$col]);
				if (preg_match('/^[0-9]+$/', $v)) {
					$where_array[] = sprintf('%s = %s', $wcol, $v);
				}
				break;
			case 'strkey' :
				if (strlen($post_data[$col]) > 0) {
					$where_array[] = sprintf("%s = '%s'", $wcol, db_qs($main_con, $post_data[$col]));
				}
				break;
			case 'num' :
				$min = preg_replace('/(,|\.|\s)/', '', $post_data[$col]['min']);
				if (is_numeric($min)) {
					$where_array[] = sprintf('%d <= %s', $min, $wcol);
				}
				$max = preg_replace('/(,|\.|\s)/', '', $post_data[$col]['max']);
				if (is_numeric($max)) {
					$where_array[] = sprintf('%s <= %d', $wcol, $max);
				}
				break;
			case 'enum' :
				if (array_key_exists($col, $master) && is_array($post_data[$col])) {
					$vals = array();
					$w = array();
					foreach ($post_data[$col] AS $val) {
						if (strlen($val) == 0) {
							$w[] = sprintf('(%s IS NULL)', $wcol);
						}
						elseif (array_key_exists($val, $master[$col])) {
							$vals[] = $val;
						}
					}
					if (sizeof($vals) > 0) {
						$w[] = sprintf("%s IN ('%s')", $wcol, implode("','", $vals));
					}
					if (sizeof($w) > 0) {
						$where_array[] = sprintf('(%s)', implode(' OR ', $w));
					}
				}
				break;
			case 'string' :
				if (strlen($post_data[$col]) > 0) {
					$where_array[] = sprintf("%s LIKE '%%%s%%'", $wcol, db_qs($main_con, $post_data[$col]));
				}
				break;
			case 'datetime' :
			case 'unixtime' :
				if (preg_match('/^20[0-9]{2}-(0?[0-9]|1[0-2])-([012]?[0-9]|3[01]) ([01]?[0-9]|2[0-3]):([0-5]?[0-9]):([0-5]?[0-9])$/', $post_data[$col]['begin'])) {
					if ($info['type'] == 'unixtime') {
						$where_array[] = sprintf('%d <= %s', strtotime($post_data[$col]['begin']), $wcol);
					}
					else {
						$where_array[] = sprintf("'%s' <= %s", $post_data[$col]['begin'], $wcol);
					}
				}
				if (preg_match('/^20[0-9]{2}-(0?[0-9]|1[0-2])-([012]?[0-9]|3[01]) ([01]?[0-9]|2[0-3]):([0-5]?[0-9]):([0-5]?[0-9])$/', $post_data[$col]['end'])) {
					if ($info['type'] == 'unixtime') {
						$where_array[] = sprintf('%s <= %d', $wcol, strtotime($post_data[$col]['end']));
					}
					else {
						$where_array[] = sprintf("%s <= '%s'", $wcol, $post_data[$col]['end']);
					}
				}
				break;
			case 'subquery' :
				if (isset($post_data[$col]) and $post_data[$col]) {
					$where_array[] = $info['query'];
				}
				break;
			case 'checkbox' :
				break;
			default:
				break;
		}
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、CSV
	if (isset($_POST['export']) && $_POST['export'] == 1) {
		
		$results = array();
		
		// ヘッダ部分の作成
		$heads = array();
		foreach ($select_columns AS $col => $colinfo) {
			if (in_array($col, $display_cols)) {
				$heads[] = $colinfo['name'];
			}
		}
		$results[] = $heads;
		
		$sql = "SELECT * FROM {$table_name} ";
		if (sizeof($where_array) > 0) {
			$sql .= ' WHERE ' . implode(' AND ', $where_array);
		}
		$sql .= sprintf(" ORDER BY %s %s", $order, $desc);
		
		// 出力
		header("Content-Type: application/octet-stream");
//		header("Content-Type: application/x-csv");
		header("Content-Disposition: attachment; filename=kakin.csv");
		header("Cache-Control: public");
		header("Pragma: public");
		
		$res = db_exec($main_con, $sql);
		if ($res) {
			while ($rec = db_fetch($res)) {
				$line = array();
				foreach ($select_columns AS $col => $colinfo) {
					if (in_array($col, $display_cols)) {
						if (isset($master[$col])) {
							$line[] = sprintf('%s : %s', $rec[$col], (is_array($master[$col][$rec[$col]]) ? $master[$col][$rec[$col]]['name'] : $master[$col][$rec[$col]]));
						}
						elseif ($colinfo["type"] == 'num') {
							$line[] = intval($rec[$col]);
						}
						else {
							$line[] = (is_null($rec[$col]) ? '' : $rec[$col]);
						}
					}
				}
				
				print csv_encode('"' . implode('","', $line) . '"');
				print "\r\n";
			}
		}
		
		exit;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、json
	if (isset($_GET['json'])) {
		$ret = array(
			'rows' => array(), 
			'total' => 1, 
			'page' => 1, 
		);
		
		$type = $_GET['type'];
		if ($type == 'history') {
			
			// オーダ、リミット、オフセットを用意
			
			if (in_array($_POST['rp'], $limit_list)) {
				$limit = $_POST['rp'];
			}
			if (is_numeric($_POST['page'])) {
				$offset = $limit * ($_POST['page'] - 1);
			}
			if (isset($select_columns[$_POST['sortname']])) {
				$order = $_POST['sortname'];
			}
			if ($_POST['sortorder'] == 'asc' || $_POST['sortorder'] == 'desc') {
				$desc = $_POST['sortorder'];
			}
			
			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_name} ";
			if (sizeof($where_array) > 0) {
				$sql .= ' WHERE ' . implode(' AND ', $where_array);
			}
			$sql .= sprintf(" ORDER BY %s %s LIMIT %d,%d", $order, $desc, $offset, $limit);
			
			$arr = db_select($main_con, $sql);
//mylog($_POST);mylog($sql);mylog($arr);
			$tr_cnt = 1;
			
			$f2_history_trs = '';
			if (sizeof($arr) > 0) {
				foreach ($arr AS $rec) {
					
					$a = array();
					foreach ($select_columns AS $col => $colinfo) {
						if (isset($master[$col]) && isset($master[$col][$rec[$col]])) {
							$a[] = sprintf('%s : %s', $rec[$col], (is_array($master[$col][$rec[$col]]) ? $master[$col][$rec[$col]]['name'] : $master[$col][$rec[$col]]));
						}
						else {
							$a[] = (is_null($rec[$col]) ? '' : $rec[$col]);
						}
					}
					$ret['rows'][] = array('cell' => $a);
				}
			}
			
			$sql = "SELECT FOUND_ROWS() As cnt;";
			$arr = db_select($main_con, $sql);
			$ret['total'] = $arr[0]['cnt'];
			$ret['page'] = ($offset / $limit) + 1;
		}
		
		////////////////////////////////////////////////////////
		// ここからランキング
		
		if ($type == 'ranking') {
			$sql = "SELECT {$ranking_key} AS k, sum({$ranking_countl}) AS kazu, sum({$ranking_sum}) AS gaku FROM {$table_name} ";
			if (sizeof($where_array) > 0) {
				$sql .= ' WHERE ' . implode(' AND ', $where_array);
			}
			$sql .= ' GROUP BY k ORDER BY gaku DESC';
			$arr = db_select($main_con, $sql);
//var_dump($sql);
			
			if (sizeof($arr) > 0) {
				
				$ret['total'] = sizeof($arr);
				
				foreach ($arr AS $rec) {
					$a = array(
						'cell' => array(
							"{$rec['k']}:{$master[$ranking_key][$rec['k']]}", 
							$rec['kazu'], 
							$rec['gaku'], 
						), 
					);
					$ret['rows'][] = $a;
				}
			}
		}
		
		if ($type == 'granking') {
			if (isset($user_vars['item_id_group']) and is_array($user_vars['item_id_group']) and sizeof($user_vars['item_id_group']) < 50) {
				
				
				$granking = array(
					/*
						key => array(kazu => n, gaku => n), ...
					*/
				);
				
				foreach ($user_vars['item_id_group'] as $k => $v) {
					if ($v) {
						$sql = "SELECT sum({$ranking_countl}) AS kazu, sum({$ranking_sum}) AS gaku FROM {$table_name} WHERE ";
						
						// str_getcsv() が使えない環境なので簡易実装
						$a = preg_split('/\"?,\"?/', trim($v, '"'));
						if (sizeof($a) > 1) {
							// 複数要素なのでリストで検索
							$qa = array();
							foreach ($a as $b) {
								$qs[] = "'" . db_qs($main_con, $b) . "'";
							}
							$sql .= sprintf(" %s IN (%s)", $ranking_key, implode(',', $qa));
						} else {
							// 単一要素なので前方一致として検索
							$q = db_qs($main_con, $a[0]);
							$q = str_replace('\\%', '%', $q);
							$q = str_replace('\\_', '_', $q);
							$sql .= sprintf(" %s LIKE '%s%%'", $ranking_key, $q);
						}
						
						if (sizeof($where_array) > 0) {
							$sql .= ' AND ' . implode(' AND ', $where_array);
						}
						
						$arr = db_select($main_con, $sql);
						if ($arr) {
							$granking[$k] = $arr[0];
						}
					}
				}
				
				// ソート
				function my_granking_sort($a,$b) {
					return (intval($a['gaku']) < intval($b['gaku']) ? 1 : -1);
				}
				uasort($granking, 'my_granking_sort');
				
				foreach ($granking as $k => $rec) {
					$a = array(
						'cell' => array(
							"$k", 
							intval($rec['kazu']), 
							intval($rec['gaku']), 
						), 
					);
					$ret['rows'][] = $a;
				}
			}
		}
		
		////////////////////////////////////////////////////////
		// ここから時間統計
		
		if ($type == 'stat') {
			$sql = "SELECT date_format({$stat_key}, '{$stat_units[$stat_unit]['df']}') AS d, count({$stat_val}) AS kazu, sum({$stat_val}) AS gaku FROM {$table_name} ";
			if (sizeof($where_array) > 0) {
				$sql .= ' WHERE ' . implode(' AND ', $where_array);
			}
			$sql .= ' GROUP BY d ORDER BY d';
			$arr = db_select($main_con, $sql);
			
			if ($arr) {
				$last_ts = strtotime(isset($where_values['begin_date']) ? $where_values['begin_date'] : $arr[0]['d']);
				
				foreach ($arr AS $rec) {
					
					// ここでログのなかった時間帯の処理
					$ts = strtotime($rec['d']);
					while (strtotime($stat_units[$stat_unit]['range'], $last_ts) < $ts) {
						
						$last_ts = strtotime($stat_units[$stat_unit]['range'], $last_ts);
						
						$d = date('Y-m-d', $last_ts);
						$d2 = date('Y-m-d', strtotime($stat_units[$stat_unit]['range'] . " -1 day", $last_ts) );
						
						$a = array(
							'cell' => array(
								($d == $d2 ? $d : "{$d} ～ {$d2}"), 
								0, 
								0, 
							), 
						);
						
						$ret['rows'][] = $a;
					}
					$last_ts = $ts;
					
					$d = date('Y-m-d', $last_ts);
					$d2 = date('Y-m-d', strtotime($stat_units[$stat_unit]['range'] . " -1 day", $last_ts) );
					
					// 表示内容作成
					$a = array(
						'cell' => array(
							($d == $d2 ? $d : "{$d} ～ {$d2}"), 
							number_format($rec['kazu']), 
							number_format($rec['gaku']), 
						), 
					);
					$ret['rows'][] = $a;
				}
			}
		}
		
		////////////////////////////////////////////////////////
		// ここから課金履歴用エクステンション
		
		if ($type == 'ext') {
			
			// 購入者数
			$query = "SELECT count(distinct user_id) AS c FROM {$table_name}";
			if (sizeof($where_array) > 0) {
				$query .= " WHERE " . implode(" AND ", $where_array);
			}
			$ret = db_select($main_con, $query);
			$buy_user_count = $ret[0]['c'];
			
			// 総額および件数
			$query = "SELECT sum(subtotal) AS s, count(*) AS c FROM {$table_name}";
			if (sizeof($where_array) > 0) {
				$query .= " WHERE " . implode(" AND ", $where_array);
			}
			$ret = db_select($main_con, $query);
			$sales = $ret[0]['s'];
			$salecount = $ret[0]['c'];
			
			// 客単価
			$avg_user_buy = sprintf('%8.1f', ($buy_user_count > 0 ? ($ret[0]['s'] / $buy_user_count) : 0));
			
			
			$ret = array(
				'buy_user_count' => number_format($buy_user_count), 
				'sales'          => number_format($sales), 
				'salecount'      => number_format($salecount), 
				'avg_user_buy'   => $avg_user_buy, 
			);
		}
		
//		header('Content-type: application/json');
		echo json_encode($ret);
		
		exit;
	}
	
	
	////////////////////////////////////
	// 課金履歴用エクステンション
	
	$colmodels = array();
	foreach ($select_columns AS $col => $colinfo) {
		if (in_array($col, $display_cols)) {
			$colmodels[$col] = $colinfo['name'];
		}
	}
	
	// 購入者数
	$query = "SELECT count(distinct user_id) AS c FROM {$table_name}";
	if (sizeof($where_array) > 0) {
		$query .= " WHERE " . implode(" AND ", $where_array);
	}
	$ret = db_select($main_con, $query);
	$buy_user_count = $ret[0]['c'];
	$psmarty->assign('buy_user_count', number_format($buy_user_count));
	
	// 総額および件数
	$query = "SELECT sum(subtotal) AS s, count(*) AS c FROM {$table_name}";
	if (sizeof($where_array) > 0) {
		$query .= " WHERE " . implode(" AND ", $where_array);
	}
	$ret = db_select($main_con, $query);
	$sales = $ret[0]['s'];
	$salecount = $ret[0]['c'];
	$psmarty->assign('sales', number_format($sales));
	$psmarty->assign('salecount', number_format($salecount));
	
	// 客単価
	$psmarty->assign('avg_user_buy', sprintf('%8.1f', ($buy_user_count > 0 ? ($sales / $buy_user_count) : 0)));
	
?>
