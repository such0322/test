<?PHP
	////////////////////////////////////////////////////////////////////////////
	// チャットログの取り込み
//mylog(__LINE__);
//var_dump(__LINE__);
	
	// その前に未確定ログを削除
	$res = db_exec($con, "DELETE FROM l_chat_log WHERE is_fixed = 0 AND import_date < '" . date('Y-m-d') . "'");
	
	// 古いログも削除
	$res = db_exec($con, "DELETE FROM l_chat_log WHERE import_date < date_sub(now(), INTERVAL 6 month)");
	
	// 対象ログファイルの一覧を確保
	$log_files = glob("{$log_dir}/chat_{$exec_date}.log");
	
	// テンプレートとかの作成
	$tpl = "INSERT INTO l_chat_log(log_date, pftype, uid, user_id, chara_id, message, server_id, area_id, x, y, z, chat_type, chat_target, import_date, is_fixed) VALUES";
	$tpl_val = "('%s', %d, '%s', '%s', '%s', '%s', %d, %d, %d, %d, %d, %d, %d, now(), %d)";
	$values = array();
	$values_max = 50;
	
	// ログファイルの読み込み
//var_dump($log_files);
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
					$log_date,    // ログ発生日時
					$pftype,      // プラットフォーム区分
					$uid,         // UID
					$user_id,     // ユーザID
					$chara_id,    // キャラクターID
					$message,     // 発言内容
					$server_id,   // サーバ
					$area_id,     // エリア
					$x,           // x
					$y,           // y
					$z,           // z
					$chat_type,   // 発言種別
					$chat_target, // 発言対象
				) = explode("\t", trim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date || ! $message) {
					continue;
				}
				if (! preg_match('/^[0-9]*$/', $user_id) || ! preg_match('/^[0-9]*$/', $chara_id)) {
					continue;
				}
				if (! preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/', $log_date)) {
					continue;
				}
				
				// 特に細工せずそのまま格納
				$values[] = sprintf($tpl_val
				                  , db_qs($con, $log_date)
				                  , $pftype
				                  , db_qs($con, $uid)
				                  , db_qs($con, $user_id)
				                  , db_qs($con, $chara_id)
				                  , db_qs($con, $message)
				                  , $server_id
				                  , $area_id
				                  , $x
				                  , $y
				                  , $z
				                  , $chat_type
				                  , $chat_target
				                  , $is_fixed
				);
				
				// 一定数たまったら投入
				if (sizeof($values) > $values_max) {
					$sql = $tpl . implode(',', $values);
//var_dump($sql);
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
//var_dump($sql);
		$res = db_exec($con, $sql);
		$values = array();
	}
	