<?PHP
	/////////////////////////////////////
	// 登録・解約日
	
	// 休止や復帰を取る日数
	$border_days = 7;  // 最少
	$over_days = 60;   // 最大
	
	$users = array();
	
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
				if (! $user_id) {
					continue;
				}
				
				// 登録日を設定
				$users[$user_id] = array(
					'r' => $log_date, 
					'i' => $uid, 
				);
				
				
				if ($uid && $user_id) {
					// それはそれとして d_unique_id の方にレコードを生成
					$sql = sprintf("DELETE FROM d_unique_id WHERE unique_id = '%s'", db_qs($con, $uid));
					$res = db_exec($con, $sql);
					$sql = sprintf("INSERT INTO d_unique_id(unique_id,user_id) VALUES('%s', %d)", db_qs($con, $uid), $user_id);
					$res = db_exec($con, $sql);
				}
			}
			
			fclose($fp);
		}
	}
//var_dump($users);
//var_dump(__LINE__);
	$log_files = glob("{$log_dir}/login_{$exec_date}.log");
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
				if (! preg_match('/^[0-9]*$/', $user_id) || ! preg_match('/^[0-9]*$/', $chara_id)) {
					continue;
				}
				
				// ユーザIDがなくキャラIDだけあるものはキャラIDからユーザIDを出す
				if (! $user_id && $chara_id) {
					$user_id = ($chara_id & 0xFFFFFF);
				}
				
				// ユーザIDの無いものは除外
				if (! $user_id) {
					continue;
				}
				
				// 登録日を設定
				if (! isset($users[$user_id])) {$users[$user_id] = array();}
				$users[$user_id]['l'] = $log_date;
				if ($uid && ! isset($users[$user_id]['i'])) {
					$users[$user_id]['i'] = $uid;
				}
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
				if (! $user_id) {
					continue;
				}
				
				// 解約日を設定
				if (! isset($users[$user_id])) {$users[$user_id] = array();}
				$users[$user_id]['u'] = $log_date;
			}
			
			fclose($fp);
		}
	}
	
//var_dump(__LINE__);
	$limit = 100;
	$users_chunk = array_chunk($users, $limit, true);  // 一定件数ごとにまとめて行う
	foreach ($users_chunk AS $v) {
		
		$regists = $v;
		
		// まず対象キャラクターの現状の進捗の確保
		$tpl = "SELECT user_id, regist_date, last_login_date, unregist_date FROM d_regist_date WHERE user_id IN (%s)";
		$sql = sprintf($tpl, implode(',', array_keys($regists)));
		$arr = db_select($con, $sql);
		
		// 既に居るキャラクターの処理
		foreach ($arr AS $rec) {
			
			$tpl = "UPDATE d_regist_date SET regist_date='%s', last_login_date='%s', unregist_date='%s' WHERE user_id = %s";
			$sql = sprintf($tpl, ($regists[$rec['user_id']]['r'] ? $regists[$rec['user_id']]['r'] : $rec['regist_date'])
			                   , ($regists[$rec['user_id']]['l'] ? $regists[$rec['user_id']]['l'] : $rec['last_login_date'])
			                   , ($regists[$rec['user_id']]['u'] ? $regists[$rec['user_id']]['u'] : $rec['unregist_date'])
			                   , $rec['user_id']
			);
//var_dump($sql);
			$res = db_exec($con, $sql);
			
			unset($regists[$rec['user_id']]);
		}
		
		// 復帰情報の記録
		if (sizeof($values) > 0) {
			$sql = $tpl_ins . implode(',', $values);
			$res = db_exec($con, $sql);
			$values = array();
		}
		
//var_dump(__LINE__);
		// 新規キャラクターの投入
		if ($regists) {
			$tpl = "INSERT INTO d_regist_date(user_id, uid, regist_date, last_login_date, unregist_date) VALUES";
			$tpl_val = "(%s, '%s', '%s', '%s', '%s')";
			$values = array();
			foreach ($regists AS $user_id => $rec) {
				$values[] = sprintf($tpl_val, $user_id
				                            , db_qs($con, $rec['i'])
				                            , ($rec['r'] ? $rec['r'] : '0000-00-00')
				                            , ($rec['l'] ? $rec['l'] : '0000-00-00')
				                            , ($rec['u'] ? $rec['u'] : '0000-00-00')
				);
			}
			$sql = $tpl . implode(',', $values);
//var_dump($sql);
			$res = db_exec($con, $sql);
		}
	}
	