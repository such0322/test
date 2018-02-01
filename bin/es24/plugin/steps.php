<?PHP
	/////////////////////////////////////
	// 最大進捗状況
//mylog(__LINE__);
//var_dump(__LINE__);
	
	// キャラ作成日があれば確保しておく
	$chara_create_dates = array();
	$log_files = glob("{$log_dir}/chara_create_{$exec_date}.log");
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
					$slot_id, 
					$chara_name, 
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				
				$chara_create_dates[$chara_id] = $log_date;
			}
			fclose($fp);
		}
	}
	
	// その前に未確定ログを削除
	$log_files = glob("{$log_dir}/step_{$exec_date}.log");
	$chara_steps = array();
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
//var_dump(__LINE__);
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
					$step, 
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
				
				// 進捗状況が進んでいれば進める
				if (isset($chara_steps[$chara_id])) {
					//if ($chara_steps[$chara_id]['s'] < $step)
					if (strcmp($chara_steps[$chara_id]['d'], $log_date) < 0)
					 {
						$chara_steps[$chara_id]['d'] = $log_date;
						$chara_steps[$chara_id]['s'] = $step;
					}
				}
				else {
					// 設定されていなかったので初期値を設定
					$chara_steps[$chara_id] = array(
						'u' => $user_id, 
						'd' => $log_date, 
						's' => $step, 
						'c' => ($chara_create_dates[$chara_id] ? $chara_create_dates[$chara_id] : '0000-00-00 00:00:00'), 
					);
				}
			}
			fclose($fp);
		}
	}
	
	
//var_dump(__LINE__);
	$limit = 100;
	$chara_steps_chunk = array_chunk($chara_steps, $limit, true);  // 一定件数ごとにまとめて行う
//var_dump($chara_steps_chunk);
	foreach ($chara_steps_chunk AS $v) {
		
		$steps = $v;
		
		// まず対象キャラクターの現状の進捗の確保
		$tpl = "SELECT chara_id, step, log_date FROM d_steps WHERE chara_id IN (%s)";
		$sql = sprintf($tpl, implode(',', array_keys($steps)));
		$arr = db_select($con, $sql);
//var_dump($arr);
		
		// 既に居るキャラクターの処理
//var_dump(__LINE__);
		foreach ($arr AS $rec) {
			//if ($rec['step'] < $steps[$rec['chara_id']]['s'])
			if (strcmp($rec['log_date'], $steps[$rec['chara_id']]['d']) < 0)
			 {
				$tpl = "UPDATE d_steps SET log_date='%s', step='%s', chara_create_date='%s', is_fixed=%d WHERE chara_id = %s";
				$sql = sprintf($tpl, $steps[$rec['chara_id']]['d'], $steps[$rec['chara_id']]['s'], $steps[$rec['chara_id']]['c'], $is_fixed, $rec['chara_id']);
//var_dump($sql);
				$res = db_exec($con, $sql);
			}
			unset($steps[$rec['chara_id']]);
		}
		
		// 新規キャラクターの投入
//var_dump(__LINE__);
		if ($steps) {
			$tpl = "INSERT INTO d_steps(log_date, user_id, chara_id, chara_create_date, step, is_fixed) VALUES";
			$tpl_val = "('%s', %s, %s, '%s', %d, %d)";
			$values = array();
			foreach ($steps AS $chara_id => $rec) {
				$values[] = sprintf($tpl_val, $rec['d'], $rec['u'], $chara_id, $rec['c'], $rec['s'], $is_fixed);
			}
			$sql = $tpl . implode(',', $values);
//var_dump($sql);
			$res = db_exec($con, $sql);
		}
	}
	
	/////////////////////////////////////
	// つみあげ進捗履歴の蒐集
//mylog(__LINE__);
	
	// その前に未確定ログを削除
	$log_files = glob("{$log_dir}/step_{$exec_date}.log");
	$chara_steps = array();
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
//var_dump(__LINE__);
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
					$step, 
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
				
				$chara_steps[] = array(
					'u' => $user_id, 
					'c' => $chara_id, 
					'd' => $log_date, 
					'g' => ($chara_create_dates[$chara_id] ? $chara_create_dates[$chara_id] : '0000-00-00 00:00:00'), 
					's' => $step, 
				);
			}
			fclose($fp);
		}
	}
	
//var_dump(__LINE__);
	$limit = 100;
	$chara_steps_chunk = array_chunk($chara_steps, $limit, true);  // 一定件数ごとにまとめて行う
	foreach ($chara_steps_chunk AS $v) {
		
		$tpl = "REPLACE INTO d_step_history(log_date, user_id, chara_id, chara_create_date, step, is_fixed) VALUES";
		$tpl_val = "('%s', '%s', '%s', '%s', %d, %d)";
		$values = array();
		foreach ($v AS $rec) {
			$values[] = sprintf($tpl_val, $rec['d'], $rec['u'], $rec['c'], $rec['g'], $rec['s'], $is_fixed);
		}
		$sql = $tpl . implode(',', $values);
//var_dump($sql);
		$res = db_exec($con, $sql);
	}
	