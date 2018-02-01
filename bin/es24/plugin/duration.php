<?PHP
	
	// 一度に処理する最大件数
	$limit = 100;
	
	$delete_border_date = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m'), date('d') - $is_fixed + 1, date('Y')));
	
	// 導入、継続
	$userid_uid = array();
	$uid_userid = array();
	$users = array();
	
	////////////////////////////////////////////////////////////////////////
	// 先に必要になるであろう user_id => uid のハッシュを作成
	$log_files = array_merge(glob("{$log_dir}/launch_{$exec_date}.log"), glob("{$log_dir}/begin_{$exec_date}.log"), glob("{$log_dir}/continue_{$exec_date}.log"), glob("{$log_dir}/regist_{$exec_date}.log"), glob("{$log_dir}/step_{$exec_date}.log"));
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
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				
				if ($user_id) {
					$userid_uid[$user_id] = $uid;
				}
				if ($uid) {
					$uid_userid[$uid] = $user_id;
				}
			}
		}
	}
	
	// 不足分を取り直す
	$a = array();
	foreach ($userid_uid AS $user_id => $uid) {
		if (! $uid) {
			$a[] = db_qs($con, $user_id);
		}
	}
	$ac = array_chunk($a, $limit, true);  // 一定件数ごとにまとめて行う
	foreach ($ac As $a) {
		$sql = sprintf("SELECT unique_id, user_id FROM d_unique_id WHERE user_id IN ('%s')", implode("','", $a));
		$arr = db_select($con, $sql);
		if ($arr) {
			foreach ($arr AS $rec) {
				$userid_uid[$rec['user_id']] = $rec['unique_id'];
			}
		}
	}
	$a = array();
	foreach ($uid_userid AS $uid => $user_id) {
		if (! $user_id) {
			$a[] = db_qs($con, $uid);
		}
	}
	$ac = array_chunk($a, $limit, true);  // 一定件数ごとにまとめて行う
	foreach ($ac As $a) {
		$sql = sprintf("SELECT unique_id, user_id FROM d_unique_id WHERE unique_id IN ('%s')", implode("','", $a));
		$arr = db_select($con, $sql);
		if ($arr) {
			foreach ($arr AS $rec) {
				$uid_userid[$rec['unique_id']] = $rec['user_id'];
			}
		}
	}
	
	////////////////////////////////////////////////////////////////////////
	// 起動数の集計
	
