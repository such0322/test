<?PHP
	
	////////////////////////////////////////////////////////////////////////////
	// 処理開始
	
	// 更新のあったレコードを取得
	$enddate = date('Y-m-d H:i:s', $exec_ts + 86400);
	
	$sql = sprintf("SELECT unique_id, data FROM d_master_uid WHERE '%s' <= last_update AND last_update < '%s'", $exec_datetime, $enddate);
	$h = db_exec($ndb_con, $sql);
	if ($h) {
		while ($rec = db_fetch($h)) {
			$user_id = ngp_read_uid($rec['data']);
			if ($user_id) {
				// uidが見つかったのでレコードの作成
				$sql = sprintf("DELETE FROM d_unique_id WHERE unique_id = '%s'", db_qs($con, $rec['unique_id']));
				$res = db_exec($con, $sql);
				$sql = sprintf("INSERT INTO d_unique_id(unique_id,user_id) VALUES('%s', %d)", db_qs($con, $rec['unique_id']), $user_id);
				$res = db_exec($con, $sql);
			}
			
		}
	}
	
	// ログ上の不足項目を補完
	$tables  = array(
		'l_chat_log', 
		'l_freemoney', 
		'l_gacha', 
		'l_item_move', 
		'l_kakin', 
		'l_realmoney_payment', 
		'l_realmoney_trade', 
		'l_shop', 
	);
	foreach ($tables as $table) {
		$sql = "UPDATE {$table} SET user_id = (SELECT user_id FROM d_unique_id WHERE d_unique_id.unique_id = {$table}.uid LIMIT 1) WHERE user_id = 0";
		$res = db_exec($con, $sql);
		$sql = "UPDATE {$table} SET uid = (SELECT unique_id FROM d_unique_id WHERE d_unique_id.user_id = {$table}.user_id LIMIT 1) WHERE uid = ''";
		$res = db_exec($con, $sql);
	}
