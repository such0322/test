<?PHP
	////////////////////////////////////////////////////////////////////////////
	// ガチャログの取り込み
//mylog(__LINE__);
//var_dump(__LINE__);
	
	// 途中ログを削除
	$res = db_exec($con, "DELETE FROM l_gacha WHERE is_fixed = 0 AND last_update < '{$delete_border_date}'");
	
	// 対象ログファイルの一覧を確保
	$log_files = glob("{$log_dir}/gacha_{$exec_date}.log");
	
	// テンプレートとかの作成
	$tpl = "INSERT INTO l_gacha(log_date, pftype, uid, user_id, chara_id, gacha_type, gacha_id, drop_id, shop_point, ingame_point, ticket_id, ticket_num, is_free, is_comp, is_fixed) VALUES";
	$tpl_val = "('%s', %d, '%s', '%s', '%s', %d, %d, '%s', %d, %d, %d, %d, %d, %d, %d)";
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
				if (! preg_match('/^[0-9]*$/', $user_id) || ! preg_match('/^[0-9]*$/', $chara_id)) {
					continue;
				}
				
				if (! $user_id && $chara_id) {
					$user_id = charaid_to_userid($chara_id);
				}
				
				list(
					$log_date, 
					$pftype, 
					$uid, 
					$user_id, 
					$chara_id, 
					$gacha_type, 
					$gacha_id, 
					$drop_id, 
					$shop_point, 
					$ingame_point, 
					$ticket_id, 
					$ticket_num, 
					$is_free, 
					$is_comp, 
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				
				// 特に細工せずそのまま格納
				$values[] = sprintf($tpl_val, db_qs($con, $log_date)
				                            , $pftype
				                            , db_qs($con, $uid)
				                            , db_qs($con, $user_id)
				                            , db_qs($con, $chara_id)
				                            , $gacha_type
				                            , $gacha_id
				                            , db_qs($con, $drop_id)
				                            , $shop_point
				                            , $ingame_point
				                            , $ticket_id
				                            , $ticket_num
				                            , $is_free
				                            , $is_comp
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
	