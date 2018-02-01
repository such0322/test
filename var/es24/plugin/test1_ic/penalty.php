<?PHP
//print_r($_SESSION);
//var_dump($_SESSION['__mylog']);
	
	require_once( 'env.php' );
	require_once( 'lib/common.php' );
	
	////////////////////////////////////////////////////////////////////////////
	// パーツのインクルード
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	$admin_con = admin_con();
	
	$limit_list = array(10, 50, 100, 200, 500, 1000);
	$default_limit  = 100;
	$default_offset = 0;
	$default_order  = 'l_penalty_id';
	$default_display_cols = array('l_penalty_id', 'log_date', 'operator', 'note');
	
	$table_name = 'l_penalty';
	$select_columns = $tables[$table_name];
	// マスタデータ、キーの名称は $select_columns の type=enum の場合はそれに合わせる、それ以外はすきに
	$master = array(
	);
	
	$limit  = (intval($LOCAL_SESSION['limit']) > 0 ? intval($LOCAL_SESSION['limit']) : $default_limit);
	$offset = (intval($LOCAL_SESSION['offset']) >= 0 ? intval($LOCAL_SESSION['offset']) : $default_offset);
	$order  = (array_key_exists($LOCAL_SESSION['order'], $select_columns) ? $LOCAL_SESSION['order'] : $default_order);
	$desc   = ($LOCAL_SESSION['desc'] == 'DESC' ? 'DESC' : '');
	$LOCAL_SESSION['desc'] = $desc;  // セッション変数に null が入って warnning が出ることがあるので暫定処置
	$display_cols = (is_array($LOCAL_SESSION['display_cols'])?$LOCAL_SESSION['display_cols']:$default_display_cols);
	$post_data = (isset($LOCAL_SESSION['post_data']) ? $LOCAL_SESSION['post_data'] : array('display_cols' => $display_cols));
	
	$f0_result_code = 0;
	$f0_error_detail = '';
	if (isset($_SESSION['f0_result_code'])) {
		$f0_result_code = $_SESSION['f0_result_code'];
		unset($_SESSION['f0_result_code']);
		$f0_error_detail = $_SESSION['f0_error_detail'];
		unset($_SESSION['f0_error_detail']);
	}
	
	////////////////////////////////////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// ペナルティの付与
	if (isset($_POST['f0_submit'])) {
		
		$f0_result_code = 0;
		$f0_error_detail = '';
		
		$player_id = $_POST['player_id'];
		$penalty_days = $_POST['penalty_days'];
		$note = $_POST['note'];
		
		// サービス環境DBの更新
		$master_con = master_con();
		
		try {
			// パラメータの確認
			
			// トランザクションの開始
			$res = db_exec($master_con, 'BEGIN');
			if (! $res) {
mylog(__LINE__);
				throw new Exception('', __LINE__);
			}
			
			// ペナルティ状況の更新
			$sql = sprintf("UPDATE player SET penalty = date_add(now(), INTERVAL %d DAY) WHERE id = %d ", $penalty_days, $player_id);
			$res = db_exec($master_con, $sql);
			if (! $res) {
mylog(__LINE__);
				throw new Exception('', __LINE__);
			}
			
			// 結果の取得
			$sql = sprintf("SELECT * FROM player WHERE id = %d ", $player_id);
			$arr = db_select($master_con, $sql);
			if (! $arr) {
mylog(__LINE__);
				throw new Exception('', __LINE__);
			}
			$player = $arr[0];
			
			// 操作ログの書き出し
			$tpl = "INSERT INTO l_penalty(log_date,operator,player_id,penalty_days,note) VALUES(now(), '%s', %d, %d, '%s')";
			$sql = sprintf($tpl, mysql_real_escape_string($_SESSION['session_login_account'], $admin_con)
			                   , mysql_real_escape_string($player_id, $admin_con)
			                   , mysql_real_escape_string($penalty_days, $admin_con)
			                   , mysql_real_escape_string($note, $admin_con)
			);
			$res = db_exec($admin_con, $sql);
			
			$f0_result_code = 1;
			
			// サービス用DBのコミット
			$res = db_exec($master_con, 'COMMIT');
			if (! $res) {
mylog(__LINE__);
				throw new Exception('', __LINE__);
			}
			
		} catch (Exception $e) {
			if ($admin_con) {
				db_exec($admin_con, 'ROLLBACK');
			}
			if ($master_con) {
				db_exec($master_con, 'ROLLBACK');
			}
		}
		
		$_SESSION['f0_result_code'] = $f0_result_code;
		$_SESSION['f0_error_detail'] = $f0_error_detail;
	}
	
	// 検索条件の変更
	if (isset($_POST['f1_post']) || isset($_GET['f1_submit'])) {
		$LOCAL_SESSION['post_data'] = (isset($_POST['f1_post']) ? $_POST : $_GET);
		
		$LOCAL_SESSION['offset'] = 0;
		$LOCAL_SESSION['f1_search'] = 1;
		
		// 表示対象カラムの変更
		$LOCAL_SESSION['display_cols'] = $LOCAL_SESSION['post_data']['display_cols'];
	}
	
	// オーダリミットオフセットの変更
	if ($_POST['f2_post']) {
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
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、検索・抽出条件フォーム
	
	$psmarty->assign('menukey', $menukey);
	
	$psmarty->assign('master', $master);
	$psmarty->assign('select_columns', $select_columns);
	$psmarty->assign('display_cols', $display_cols);
	
	$psmarty->assign('yesterday', strtotime('-1 day'));
	$psmarty->assign('tomorrow', strtotime('+1 day'));
	
	$psmarty->assign('f0_result_code', $f0_result_code);
	$psmarty->assign('f0_error_detail', $f0_error_detail);
	// 検索条件が指定されていない場合はここで終わらせる
//	if (! isset($LOCAL_SESSION['f1_search'])) {
//		return ;
//	}
	$psmarty->assign('post_data', $post_data);
	$psmarty->assign('is_f1_search', 1);
	
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
					$where_array[] = sprintf("%s = '%s'", $wcol, mysql_real_escape_string($v, $admin_con));
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
						$w[] = sprintf('%s IN (%s)', $wcol, implode(',', $vals));
					}
					if (sizeof($w) > 0) {
						$where_array[] = sprintf('(%s)', implode(' OR ', $w));
					}
				}
				break;
			case 'string' :
				if (strlen($post_data[$col]) > 0) {
					$where_array[] = sprintf("%s LIKE '%%%s%%'", $wcol, mysql_real_escape_string($post_data[$col], $admin_con));
				}
				break;
			case 'datetime' :
			case 'unixtime' :
				if (preg_match('/^20[0-9]{2}-(0?[0-9]|1[0-2])-([012]?[0-9]|3[01]) ([01]?[0-9]|2[0-3]):([01]?[0-9]|2[0-3]):([01]?[0-9]|2[0-3])$/', $post_data[$col]['begin'])) {
					if ($info['type'] == 'unixtime') {
						$where_array[] = sprintf('%d < %s', strtotime($post_data[$col]['begin']), $wcol);
					}
					else {
						$where_array[] = sprintf("'%s' < %s", $post_data[$col]['begin'], $wcol);
					}
				}
				if (preg_match('/^20[0-9]{2}-(0?[0-9]|1[0-2])-([012]?[0-9]|3[01]) ([01]?[0-9]|2[0-3]):([01]?[0-9]|2[0-3]):([01]?[0-9]|2[0-3])$/', $post_data[$col]['end'])) {
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
			
			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_name} ";
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
			$arr = db_select($admin_con, $sql);
			$ret['total'] = $arr[0]['cnt'];
			$ret['page'] = ($offset / $limit) + 1;
		}
		
		header('Content-type: application/json');
		echo json_encode($ret);
		
		exit;
	}
