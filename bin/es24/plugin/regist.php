<?PHP
	/////////////////////////////////////
	// 登録数、解約数
//var_dump(__LINE__);
	$regist_count = 0;
	$unregist_count = 0;
	
	// その前に未確定ログを削除
	$res = db_exec($con, 'DELETE FROM l_regist WHERE is_fixed = 0');
	
	$log_files = glob("{$log_dir}/regist_{$exec_date}.log");
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
					$ad_code, 
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				
				// ユーザIDの無いものは除外
				if (! $user_id && $chara_id) {
					$user_id = charaid_to_userid($chara_id);
				}
				if (! $user_id) {
					continue;
				}
				
				// 登録数の追加
				$regist_count++;
			}
			
			fclose($fp);
		}
	}
//var_dump(__LINE__);
	$log_files = glob("{$log_dir}/unregist_{$exec_date}.log");
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
					$ad_code, 
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				
				// ユーザIDの無いものは除外
				if (! $user_id && $chara_id) {
					$user_id = charaid_to_userid($chara_id);
				}
				if (! $user_id) {
					continue;
				}
				
				// 解約数の追加
				$unregist_count++;
			}
			
			fclose($fp);
		}
	}
	$log_files = glob("{$log_dir}/login_{$exec_date}.log");
	$login_counts = array();
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
					$server_id
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				
				// ログイン数の加算
				if (! $user_id && $chara_id) {
					$user_id = charaid_to_userid($chara_id);
				}
				if ($user_id) {
					$login_counts[$user_id] = 1;
				}
			}
			fclose($fp);
		}
	}
	
	// レコードの投入
	$tpl = "INSERT INTO l_regist(log_date, log_day, regist_count, unregist_count, login_count, is_fixed) VALUES('%s', '%s', %d, %d, %d, %d)";
	$sql = sprintf($tpl, $exec_datetime, $exec_datetime, $regist_count, $unregist_count, sizeof($login_counts), $is_fixed);
	$res = db_exec($con, $sql);
	