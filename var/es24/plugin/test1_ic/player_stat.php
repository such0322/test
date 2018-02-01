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
	$select_size_max = 10;
	
	$limit_list = array(10, 50, 100, 200, 500, 1000);
	$default_limit  = 10;
	$default_offset = 0;
	
	// マスタデータ、キーの名称は $select_columns の type=enum の場合はそれに合わせる、それ以外はすきに
	$master = array();
	
	require_once('lib/mag_player_search_config.php');
	
	////////////////////////////////////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// 検索条件の変更
	if (isset($_POST['f1_submit']) || isset($_GET['f1_submit'])) {
		$post_data = (isset($_POST['f1_submit']) ? $_POST : $_GET);
		$LOCAL_SESSION['post_data'] = $post_data;
		
		$LOCAL_SESSION['offset'] = 0;
		$LOCAL_SESSION['f1_search'] = 1;
		
		// 表示対象カラムの変更
		$LOCAL_SESSION['stat_cols'] = $post_data['stat_cols'];
	}
	
	// リセット
	if (isset($_POST['f3_submit'])) {
		unset($LOCAL_SESSION['post_data']);
		unset($LOCAL_SESSION['stat_unit']);
		unset($LOCAL_SESSION['display_cols']);
		
		return ;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// セッション上に残ってる変数を展開
	
	$limit  = (intval($LOCAL_SESSION['limit']) > 0 ? intval($LOCAL_SESSION['limit']) : $default_limit);
	$offset = (intval($LOCAL_SESSION['offset']) >= 0 ? intval($LOCAL_SESSION['offset']) : $default_offset);
	$order  = (array_key_exists($LOCAL_SESSION['order'], $select_columns) ? $LOCAL_SESSION['order'] : $default_order);
	$desc   = ((isset($LOCAL_SESSION['desc']) && $LOCAL_SESSION['desc'] == '') ? 'asc' : 'desc');
	$LOCAL_SESSION['desc'] = $desc;  // セッション変数に null が入って warnning が出ることがあるので暫定処置
	$display_cols = (is_array($LOCAL_SESSION['display_cols'])?$LOCAL_SESSION['display_cols']:$default_display_cols);
	
	$post_data = (isset($LOCAL_SESSION['post_data']) ? $LOCAL_SESSION['post_data'] : array('display_cols' => $display_cols));
	
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、検索・抽出条件フォーム
	
	// flexgrid用のカラムIDを作成
	$cid = 0;
	foreach ($select_columns as $k => $v) {
		if (isset($stat_columns[$k])) {
			$select_columns[$k]['cid'] = $cid++;
		} else {
			$select_columns[$k]['cid'] = '';
		}
	}
	
	$psmarty->assign('menukey', $menukey);
	
	$psmarty->assign('master', $master);
	$psmarty->assign('select_columns', $select_columns);
	$psmarty->assign('stat_columns', $stat_columns);
	
	$psmarty->assign('yesterday', strtotime('-1 day'));
	$psmarty->assign('tomorrow', strtotime('+1 day'));
	
	$psmarty->assign('post_data', $post_data);
	$psmarty->assign('is_f1_search', 1);
	
	$psmarty->assign('desc', $desc);
	$psmarty->assign('order', $order);
	
	////////////////////////////////////////////////////////////////////////////
	// 検索条件の生成
	$admin_con = main_con();
	
	$where_array = array();
	foreach ($select_columns AS $col => $info) {
		$wcol = (isset($info['colname'])?$info['colname']:$col);
		switch ($info['type']) {
			case 'primary' :
			case 'key' :
				$v = preg_replace('/(,|\.|\s)/', '', "" . $post_data[$col] . "");
				if (preg_match('/^[0-9]+$/', $v)) {
					$where_array[] = sprintf('%s = %s', $wcol, $v);
				}
				break;
			case 'strkey' :
				if (strlen($post_data[$col]) > 0) {
					$where_array[] = sprintf("%s = '%s'", $wcol, db_qs($admin_con, $post_data[$col]));
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
					$where_array[] = sprintf("%s LIKE '%%%s%%'", $wcol, db_qs($admin_con, $post_data[$col]));
				}
				break;
			case 'datetime' :
			case 'unixtime' :
				if (preg_match('/^20[0-9]{2}-(0?[0-9]|1[0-2])-([012]?[0-9]|3[01]) ([01]?[0-9]|2[0-3]):([0-5]?[0-9]):([0-5]?[0-9])$/', $post_data[$col]['begin'])) {
					if ($info['type'] == 'unixtime') {
						$where_array[] = sprintf('%d < %s', strtotime($post_data[$col]['begin']), $wcol);
					}
					else {
						$where_array[] = sprintf("'%s' < %s", $post_data[$col]['begin'], $wcol);
					}
				}
				if (preg_match('/^20[0-9]{2}-(0?[0-9]|1[0-2])-([012]?[0-9]|3[01]) ([01]?[0-9]|2[0-3]):([0-5]?[0-9]):([0-5]?[0-9])$/', $post_data[$col]['end'])) {
					if ($info['type'] == 'unixtime') {
						$where_array[] = sprintf('%s < %d', $wcol, strtotime($post_data[$col]['end']));
					}
					else {
						$where_array[] = sprintf("%s < '%s'", $wcol, $post_data[$col]['end']);
					}
				}
				
				break;
			case 'checkbox' :
				
				break;
			default:
				break;
		}
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、結果リスト
	
	// CSV 出力
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
		
		$a = array();
		$colno = 1;
		$colname2colno = array();
		foreach ($stat_cols as $col) {
			if (isset($stat_columns[$col])) {
				$colname2colno[$col] = sprintf('col_%d', $colno++);
				$a[] = (isset($stat_columns[$col]['stat']) ? $stat_columns[$col]['stat'] : $col) . ' AS ' . $colname2colno[$col];
			}
		}
		$cols = implode(',', $a);
		$gcols = implode(',', $colname2colno);
		$sql = "SELECT {$cols}, count(*) as cnt FROM {$table_name} ";
		if (sizeof($where_array) > 0) {
			$sql .= ' WHERE ' . implode(' AND ', $where_array);
		}
		$sql .= sprintf(" GROUP BY {$gcols} ORDER BY %s %s", $order, $desc);
		
		// 出力
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename=item_move.csv');
		
		
		$arr = db_select($admin_con, $sql);
//mylog($_POST);mylog($sql);
		
		foreach ($stat_cols as $col) {
			if (isset($stat_columns[$col])) {
				$a[] = $stat_columns[$col]['name'];
			}
		}
		$a[] = '合計';
		print csv_encode('"' . implode('","', $a) . '"') . "\r\n";
		
		if (is_array($arr) and sizeof($arr) > 0) {
			foreach ($arr AS $rec) {
				$a = array();
				foreach ($select_columns AS $col => $colinfo) {
					if (in_array($col, $stat_cols)) {
						//$k = (isset($stat_columns[$col]['stat']) ? $stat_columns[$col]['stat'] : $col);
						$k = $colname2colno[$col];
						if (isset($master[$col]) && isset($master[$col][$rec[$col]])) {
							$a[] = sprintf('%s : %s', esc($rec[$k]), (is_array($master[$col][$rec[$k]]) ? $master[$col][$rec[$k]]['name'] : $master[$col][$rec[$k]]));
						} else {
							$a[] = esc($rec[$k]);
						}
					}
					else {
						$a[] = '';
					}
				}
				$a[] = $rec['cnt'];
				
				print csv_encode('"' . implode('","', $a) . '"') . "\r\n";
			}
		}
		
		exit;
	}
	
	////////////////////////////////////////////////////////
	// ここから履歴
	if (isset($_GET['json'])) {
		$ret = array(
			'rows' => array(), 
			'total' => 1, 
			'page' => 1, 
		);
		
		$type = $_GET['type'];
		if ($type == 'stat') {
			
			// 集計対象項目の確保
			$stat_cols = (is_array($LOCAL_SESSION['stat_cols']) ? $LOCAL_SESSION['stat_cols'] : array());
			if ($stat_cols) {
				$a = array();
				$colno = 1;
				$colname2colno = array();
				foreach ($stat_cols as $col) {
					if (isset($stat_columns[$col])) {
						$colname2colno[$col] = sprintf('col_%d', $colno++);
						$a[] = (isset($stat_columns[$col]['stat']) ? $stat_columns[$col]['stat'] : $col) . ' AS ' . $colname2colno[$col];
					}
				}
				$cols = implode(',', $a);
				$gcols = implode(',', $colname2colno);
				
				// オーダ、リミット、オフセットを用意
				$order = 'cnt';
				if (isset($select_columns[$_POST['sortname']])) {
					$order = $_POST['sortname'];
				}
				$desc = 'DESC';
				if ($_POST['sortorder'] == 'asc' || $_POST['sortorder'] == 'desc') {
					$desc = $_POST['sortorder'];
				}
				$limit = 0;
				if (in_array($_POST['rp'], $limit_list)) {
					$limit = $_POST['rp'];
				}
				
				$sql = "SELECT {$cols}, count(*) as cnt FROM {$table_name} ";
				if (sizeof($where_array) > 0) {
					$sql .= ' WHERE ' . implode(' AND ', $where_array);
				}
				$sql .= sprintf(" GROUP BY {$gcols} ORDER BY %s %s", $order, $desc);
				if ($limit > 0) {
					$sql .= " LIMIT {$limit}";
				}
				
				$arr = db_select($admin_con, $sql);
//mylog($_POST);mylog($sql);
//var_dump($sql, $arr, $colname2colno);
				
				if (is_array($arr) and sizeof($arr) > 0) {
					foreach ($arr AS $rec) {
						$a = array();
						foreach ($select_columns AS $col => $colinfo) {
							if (in_array($col, $stat_cols)) {
								
								//$k = (isset($stat_columns[$col]['stat']) ? $stat_columns[$col]['stat'] : $col);
								$k = $colname2colno[$col];
								
								if (isset($master[$col]) && isset($master[$col][$rec[$col]])) {
									$a[] = sprintf('%s : %s', esc($rec[$k]), (is_array($master[$col][$rec[$k]]) ? $master[$col][$rec[$k]]['name'] : $master[$col][$rec[$k]]));
								} else {
									$a[] = esc($rec[$k]);
								}
							}
							else {
								$a[] = '';
							}
						}
						$a[] = '';
						$a[] = $rec['cnt'];
						
						$ret['rows'][] = array('cell' => $a);
					}
				}
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($ret);
		
		exit;
	}
