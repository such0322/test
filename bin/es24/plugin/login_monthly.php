<?PHP
	/////////////////////////////////////
	// 月次ログイン数
//mylog(__LINE__);
//var_dump(__LINE__);
	// 処理対象日が１日なら前月の処理を行なう
	if (date('d', $exec_ts) == '01') {
		
		// 前日を対象とする (月単位でしかものを見ないので１日でも30日でも特に変わらない)
		$ts = $exec_ts - 86400;
		
		// その前に未確定ログを削除
		$res = db_exec($con, 'DELETE FROM l_login_monthly WHERE is_fixed = 0');
		
		$m = date('Ym', $ts);
		$log_files = glob("{$log_dir}/login_{$m}*.log");
		
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
					
					$server_id = intval($server_id);
					
					// 日付の無いものは除外
					if (! $log_date) {
						continue;
					}
					
					if (! $user_id && $chara_id) {
						$user_id = charaid_to_userid($chara_id);
					}
					
					// ログイン数の加算
					if (! is_array($login_counts[$server_id])) {
						$login_counts[$server_id] = array(
							'user' => array(), 
							'chara' => array(), 
						);
					}
					$login_counts[$server_id]['user'][$user_id] = 1;
					$login_counts[$server_id]['chara'][$chara_id] = 1;
				}
				fclose($fp);
			}
		}
		
//var_dump($login_counts);
//var_dump(__LINE__);
		$tpl = "INSERT INTO l_login_monthly(log_date, log_month, server_id, user_count, chara_count, is_fixed) VALUES";
		$tpl_val = "('%s', '%s', %d, %d, %d, %d)";
		$values = array();
		foreach ($login_counts AS $server_id => $v) {
			$user_count = sizeof($v['user']);
			$chara_count = sizeof($v['chara']);
			$values[] = sprintf($tpl_val, date('Y-m-01 00:00:00', $ts), date('Y-m-01', $ts), $server_id, $user_count, $chara_count, $is_fixed);
		}
		$sql = $tpl . implode(',', $values);
		$res = db_exec($con, $sql);
//var_dump($sql);
		
		unset($login_counts);
	}
	