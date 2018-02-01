<?PHP
//print_r($_SESSION);
//var_dump($_SESSION['__mylog']);
	
	require_once( 'env.php' );
	require_once( 'lib/common.php' );
	
	////////////////////////////////////////////////////////////////////////////
	// パーツのインクルード
	
	// 設定ファイルの読み込み
	$loglist = loglist_update(log_search_conf());
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	$where_values = (isset($LOCAL_SESSION['where_values']) ? $LOCAL_SESSION['where_values'] : array());
	
	$current_log = $LOCAL_SESSION['current_log'];
	$select_columns = (isset($loglist[$current_log]) ? $loglist[$current_log]['cols'] : array());
	
	$limit_list = array(10, 50, 100, 200, 500, 1000);
	$default_limit  = 100;
	$default_offset = 0;
	$default_order  = 'log_date';
	$default_display_cols = array('1','2','3','4','5','6','7','8','9','10',);
	
	$limit  = (intval($LOCAL_SESSION['limit']) > 0 ? intval($LOCAL_SESSION['limit']) : $default_limit);
	$offset = (intval($LOCAL_SESSION['offset']) >= 0 ? intval($LOCAL_SESSION['offset']) : $default_offset);
	$order  = (array_key_exists($LOCAL_SESSION['order'], $select_columns) ? $LOCAL_SESSION['order'] : $default_order);
	$desc   = ($LOCAL_SESSION['desc'] == 'DESC' ? 'DESC' : '');
	$LOCAL_SESSION['desc'] = $desc;  // セッション変数に null が入って warnning が出ることがあるので暫定処置
	$display_cols = (is_array($LOCAL_SESSION['display_cols'])?$LOCAL_SESSION['display_cols']:$default_display_cols);
	
	////////////////////////////////////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// 検索条件の変更
	if (isset($_POST['f1_submit']) || isset($_GET['f1_submit'])) {
		$post_data = (isset($_POST['f1_submit']) ? $_POST : $_GET);
		
		$where_values = array(
			'log_date' => array(
				'begin' => null, 
				'end' => null, 
			), 
			'app_type' => '', 
			'uid' => '', 
			'user_id' => 0, 
			'chara_id' => 0, 
		);
		
		if (preg_match('/^20[0-9]{2}-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/', $post_data['log_date']['begin'])) {
			$where_values['log_date']['begin'] = $post_data['log_date']['begin'];
		}
		if (preg_match('/^20[0-9]{2}-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/', $post_data['log_date']['end'])) {
			$where_values['log_date']['end'] = $post_data['log_date']['end'];
		}
		if (intval($post_data['app_type']) > 0) {
			$where_values['app_type'] = intval($post_data['app_type']);
		}
		$where_values['uid'] = "" . $post_data['uid'] . "";
		if (intval($post_data['user_id']) > 0) {
			$where_values['user_id'] = intval($post_data['user_id']);
		}
		if (intval($post_data['chara_id']) > 0) {
			$where_values['chara_id'] = intval($post_data['chara_id']);
		}
		
		$LOCAL_SESSION['where_values'] = $where_values;
	}
	
	// CSV 出力
	if (isset($_POST['export']) && $_POST['export'] == 1) {
		
		$target = $_POST['export_target'];
		$select_columns = $loglist[$target];
		if ($select_columns) {
			$results = array();
			
			// 出力
			header('Content-type: application/octet-stream');
			header("Content-Disposition: attachment; filename={$target}.csv");
			
			// ヘッダ部分の作成
			$heads = array();
			foreach ($select_columns['cols'] AS $col => $colinfo) {
				$heads[] = $colinfo['name'];
			}
			print '"' . implode('","', $heads) . '"';
			print "\r\n";
			
			$admin_con = admin_con();
			
			// 検索条件の作成
			$where_array = array();
			if (preg_match('/^20[0-9]{2}-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/', $where_values['log_date']['begin'])) {
				$where_array[] = sprintf("'%s' <= log_date", $where_values['log_date']['begin']);
			}
			if (preg_match('/^20[0-9]{2}-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/', $where_values['log_date']['end'])) {
				$where_array[] = sprintf("log_date <= '%s'", $where_values['log_date']['end']);
			}
			if (intval($where_values['app_type']) > 0) {
				$where_array[] = sprintf("app_type = %d", $where_values['app_type']);
			}
			if (strlen($where_values['uid']) > 0) {
				$where_array[] = sprintf("uid = '%s'", db_qs($admin_con, $where_values['uid']));
			}
			if (intval($where_values['user_id']) > 0) {
				$where_array[] = sprintf("user_id = %d", $where_values['user_id']);
			}
			if (intval($where_values['chara_id']) > 0) {
				$where_array[] = sprintf("chara_id = %d", $where_values['chara_id']);
			}
			
			// オーダ、リミット、オフセットを用意
			if (in_array($_GET['rp'], $limit_list)) {
				$limit = $_GET['rp'];
			}
			if (is_numeric($_GET['page'])) {
				$offset = $limit * ($_GET['page'] - 1);
			}
			
			$sql = "SELECT log FROM log_{$target} ";
			if (sizeof($where_array) > 0) {
				$sql .= ' WHERE ' . implode(' AND ', $where_array);
			}
			$sql .= sprintf(" ORDER BY %s %s", $order, $desc);
			
			$res = db_exec($admin_con, $sql);
			if ($res) {
				while ($rec = db_fetch($res)) {
					$l = implode(',', explode("\t", $rec['log']));
					print "{$l}\r\n";
				}
			}
		}
		exit;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、対象ログ選択フォーム
	$psmarty->assign('loglist', $loglist);
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、検索・抽出条件フォーム
	$psmarty->assign('current_log', $current_log);
	
	$psmarty->assign('menukey', $menukey);
	
	$psmarty->assign('select_columns', $select_columns);
	$psmarty->assign('display_cols', $display_cols);
	
	$psmarty->assign('yesterday', strtotime('-1 day'));
	$psmarty->assign('tomorrow', strtotime('+1 day'));
	
	$psmarty->assign('where_values', $where_values);
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、json
	if ($_GET['fu_submit'] == 1) {
		
		$ret = array(
			'result' => 'failed', 
			'uid' => '', // uid
			'usr' => '', // user_id
			'red' => '', // regist_date
			'lld' => '', // last_login_date
			'rmp' => '', // realmoney_payment
			'rmb' => '', // realmoney_buy
			'rmu' => '', // realmoney_use
			'rml' => '', // realmoney_lost
		);
		do {
			
			$con = admin_con();
			
			// まずは uid / user_id の有無を確認
			if (! $where_values['uid'] && ! $where_values['user_id']) {
				break;
			}
			$uid     = $where_values['uid'];
			$user_id = $where_values['user_id'];
			
			if ($where_values['uid']) {
				$uid     = $where_values['uid'];
				
				// uid から user_id を取得
				$sql = sprintf("SELECT unique_id, user_id FROM d_unique_id WHERE unique_id = '%s' ORDER BY d_unique_id_id DESC LIMIT 1", db_qs($con, $uid));
				$arr = db_select($con, $sql);
				if ($arr) {
					$ret['uid'] = $arr[0]['unique_id'];
					$ret['usr'] = $arr[0]['user_id'];
					
					if (! $user_id) {
						$user_id = $arr[0]['user_id'];
					}
				}
			}
			elseif ($where_values['user_id']) {
				$user_id = $where_values['user_id'];
				
				// user_id から uid を取得
				$sql = sprintf("SELECT unique_id, user_id FROM d_unique_id WHERE user_id = '%s' ORDER BY d_unique_id_id DESC LIMIT 1", db_qs($con, $user_id));
				$arr = db_select($con, $sql);
				if ($arr) {
					$ret['uid'] = $arr[0]['unique_id'];
					$ret['usr'] = $arr[0]['user_id'];
					
					if (! $uid) {
						$uid = $arr[0]['unique_id'];
					}
				}
			}
			else {
				break;
			}
			
			
			// 会員登録日とかを取得
			$sql = sprintf("SELECT regist_date, last_login_date FROM d_regist_date WHERE uid = '%s' OR user_id = '%s' ", db_qs($con, $uid), db_qs($con, $user_id));
			$arr = db_select($con, $sql);
			if ($arr) {
				$ret['red'] = $arr[0]['regist_date'];
				$ret['lld'] = $arr[0]['last_login_date'];
			}
			
			// 累計決済情報を取得
			$sql = sprintf("SELECT sum(trade_val) AS s FROM l_realmoney_payment WHERE uid = '%s' OR user_id = '%s' AND trade_type = 1", db_qs($con, $uid), db_qs($con, $user_id));
			$arr = db_select($con, $sql);
			if ($arr) {
				$ret['rmp'] = 0;
				foreach ($arr As $rec) {
					$ret['rmp'] = $rec['s'];
				}
			}
			
			// 累計課金通貨情報を取得
			if ($uid) {$sql = sprintf("SELECT trade_type, sum(trade_val) AS s FROM l_realmoney_trade WHERE uid = '%s' GROUP BY trade_type", db_qs($con, $uid));}
			else {     $sql = sprintf("SELECT trade_type, sum(trade_val) AS s FROM l_realmoney_trade WHERE user_id = '%s' GROUP BY trade_type", db_qs($con, $user_id));}
			$arr = db_select($con, $sql);
			if ($arr) {
				$ret['rmb'] = 0;
				$ret['rmu'] = 0;
				$ret['rml'] = 0;
				foreach ($arr As $rec) {
					if ($rec['trade_type'] == 1) {
						$ret['rmb'] = $rec['s'];
					}elseif ($rec['trade_type'] == 2) {
						$ret['rmu'] = $rec['s'];
					}elseif ($rec['trade_type'] == 3) {
						$ret['rml'] = $rec['s'];
					}
				}
			}
			
			$ret['uid'] = esc($ret['uid']);
			$ret['result'] = 'success';
			
		} while (0);
		
		
		header('Content-type: application/json');
		echo json_encode($ret);
		
		exit;
	}
	
	if (isset($_GET['json'])) {
		
		$current_log = $_GET['k'];
		$ret = array(
			'rows' => array(), 
			'total' => 1, 
			'page' => 1, 
		);
		
		if (isset($loglist[$current_log])) {
			
			$admin_con = admin_con();
			
			// 検索条件の作成
			$where_array = array();
			if (preg_match('/^20[0-9]{2}-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/', $where_values['log_date']['begin'])) {
				$where_array[] = sprintf("'%s' <= log_date", $where_values['log_date']['begin']);
			}
			if (preg_match('/^20[0-9]{2}-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/', $where_values['log_date']['end'])) {
				$where_array[] = sprintf("log_date <= '%s'", $where_values['log_date']['end']);
			}
			if (intval($where_values['app_type']) > 0) {
				$where_array[] = sprintf("app_type = %d", $where_values['app_type']);
			}
			if (strlen($where_values['uid']) > 0) {
				$where_array[] = sprintf("uid = '%s'", db_qs($admin_con, $where_values['uid']));
			}
			if (intval($where_values['user_id']) > 0) {
				$where_array[] = sprintf("user_id = %d", $where_values['user_id']);
			}
			if (intval($where_values['chara_id']) > 0) {
				$where_array[] = sprintf("chara_id = %d", $where_values['chara_id']);
			}
			
			// オーダ、リミット、オフセットを用意
			if (in_array($_GET['rp'], $limit_list)) {
				$limit = $_GET['rp'];
			}
			if (is_numeric($_GET['page'])) {
				$offset = $limit * ($_GET['page'] - 1);
			}
			if (isset($select_columns[$_GET['sortname']])) {
				$order = "col_" . $_GET['sortname'];
			}
			if ($_GET['sortorder'] == 'asc' || $_GET['sortorder'] == 'desc') {
				$desc = $_GET['sortorder'];
			}
			
			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM log_{$current_log} ";
			if (sizeof($where_array) > 0) {
				$sql .= ' WHERE ' . implode(' AND ', $where_array);
			}
			$sql .= sprintf(" ORDER BY %s %s LIMIT %d,%d", $order, $desc, $offset, $limit);
			
			$arr = db_select($admin_con, $sql);
//mylog($_POST);mylog($sql);mylog($arr);
			
			if (sizeof($arr) > 0) {
				foreach ($arr AS $rec) {
					
					$a = array($rec['log_id']);
					$b = explode("\t", $rec['log']);
					foreach ($b AS $k => $v) {
						$vv = (is_null($v) ? '' : $v);
						if (preg_match('/^20[0-9]{2}-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/', $v)) {
							$vv = sprintf('<span class="notjst_">%s</span><span class="jst_" style="display:none">%s (JST)</span>', $v, to_user_time($v));
						}
						$a[] = $vv;
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
	
/*
	////////////////////////////////////////////////////////
	// ここから履歴
//var_dump($where_array);
	$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM lf_{$current_log} ";
	if (sizeof($where_array) > 0) {
		$sql .= ' WHERE ' . implode(' AND ', $where_array);
	}
	$sql .= sprintf(' ORDER BY %s %s LIMIT %d,%d', $order, $desc, $offset, $limit);
	$arr = db_select($admin_con, $sql);
//var_dump($sql);var_dump($arr);
	
	$f2_tr = array();
	if (sizeof($arr) > 0) {
		foreach ($arr AS $rec) {
			$row = array();
			foreach ($select_columns AS $col => $colinfo) {
				if (in_array($col, $display_cols)) {
					if (isset($master[$col])) {
						$row[] = sprintf('%d : %s', $rec[$col], $master[$col][$rec[$col]]);
					}
					elseif ($colinfo['type'] == 'num') {
						$row[] = (is_null($rec[$col]) ? '' : $rec[$col]);
					}
					else {
						$row[] = (is_null($rec[$col]) ? '' : $rec[$col]);
					}
				}
			}
			$f2_tr[] = $row;
		}
	}
	$psmarty->assign('f2_tr', $f2_tr);
	
	// ヘッダ行の作成
	$f2_trh = array();
	foreach ($select_columns AS $col => $colinfo) {
		if (in_array($col, $display_cols)) {
			$f2_trh[$col] = $colinfo;
		}
	}
	$psmarty->assign('f2_trh', $f2_trh);
	
	////////////////////////////////////
	// 最大件数取得と現在のページ取得
	$paging = array(
		'offset' => $offset, 
		'limit'  => $limit, 
		'order'  => $order, 
		'desc'   => $desc, 
		
		'limit_list' => $limit_list, 
	);
	
	$query = 'SELECT FOUND_ROWS() As cnt;';
	$cur = intval($offset / $limit) + 1;
	$ret = db_select($admin_con, $query);
	$max = $ret[0]['cnt'];
	$maxpage = (($max - 1) / $limit) + 1;
	
	$paging['max']   = $max;
	$paging['begin'] = ($max==0?0:($offset + 1));
	$paging['end']   = (($offset+$limit)>$max?$max:($offset+$limit));
	
	// 各ページへのリンク作成
	$pages = array();
	if ($max > 0) {
		for ($i = ($cur>10?$cur-10:1);$i <= ($maxpage>($cur+10)?($cur+10):$maxpage);$i++) {
			if ($i == ($cur-10) && $i != 1) {
				$pages[(($i-1)*$limit)] = '...';
			}
			elseif ($i == ($cur+10) && $i != $maxpage) {
				$pages[(($i-1)*$limit)] = '...';
			}
			else {
				$pages[(($i-1)*$limit)] = $i;
			}
		}
	}
	$paging['pages'] = $pages;
	
	$psmarty->assign('paging', $paging);
*/
	/** loglist の中身の不足項目を追加 */
	function loglist_update($arg) {
		
		$loglist = $arg;
		$con = admin_con();
		$current_log = $LOCAL_SESSION['current_log'];
		
		// それっぽいテーブル一覧を取得
		$sql = "show table status like 'log\_%'";
		$arr = db_select($con, $sql);
		foreach ($arr AS $rec) {
			$prefix = substr($rec['Name'], 4);
			
			// ログ定義になければそれっぽく自動生成
			if (! isset($loglist[$prefix])) {
				
				$loglist[$prefix] = array(
					'name' => $prefix, 
					'prefix' => $prefix, 
					'cols' => array(
						'1' => array(
							'name' => '発生日時', 
							'type' => 'DATETIME', 
						), 
						'2' => array(
							'name' => 'プラットフォーム種別', 
							'type' => 'INTEGER', 
						), 
						'3' => array(
							'name' => 'UID', 
							'type' => 'VARCHAR(64)', 
						), 
						'4' => array(
							'name' => 'ユーザID', 
							'type' => 'INTEGER', 
						), 
						'5' => array(
							'name' => 'キャラクターID', 
							'type' => 'INTEGER', 
						), 
					), 
				);
				
				if ($rec['Rows'] > 0) {
					// 画面表示時に一度取得するのみなので
					$sql = "SELECT log FROM {$rec['Name']} ORDER BY log_id DESC LIMIT 1";
					$a = db_select($con, $sql);
					if ($a) {
						$log = $a[0]['log'];
						$l = explode("\t", $log);
						if (sizeof($l) > 5) {
							for ($i = 5;$i < sizeof($l);$i++) {
								$c = $i + 1;
								$loglist[$prefix]['cols']["{$c}"] = array('name' => "{$c}", 'type' => 'view_only');
							}
						}
					}
				}
			}
			else {
				// ログ情報が定義されている場合
				if ($current_log == $prefix) {
					// 検索できないので５項目目以降は type を view_only にする
					for ($i = 5;$i < sizeof($l);$i++) {
						$loglist[$prefix]['cols']["{$i}"]['type'] = 'view_only';
					}
				}
			}
			
			// 事と次第によっては行数でフィルタとかする事も考えておく
			$loglist[$prefix]['rows'] = $rec['Rows'];
		}
		
		return $loglist;
	}