//var_dump(__LINE__);
	$log_files = glob("{$log_dir}/launch_{$exec_date}.log");
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
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				if (! preg_match('/^[0-9]*$/', $user_id) || ! preg_match('/^[0-9]*$/', $chara_id)) {
					continue;
				}
				
				// 不足項目の補填
				if (! $user_id && $chara_id) {
					$user_id = charaid_to_userid($chara_id);
				}
				if (! $uid) {
					$uid = $userid_uid[$user_id];
				}
				
				// uidの無いものは除外
				if (! $uid) {
					continue;
				}
				
				// 起動日を設定
				if (isset($users[$uid]) && isset($users[$uid]['l'])) {
					// 設定済みの場合は若い値を優先
					if (strcmp($log_date, $users[$uid]['l']) < 0) {
						$users[$uid]['l'] = $log_date;
					}
				}
				else {
					$users[$uid] = array(
						'l' => $log_date, 
					);
				}
			}
			
			fclose($fp);
		}
	}
	
	////////////////////////////////////////////////////////////////////////
	// 導入数の集計
	$log_files = glob("{$log_dir}/begin_{$exec_date}.log");
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
				
				// 不足項目の補填
				if (! $user_id && $chara_id) {
					$user_id = charaid_to_userid($chara_id);
				}
				if (! $uid) {
					$uid = $userid_uid[$user_id];
				}
				
				// uid の無いものは除外
				if (! $uid) {
					continue;
				}
				
				// 開始日を設定
				if (isset($users[$uid])) {
					if (! isset($users[$uid]['b'])) {
						$users[$uid]['b'] = $log_date;
						$users[$uid]['c'] = $log_date;
					}
					elseif (strcmp($log_date, $users[$uid]['b']) < 0) {
						// 開始日は古いものが優先される
						$users[$uid]['b'] = $log_date;
						$users[$uid]['c'] = $log_date;
					}
				}
				else {
					$users[$uid] = array(
						'b' => $log_date, 
						'c' => $log_date, 
					);
				}
				
				// user_id の設定
				if ($user_id) {
					$users[$uid]['u'] = $user_id;
				}
			}
			
			fclose($fp);
		}
	}
	
	////////////////////////////////////////////////////////////////////////
	// 継続の集計
	$log_files = glob("{$log_dir}/continue_{$exec_date}.log");
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
				if (! preg_match('/^[0-9]*$/', $user_id) || ! preg_match('/^[0-9]*$/', $chara_id)) {
					continue;
				}
				
				// 不足項目の補填
				if (! $user_id && $chara_id) {
					$user_id = charaid_to_userid($chara_id);
				}
				if (! $uid) {
					$uid = $userid_uid[$user_id];
				}
				
				// uid の無いものは除外
				if (! $uid) {
					continue;
				}
				
				// 継続日を設定
				if (isset($users[$uid])) {
					if (! isset($users[$uid]['c'])) {
						$users[$uid]['c'] = $log_date;
					}
					elseif (strcmp($log_date, $users[$uid]['c']) > 0) {
						// 継続は新しいものが優先される
						$users[$uid]['c'] = $log_date;
					}
				}
				else {
					$users[$uid] = array(
						'c' => $log_date, 
					);
				}
				
				// user_id の設定
				if ($user_id) {
					$users[$uid]['u'] = $user_id;
				}
			}
			
			fclose($fp);
		}
	}
	
	
	////////////////////////////////////////////////////////////////////////
	// uid 単位で最新の進捗を確保
	$log_files = glob("{$log_dir}/step_{$exec_date}.log");
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
			while (! feof($fp)) {
				$log = fgets($fp);
				
				list(
					$log_date, 
					$pftype, 
					$uid, 
					$user_id, 
					$chara_id, 
					$step, 
				) = explode("\t", rtrim($log, "\r\n"));
				
				if (! $user_id && $chara_id) {
					$user_id = ($chara_id & 0xFFFFFF);
				}
				if (! $uid) {
					$uid = $userid_uid[$user_id];
				}
				
				if ($uid && $step) {
					if (! isset($users[$uid])) {
						// 設定されてなければ素直に設定
						$users[$uid] = array(
							's' => $step, 
							'd' => $log_date, 
						);
						if ($user_id) {
							$users[$uid]['u'] = $user_id;
						}
					}
					elseif (! $users[$uid]['d']) {
						// step 最終更新日時が空なので更新
						$users[$uid]['s'] = $step;
						$users[$uid]['d'] = $log_date;
					}
					elseif (strcmp($users[$uid]['d'], $log_date) < 0) {
						
						// 日時で比較して新しければ更新
						$users[$uid]['s'] = $step;
						$users[$uid]['d'] = $log_date;
						if ($user_id) {
							$users[$uid]['u'] = $user_id;
						}
					}
					else {
						// 古いログなので何もしない
					}
				}
			}
			fclose($fp);
		}
	}
	
	
	////////////////////////////////////////////////////////////////////////
	// 集計結果の格納
	$users_chunk = array_chunk($users, $limit, true);  // 一定件数ごとにまとめて行う
	foreach ($users_chunk AS $v) {
		
		$regists = $v;
		$uids = array();
		foreach ($v AS $uid => $rec) {
			$uids[] = db_qs($con, $uid);
		}
		
		// まず対象ユーザの現状の状態の確保
		$tpl = "SELECT d_duration_id, uid, user_id, launch_date, regist_date, last_login_date, last_step, last_step_date FROM d_duration WHERE uid IN ('%s')";
		$sql = sprintf($tpl, implode("','", $uids));
		$arr = db_select($con, $sql);
		
		// 既に居るキャラクターの処理
		foreach ($arr AS $rec) {
			
			$log = $regists[$rec['uid']];
			
			$sets = array();
			if ($log['u'] && $log['u'] != $rec['user_id']) {
				$sets[] = sprintf(" user_id = '%d' ", $log['u']);
			}
			if ($log['l']) {
				if ($rec['launch_date'] == '0000-00-00') {
					$sets[] = sprintf(" launch_date = '%s' ", $log['l']);
				}
				elseif (strcmp($log['l'], $rec['launch_date']) < 0) {
					$sets[] = sprintf(" launch_date = '%s' ", $log['l']);
				}
			}
			if ($log['r']) {
				if ($rec['regist_date'] == '0000-00-00') {
					$sets[] = sprintf(" regist_date = '%s' ", $log['r']);
				}
				elseif (strcmp($log['r'], $rec['regist_date']) < 0) {
					$sets[] = sprintf(" regist_date = '%s' ", $log['r']);
				}
			}
			if ($log['c']) {
				$sets[] = sprintf(" last_login_date = '%s' ", $log['c']);
			}
			if ($log['s'] && $log['d']) {
				
				if ($rec['last_step_date'] == '0000-00-00') {
					$sets[] = sprintf(" last_step = '%d' ", $log['s']);
					$sets[] = sprintf(" last_step_date = '%s' ", $log['d']);
				}
				elseif (strcmp($log['d'], $rec['last_step_date']) < 0) {
					$sets[] = sprintf(" last_step = '%d' ", $log['s']);
					$sets[] = sprintf(" last_step_date = '%s' ", $log['d']);
				}
			}
			
			if (sizeof($sets) > 0) {
				$sql = sprintf("UPDATE d_duration SET %s WHERE d_duration_id = %d", implode(' , ', $sets), $rec['d_duration_id']);
//var_dump($sql);
				$res = db_exec($con, $sql);
//if (! $res) {var_dump(db_error($con));}
			}
			
			unset($regists[$rec['uid']]);
		}
		
//var_dump(__LINE__);
		// 新規ユーザ情報の投入
		if ($regists) {
			$tpl = "INSERT INTO d_duration(user_id, uid, launch_date, regist_date, last_login_date, last_step, last_step_date) VALUES";
			$tpl_val = "('%s', '%s', '%s', '%s', '%s', %d, '%s')";
			$values = array();
			foreach ($regists AS $uid => $rec) {
				$values[] = sprintf($tpl_val, $rec['u']
				                            , db_qs($con, $uid)
				                            , ($rec['l'] ? $rec['l'] : '0000-00-00')
				                            , ($rec['b'] ? $rec['b'] : '0000-00-00')
				                            , ($rec['c'] ? $rec['c'] : '0000-00-00')
				                            , $rec['s']
				                            , ($rec['d'] ? $rec['d'] : '0000-00-00')
				);
			}
			$sql = $tpl . implode(',', $values);
//var_dump($sql);
			$res = db_exec($con, $sql);
//if (! $res) {var_dump(db_error($con));}
		}
	}