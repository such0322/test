<?PHP
//print_r($_SESSION);
//var_dump($_SESSION['__mylog']);
	
	require_once( 'env.php' );
	require_once( 'lib/common.php' );
	
	////////////////////////////////////////////////////////////////////////////
	// パーツのインクルード
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	$select_size_max = 10;
	
	$limit_list = array(10, 50, 100, 200, 500, 1000);
	$default_limit  = 100;
	$default_offset = 0;
	$default_order  = 'l_chat_log_id';
	$default_display_cols = array('l_chat_log_id', 'log_date', 'user_id', 'chara_id', 'server_id', 'message', 'chat_type');
	
	$table_name = 'l_chat_log';
	$select_columns = $tables[$table_name];
	// マスタデータ、キーの名称は $select_columns の type=enum の場合はそれに合わせる、それ以外はすきに
	$master = $code_master[$table_name];
	
	$limit  = (intval($LOCAL_SESSION['limit']) > 0 ? intval($LOCAL_SESSION['limit']) : $default_limit);
	$offset = (intval($LOCAL_SESSION['offset']) >= 0 ? intval($LOCAL_SESSION['offset']) : $default_offset);
	$order  = (array_key_exists($LOCAL_SESSION['order'], $select_columns) ? $LOCAL_SESSION['order'] : $default_order);
	$desc   = ((isset($LOCAL_SESSION['desc']) && $LOCAL_SESSION['desc'] == '') ? 'asc' : 'desc');
	$LOCAL_SESSION['desc'] = $desc;  // セッション変数に null が入って warnning が出ることがあるので暫定処置
	$display_cols = (is_array($LOCAL_SESSION['display_cols'])?$LOCAL_SESSION['display_cols']:$default_display_cols);
	
	$post_data = (isset($LOCAL_SESSION['post_data']) ? $LOCAL_SESSION['post_data'] : array('display_cols' => $display_cols));
	
	////////////////////////////////////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// 検索条件の変更
	if (isset($_POST['f1_submit']) || isset($_GET['f1_submit'])) {
		$post_data = (isset($_POST['f1_submit']) ? $_POST : $_GET);
		$LOCAL_SESSION['post_data'] = $post_data;
		
		$LOCAL_SESSION['offset'] = 0;
		$LOCAL_SESSION['f1_search'] = 1;
		
		// 表示対象カラムの変更
		$LOCAL_SESSION['display_cols'] = $post_data['display_cols'];
	}
	
	// リセット
	if (isset($_POST['f3_submit'])) {
		unset($LOCAL_SESSION['post_data']);
		unset($LOCAL_SESSION['stat_unit']);
		unset($LOCAL_SESSION['display_cols']);
		
		return ;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、検索・抽出条件フォーム
	
	$psmarty->assign('menukey', $menukey);
	
	$psmarty->assign('master', $master);
	$psmarty->assign('select_columns', $select_columns);
	$psmarty->assign('display_cols', $display_cols);
	
	$psmarty->assign('yesterday', strtotime('-1 day'));
	$psmarty->assign('tomorrow', strtotime('+1 day'));
	
	$psmarty->assign('post_data', $post_data);
	$psmarty->assign('is_f1_search', 1);
	
	$psmarty->assign('desc', $desc);
	$psmarty->assign('order', $order);
	
	////////////////////////////////////////////////////////////////////////////
	// 検索条件の生成
	$admin_con = admin_con();
	
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
		
		$sql = "SELECT * FROM {$table_name} ";
		if (sizeof($where_array) > 0) {
			$sql .= ' WHERE ' . implode(' AND ', $where_array);
		}
		$sql .= sprintf(' ORDER BY %s %s', $order, $desc);
		
		// 出力
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename=chat.csv');
		
		$res = db_exec($admin_con, $sql);
		if ($res) {
			while ($rec = db_fetch($res)) {
				$line = array();
				foreach ($select_columns AS $col => $colinfo) {
					if (in_array($col, $display_cols)) {
						if (isset($master[$col])) {
							$line[] = sprintf('%s : %s', $rec[$col], $master[$col][$rec[$col]]);
						}
						elseif ($colinfo['type'] == 'num') {
							$line[] = intval($rec[$col]);
						}
						else {
							$line[] = $rec[$col];
						}
					}
				}
				
				print csv_encode('"' . implode('","', $line) . '"');
				print "\r\n";
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
			
			$sql = "SELECT SQL_CALC_FOUND_ROWS *, (SELECT chara_name FROM d_chara WHERE d_chara.chara_id = {$table_name}.chara_id LIMIT 1) as chara_name FROM {$table_name} ";
			if (sizeof($where_array) > 0) {
				$sql .= ' WHERE ' . implode(' AND ', $where_array);
			}
			$sql .= sprintf(" ORDER BY %s %s LIMIT %d,%d", $order, $desc, $offset, $limit);
			
			$arr = db_select($admin_con, $sql);
//mylog($_POST);mylog($sql);mylog($arr);
			$tr_cnt = 1;
			
			$f2_history_trs = '';
			if (sizeof($arr) > 0) {
				foreach ($arr AS $rec) {
					
					$a = array();
					foreach ($select_columns AS $col => $colinfo) {
						if (isset($master[$col]) && isset($master[$col][$rec[$col]])) {
							$a[] = sprintf('%s : %s', esc($rec[$col]), (is_array($master[$col][$rec[$col]]) ? $master[$col][$rec[$col]]['name'] : $master[$col][$rec[$col]]));
						}
						elseif ($colinfo['type'] == 'datetime') {
							
							//$dt = new DateTime($rec[$col]);
							//$dt->setTimezone(new DateTimeZone('Asia/Tokyo'));
							//$a[] = sprintf('<span class="notjst_%s">%s</span><span class="jst_%s" style="display:none">%s (JST)</span>', $col, $rec[$col], $col, $dt->format('Y-m-d H:i:s'));
							
							$a[] = sprintf('<span class="notjst_%s">%s</span><span class="jst_%s" style="display:none">%s (JST)</span>', $col, $rec[$col], $col, to_user_time($rec[$col]));
						}
						elseif ($col == 'chara_id') {
							$a[] = sprintf('%d : %s', $rec[$col], $rec['chara_name']);
						}
						else {
							$a[] = (is_null($rec[$col]) ? '' : esc($rec[$col]));
						}
					}
					$ret['rows'][] = array('cell' => $a);
				}
			}
			
			$sql = "SELECT FOUND_ROWS() As cnt;";
			$arr = db_select($admin_con, $sql);
			$ret['total'] = $arr[0]['cnt'];
			$ret['page'] = ($offset / $limit) + 1;
		}
		
		header('Content-type: application/json');
		echo json_encode($ret);
		
		exit;
	}
	
	
?>
