<?PHP
	$con = admin_con();
	
	// 一度に処理する最大件数
	$limit = 100;
	
	$exec_ts = mktime(0, 0, 0, date('m'), date('d') - $is_fixed, date('Y'));
	$exec_date = date('Ymd', $exec_ts);
	$exec_datetime = date('Y-m-d H:i:s', $exec_ts);
	
	$delete_border_date = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m'), date('d') - $is_fixed + 1, date('Y')));
	
	
	// 途中ログを削除
	$res = db_exec($con, "DELETE FROM l_freemoney WHERE is_fixed = 0 AND last_update < '{$delete_border_date}'");
	
	// 対象ログファイルの一覧を確保
	$log_files = glob("{$log_dir}/freemoney_{$exec_date}.log");
	
	// テンプレートとかの作成
	$tpl = "INSERT INTO l_freemoney(log_date, pftype, uid, user_id, chara_id, trade_val, trade_type, add_type, is_fixed) VALUES";
	$tpl_val = "('%s', %d, '%s', '%s', '%s', %d, %d, %d, %d)";
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
					$trade_val, 
					$trade_type, 
					$add_type, 
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				if (! preg_match('/^[0-9]*$/', $user_id) || ! preg_match('/^[0-9]*$/', $chara_id)) {
					continue;
				}
				
				// 特に細工せずそのまま格納
				$values[] = sprintf($tpl_val, db_qs($con, $log_date)
				                            , $pftype
				                            , db_qs($con, $uid)
				                            , db_qs($con, $user_id)
				                            , db_qs($con, $chara_id)
				                            , $trade_val
				                            , $trade_type
				                            , $add_type
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
	