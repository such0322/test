<?PHP
	////////////////////////////////////////////////////////////////////////////
	// ミッション達成ログの取り込み
//mylog(__LINE__);
//var_dump(__LINE__);
	
	// 対象ログファイルの一覧を確保
	$log_files = glob("{$log_dir}/mission_clear_{$exec_date}.log");
	
	// テンプレートとかの作成
	$tpl = "INSERT INTO mission_clear(log_date, player_id, mission_id, level, regist_date) VALUES";
	$tpl_val = "('%s', %d, %d, %d, '%s')";
	$values = array();
	$values_max = 50;
	
	// ログファイルの読み込み
//var_dump($log_files);
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
			while (! feof($fp)) {
				$log = rtrim(fgets($fp), "\r\n");
				
				// 空行は次へ
				if (! $log) {
					continue;
				}
				
				// まず分断する
				list(
					$log_date,    // ログ発生日時
					$pftype,      // プラットフォーム区分
					$uid,         // UID
					$user_id,     // ユーザID
					$chara_id,    // キャラクターID
					$mission_id,  // ミッションID
					$level,       // 達成時のプレイヤーのレベル
					$regist_date, // プレイヤーの会員登録日時
				) = explode("\t", $log);
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				if (! preg_match('/^[0-9]*$/', $user_id) || ! preg_match('/^[0-9]*$/', $chara_id)) {
					continue;
				}
				if (! preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/', $log_date)) {
					continue;
				}
				
				// 多少削ってずそのまま格納
				$values[] = sprintf($tpl_val
				                  , db_qs($con, $log_date)
				                  , $user_id
				                  , $mission_id
				                  , $level
				                  , db_qs($con, $log_date)
				);
				
				// 一定数たまったら投入
				if (sizeof($values) > $values_max) {
					$sql = $tpl . implode(',', $values);
					$res = db_exec($con, $sql);
//var_dump($sql, $res);
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
//var_dump($sql, $res);
		$values = array();
	}
	