<?PHP
	/////////////////////////////////////
	// ログイン数
//mylog(__LINE__);
//var_dump(__LINE__);
	
	// その前に未確定ログを削除
	$res = db_exec($con, 'DELETE FROM l_login_daily WHERE is_fixed = 0');
	
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
				$server_id = intval($server_id);
				if (! is_array($login_counts[$server_id])) {
					$login_counts[$server_id] = array(
						'user' => array(), 
						'chara' => array(), 
					);
				}
				if (! $user_id && $chara_id) {
					$user_id = charaid_to_userid($chara_id);
				}
				if ($user_id) {
					$login_counts[$server_id]['user'][$user_id] = 1;
				}
				if ($chara_id) {
					$login_counts[$server_id]['chara'][$chara_id] = 1;
				}
			}
			fclose($fp);
		}
	}
	
//var_dump(__LINE__);
	if ($login_counts) {
		$tpl = "INSERT INTO l_login_daily(log_date, log_day, server_id, user_count, chara_count, is_fixed) VALUES";
		$tpl_val = "('%s', '%s', %d, %d, %d, %d)";
		$values = array();
		foreach ($login_counts AS $server_id => $v) {
			$user_count = sizeof($v['user']);
			$chara_count = sizeof($v['chara']);
			$values[] = sprintf($tpl_val, $exec_datetime, $exec_datetime, $server_id, $user_count, $chara_count, $is_fixed);
		}
		$sql = $tpl . implode(',', $values);
		$res = db_exec($con, $sql);
		
		unset($login_counts);
	}
	