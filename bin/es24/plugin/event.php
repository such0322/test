<?PHP
	/** 各種イベント系集計の更新 */
	
	
	////////////////////////////////////////////////////////////////////////
	// ユーザ単位での集計
	
	$log_files = glob("{$log_dir}/event_step_{$exec_date}.log");
	
	$chara_count = array(
//		event_id => array(
//			chara_id => 1, 
//		), 
//		...
	);
	$duration = array(
//		event_id => array(
//			user_id => array(
//				d => date
//				'max_step' => max
//				'last_step' => last
//			), 
//			...
//		), 
	);
	
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
			while (! feof($fp)) {
				$log = fgets($fp);
				
				// 空行は次へ
				if (! rtrim($log, "\r\n")) {
					continue;
				}
				
				// まず分断する
				list(
					$log_date, 
					$pftype, 
					$uid, 
					$user_id, 
					$chara_id, 
					$step, 
					$event_id, 
				) = explode("\t", trim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				if (! preg_match('/^[0-9]*$/', $chara_id)) {
					continue;
				}
				$user_id = ($user_id ? $user_id : intval(0xFFFFFF & $chara_id));
				
				// まずキャラクターカウントを加算
				if (! $chara_count[$event_id]) {
					$chara_count[$event_id] = array();
				}
				$chara_count[$event_id][$chara_id] = 1;
				
				// キャラごとの進捗を作成
				if (! $duration[$event_id]) {
					$duration[$event_id] = array();
				}
				if (! isset($duration[$event_id][$user_id])) {
					// 無い場合は新規作成
					$duration[$event_id][$user_id] = array(
						'event_begin_date' => $log_date, 
						'regist_date' => $log_date, 
						'max_step' => $step, 
						'last_step' => $step, 
					);
				}
				else {
					// ある場合は条件に応じて更新
					if ($duration[$event_id][$user_id]['max_step'] < $step) {
						$duration[$event_id][$user_id]['max_step'] = $step;
					}
					if (strcmp($duration[$event_id][$user_id]['regist_date'], $log_date) < 0) {
						$duration[$event_id][$user_id]['regist_date'] = $log_date;
						$duration[$event_id][$user_id]['last_step'] = $step;
					}
					if (strcmp($duration[$event_id][$user_id]['event_begin_date'], $log_date) > 0) {
						$duration[$event_id][$user_id]['event_begin_date'] = $log_date;
					}
				}
			}
		}
	}
	
	// ログイン数を DB に格納
	foreach ($chara_count AS $event_id => $c) {
		$sql = sprintf("INSERT INTO l_event_usercount(log_date, event_id, user_count, chara_count) VALUES('%s', %d, %d, %d)"
		             , date('Y-m-d', $exec_ts)
		             , $event_id
		             , sizeof($duration[$event_id])
		             , sizeof($c)
		);
		$res = db_exec($con, $sql);
	}
	
	// 進捗をDBに格納
	// TODO: 複数件まとめて処理で効率化できる
	foreach ($duration AS $event_id => $charas) {
		
		foreach ($charas AS $user_id => $rec) {
			$a = $rec;
			
			// 現在の状況を取得
			$tpl = "SELECT d_event_duration_id, last_step, max_step, last_login_date FROM d_event_duration WHERE event_id = %d AND chara_id = %d";
			$sql = sprintf($tpl, $event_id, $chara_id);
			$arr = db_select($con, $sql);
			
			if ($arr) {
				// すでにあれば状況に応じて更新
				$m = ($arr[0]['max_step'] > $a['max_step'] ? $arr[0]['max_step'] : $a['max_step']);
				$d = $arr[0]['last_login_date'];
				$l = $arr[0]['last_step'];
				if (strcmp($d, $a['regist_date']) < 0) {
					$d = $a['regist_date'];
					$l = $a['last_step'];
				}
				
				$tpl = "UPDATE d_event_duration SET last_step = %d, max_step = %d, last_login_date = '%s' WHERE d_event_duration_id = %d";
				$sql = sprintf($tpl, $d, $m, $l, $arr[0]['d_event_duration_id']);
				$res = db_exec($con, $sql);
			}
			else {
				// なければ不足情報を取得して新規登録
				
				$uid = '';
				$regist_date = '0000-00-00 00:00:00';
				
				// UIDおよびregist_dateを取得
				$sql = sprintf("SELECT uid, regist_date FROM d_regist_date WHERE user_id = %d", ($chara_id & 0xFFFFFF));
				$arr = db_select($con, $sql);
				if ($arr) {
					$uid = $arr[0]['uid'];
					$regist_date = $arr[0]['regist_date'];
					
					$a['uid'] = $uid;
					$a['regist_date'] = $regist_date;
				}
				
				// 新規登録用のクエリを発行
				$tpl = "INSERT INTO d_event_duration(uid, user_id, event_id, last_step, max_step, regist_date, begin_date, last_login_date) VALUES('%s', %d, %d, %d, %d, '%s', '%s', '%s')";
				$sql = sprintf($tpl
				             , db_qs($con, $uid)
				             , $user_id
				             , $event_id
				             , $a['last_step']
				             , $a['max_step']
				             , $regist_date
				             , $a['event_begin_date']
				             , $a['regist_date']
				);
				$res = db_exec($con, $sql);
			}
		}
	}
	