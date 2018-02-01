<?PHP
	////////////////////////////////////////////////////////////////////////////
	// アイテム移動の取り込み
//mylog(__LINE__);
//var_dump(__LINE__);
	
	// 途中ログを削除
	$res = db_exec($con, "DELETE FROM l_item_move WHERE is_fixed = 0 AND last_update < '{$delete_border_date}'");
	
	// コミット時ならば古いログも削除
	if ($is_fixed) {
		$res = db_exec($con, 'DELETE FROM l_item_move WHERE log_date < date_sub(now(), INTERVAL 6 month)');
	}
	
	// 取り込み対象アイテムIDを確保
	
	// 対象ログファイルの一覧を確保
	$log_files = glob("{$log_dir}/item_act_{$exec_date}.log");
	
	// テンプレートとかの作成
	$tpl = "INSERT INTO l_item_move(log_date, pftype, uid, user_id, chara_id, type, item_id, item_num, item_serial, action_type, trade_price, target_user_id, target_chara_id, is_fixed) VALUES";
	$tpl_val = "('%s', %d, '%s', '%s', '%s', %d, '%s', %d, '%s', %d, %d, '%s', '%s', %d)";
	$values = array();
	$values_max = 50;
	
	// ログファイルの読み込み
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
			while (! feof($fp)) {
				$log = fgets($fp);
				
				// 空行は次へ
				if (! rtrim($log, "\r\n")) {
					continue;
				}
				
				list(
					$log_date, 
					$pftype, 
					$uid, 
					$user_id, 
					$chara_id, 
					$type, 
					$item_id, 
					$item_num, 
					$item_serial, 
					$action_type, 
					$trade_price, 
					$target_user_id, 
					$target_chara_id, 
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				if (! preg_match('/^[0-9]*$/', $user_id) || ! preg_match('/^[0-9]*$/', $chara_id)) {
					continue;
				}
				
				if (! $user_id && $chara_id) {
					$user_id = charaid_to_userid($chara_id);
				}
				
				// 取り込み対象外のアイテムIDなら除外
				//if () {
				//
				//}
				
				
				// 特に細工せずそのまま格納
				$values[] = sprintf($tpl_val, db_qs($con, $log_date)
				                            , $pftype
				                            , db_qs($con, $uid)
				                            , db_qs($con, $user_id)
				                            , db_qs($con, $chara_id)
				                            , $type
				                            , db_qs($con, $item_id)
				                            , $item_num
				                            , db_qs($con, $item_serial)
				                            , $action_type
				                            , $trade_price
				                            , $target_user_id
				                            , $target_chara_id
				                            , $is_fixed
				);
				
				// 一定数たまったら投入
				if (sizeof($values) > $values_max) {
					$sql = $tpl . implode(',', $values);
					$res = db_exec($con, $sql);
					$values = array();
				}
			}
			
			fclose($fp);
		}
	}
	
	// まだ残ってれば投入
	if (sizeof($values) > 0) {
		$sql = $tpl . implode(',', $values);
		$res = db_exec($con, $sql);
		$values = array();
	}
	