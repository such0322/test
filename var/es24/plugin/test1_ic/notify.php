<?PHP
//print_r($_SESSION);
//var_dump($_SESSION['__mylog']);
	
	require_once( 'env.php' );
	require_once( 'lib/common.php' );
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	$env = env();
	$con = admin_con();
	$user_vars = user_vars_load();
	
	$id = (isset($LOCAL_SESSION['id']) ? $LOCAL_SESSION['id'] : 0);
	$f1_result = (isset($LOCAL_SESSION['f1_result']) ? $LOCAL_SESSION['f1_result'] : 0);
	$f2_result = (isset($LOCAL_SESSION['f2_result']) ? $LOCAL_SESSION['f2_result'] : 0);
	
	// 通知グループマスタ (これは user_vars に移す予定)
/*
	$apns_group_id_list = array(
		0 => '指定なし', 
		1 => '(仮) 行動力回復', 
		2 => '(仮) コメント受信', 
		3 => '(仮) 強敵発見', 
		4 => '(仮) お使いの結果がでました', 
	);
*/
	$apns_group_id_list = $user_vars['apns_group'];
	if (! isset($apns_group_id_list[0])) {
		$apns_group_id_list[0] = '指定なし';
	}
	
	// apns種別マスタ (これは管理側の内部的に決めてるものなので user_vars で編集させない)
	$trigger_type_list = array(
		0 => '送信しない', 
		1 => '予約して一回だけ送信', 
		2 => '期間内の指定した時間に送信', 
		3 => 'ユーザごとに指定があれば送信', 
	);
	if (! isset($trigger_type_list[0])) {
		$trigger_type_list[0] = '送信しない';
	}
	
	// 曜日リスト
	$wday_list = array(0 => '日', 1 => '月', 2 => '火', 3 => '水', 4 => '木', 5 => '金', 6 => '土', );
	
	// テーブル情報
	$master = array(
		'apns_group_id' => $apns_group_id_list, 
		'trigger_type' => $trigger_type_list, 
		'trigger_wday' => $wday_list, 
	);
	$tables = array(
		'notify_master' => array(
			'id' => array(
				'type' => 'primary',
				'size' => 8, 
				'name' => '通知マスタID',
			), 
			'trigger_type' => array(
				'type' => 'enum',
				'size' => 4, 
				'name' => '通知種別', 
			), 
			'trigger_datetime' => array(
				'type' => 'datetime',
				'size' => 20, 
				'name' => '送信日時',
			), 
			'begin_date' => array(
				'type' => 'date',
				'size' => 10, 
				'name' => '送信期間始端日',
			), 
			'end_date' => array(
				'type' => 'date',
				'size' => 10, 
				'name' => '送信期間終端日',
			), 
			'trigger_wday' => array(
				'type' => 'enumulti',
				'size' => 7, 
				'name' => '送信曜日',
				'separator' => ',',  
			), 
			'trigger_time' => array(
				'type' => 'time',
				'size' => 8, 
				'name' => '送信時刻',
			), 
			'msg' => array(
				'type' => 'text',
				'name' => '本文', 
			), 
			'memo' => array(
				'type' => 'text',
				'name' => 'メモ', 
			), 
		), 
	);
	$select_columns = $tables['notify_master'];
	
	// オーダー、リミット、オフセット
	$limit_list = array(10, 50, 100, 200, 500, 1000);
	$default_limit  = 100;
	$default_offset = 0;
	$default_order  = 'id';
	$default_display_cols = array('id', 'trigger_type', 'msg');
	
	$limit  = (intval($LOCAL_SESSION['limit']) > 0 ? intval($LOCAL_SESSION['limit']) : $default_limit);
	$offset = (intval($LOCAL_SESSION['offset']) >= 0 ? intval($LOCAL_SESSION['offset']) : $default_offset);
	$order  = (array_key_exists($LOCAL_SESSION['order'], $select_columns) ? $LOCAL_SESSION['order'] : $default_order);
	$desc   = ($LOCAL_SESSION['desc'] == 'DESC' ? 'DESC' : '');
	$LOCAL_SESSION['desc'] = $desc;  // セッション変数に null が入って warnning が出ることがあるので暫定処置
	$display_cols = (is_array($LOCAL_SESSION['display_cols'])?$LOCAL_SESSION['display_cols']:$default_display_cols);
	
	
	////////////////////////////////////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	if (isset($_POST['f1_submit'])) {
		try {
			
			// 入力値の確保
			$pkk = 'id';
			$pk = $_POST[$pkk];
			$post_data = array();
			foreach ($select_columns as $col => $info) {
				$post_data[$col] = $_POST[$col];
			}
			
			// 入力内容のエラーチェック
			$values = check_values($select_columns, $post_data);
//mylog($post_data);mylog($values);
			
			// レコードの有無を確認
			$sql = sprintf("SELECT count(*) AS cnt FROM notify_master WHERE %s = %d", $pkk, $pk);
			$arr = db_select($con, $sql);
//mylog($arr);mylog($sql);
			
			// 実際に更新
			unset($values[$pkk]);
			if ($arr && $arr[0]['cnt'] > 0) {
				// UPDATE
				$sets = array();
				foreach ($values as $k => $v) {
					$sets[] = sprintf("%s='%s'", $k, db_qs($con, $v));
				}
				$tpl = "UPDATE notify_master SET %s WHERE %s = %d";
				$sql = sprintf($tpl, implode(',', $sets), $pkk, $pk);
				$res = db_exec($con, $sql);
			} else {
				// INSERT
				$cols = array();
				$vals = array();
				
				// 主キーの指定があればそれも設定
				if ($pk > 0) {
					$cols[] = $pkk;
					$vals[] = intval($pk);
				}
				
				foreach ($values as $k => $v) {
					$cols[] = $k;
					$vals[] = sprintf("'%s'", db_qs($con, $v));
				}
				$tpl = "INSERT INTO notify_master(%s) VALUES(%s)";
				$sql = sprintf($tpl, implode(',', $cols), implode(',', $vals));
				$res = db_exec($con, $sql);
				
				$LOCAL_SESSION['id'] = db_insert_id($con);
			}
//mylog($sql);
			
		} catch (Eception $e) {
			
			
			
		}
	}
	elseif (isset($_POST['f2_submit'])) {
		// 検索条件の変更
		
	}
	elseif (isset($_POST['f4_submit'])) {
		
		// レコードの削除1
		
		if (isset($_POST['delete_id']) && is_array($_POST['delete_id'])) {
			$delete_ids = array();
			foreach ($_POST['delete_id'] AS $delete_id) {
				if (preg_match('/^[0-9]+$/', $delete_id)) {
					$delete_ids[] = $delete_id;
				}
			}
			if ($delete_ids) {
				$sql = sprintf('DELETE FROM notify_master WHERE id IN (%s)', implode(',', $delete_ids));
				$res = db_exec($con, $sql);
			}
		}
	}
	elseif (isset($_POST['f5_submit'])) {
		// 編集対象レコードの選択
		if (preg_match('/^[0-9]+$/', $_POST['id'])) {
			$LOCAL_SESSION['id'] = $_POST['id'];
		}
		else {
			unset($LOCAL_SESSION['id']);
		}
	}
	elseif (isset($_POST['f6_submit'])) {
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
	// 表示内容作成
	
	//////////////////////////////////////////////////////
	// 表示内容の生成、入力フォーム
	
	$f1_defaults = array(
		'trigger_wday_checker' => array(), 
		'expiry' => 120, 
	);
	if ($id > 0) {
		$sql = sprintf("SELECT * FROM notify_master WHERE id = %d", $id);
		$arr = db_select($con, $sql);
		if ($arr) {
			$f1_defaults = $arr[0];
			$f1_defaults['trigger_wday_checker'] = array_combine(explode(',', $arr[0]['trigger_wday']), explode(',', $arr[0]['trigger_wday']));
		}
	}
	$psmarty->assign('f1_defaults', $f1_defaults);
	
	//////////////////////////////////////////////////////
	// 表示内容の生成、検索フォーム
	
	//////////////////////////////////////////////////////
	// 表示内容の設定、マスタ系
	$psmarty->assign('apns_group_id_list', $apns_group_id_list);
	$psmarty->assign('trigger_type_list', $trigger_type_list);
	
	//////////////////////////////////////////////////////
	// 表示内容の生成、検索結果
	$apnss = array();
	
	$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM notify_master";
	if (sizeof($where_array) > 0) {
		$sql .= ' WHERE ' . implode(' AND ', $where_array);
	}
	$sql .= sprintf(' ORDER BY %s %s LIMIT %d,%d', $order, $desc, $offset, $limit);
	$arr = db_select($con, $sql);
	foreach ($arr as $rec) {
		$log = $rec;
		
		// サイズの計算
		$log['payload_size'] =  strlen(json_encode(array('aps' => array('alert' => $rec['msg'], ), )));
		$log['is_payload_size_over'] = ($log['payload_size'] > 256 ? 1 : 0);
		
		$wdays = array();
		$a = explode(',', $rec['trigger_wday']);
		foreach ($a AS $b) {
			$wdays[] = $wday_list[$b];
		}
		$log['trigger_wday_names'] = implode(', ', $wdays);
		
		$apnss[] = $log;
	}
	$psmarty->assign('apnss', $apnss);
	
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
	$ret = db_select($con, $query);
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
	
	//
	// -------------------------------------------------------------------------
	//
	
	/** ポスト内容を整形して返す */
	function check_values($table, $post_data) {
		$ret = array();
		
		$master = $GLOBALS['master'];
		
		foreach ($table as $col => $info) {
			switch ($info['type']) {
				case 'primary' :
				case 'key' :
				case 'num' :
					$n = preg_replace('/(,|\.|\s)/', '', $post_data[$col]);
					if (preg_match('/^[0-9]+$/', $n)) {
						$ret[$col] = $n;
					}
					else {
						$ret[$col] = null;
					}
					break;
				case 'enum' :
					if (array_key_exists($col, $master) && array_key_exists($post_data[$col], $master[$col])) {
						$ret[$col] = ($post_data[$col]);
					}
					else {
						$ret[$col] = null;
					}
					break;
				case 'enumulti' :
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
							$ret[$col] = implode((isset($info['separator'])?$info['separator']:''), $vals);
						}
					}
					break;
				case 'strkey' :
				case 'string' :
				case 'text' :
					if (isset($post_data[$col])) {
						$ret[$col] = ($post_data[$col]);
					}
					else {
						$ret[$col] = null;
					}
					break;
				case 'date' :
					if (preg_match('/^20[0-9]{2}-(0?[0-9]|1[0-2])-([012]?[0-9]|3[01])$/', $post_data[$col])) {
						$ret[$col] = $post_data[$col];
					}
					else {
						$ret[$col] = null;
					}
					break;
				case 'time' :
					if (preg_match('/^([01]?[0-9]|2[0-3]):([0-5]?[0-9]):([0-5]?[0-9])$/', $post_data[$col])) {
						$ret[$col] = $post_data[$col];
					}
					else {
						$ret[$col] = null;
					}
					break;
				case 'datetime' :
					if (preg_match('/^20[0-9]{2}-(0?[0-9]|1[0-2])-([012]?[0-9]|3[01]) ([01]?[0-9]|2[0-3]):([0-5]?[0-9]):([0-5]?[0-9])$/', $post_data[$col])) {
						$ret[$col] = $post_data[$col];
					}
					else {
						$ret[$col] = null;
					}
					break;
				case 'unixtime' :
					if (preg_match('/^20[0-9]{2}-(0?[0-9]|1[0-2])-([012]?[0-9]|3[01]) ([01]?[0-9]|2[0-3]):([0-5]?[0-9]):([0-5]?[0-9])$/', $post_data[$col])) {
						$ret[$col] = strtotime($post_data[$col]);
					}
					else {
						$ret[$col] = null;
					}
					break;
				case 'checkbox' :
					if (isset($post_data[$col])) {
						$ret[$col] = ($post_data[$col]);
					}
					else {
						$ret[$col] = null;
					}
					break;
				default:
					break;
			}
		}
		
		return $ret;
	}
