<?PHP
	
	if (file_exists('../../var/es24/env/log_search_conf.php')) {
		include_once('../../var/es24/env/log_search_conf.php');
	} elseif (file_exists('../../../var/es24/env/log_search_conf.php')) {
		include_once('../../../var/es24/env/log_search_conf.php');
	} else {
		// 設定が読み込めないなら終わる
		return;
	}
	
	// DB接続を確保
//	$con = logdb_con(substr($exec_date, 6));
	$con = admin_con();
	
	// 保持期間は暫定で 100 日とする
	$log_expire_date = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') - 100, date('Y')));
	
	// 対象ファイル一覧を取得
	$files = glob("{$log_dir}/*_{$exec_date}.log");
	
	// それっぽいテーブル一覧を取得
	$sql = "show tables like 'lt\_%'";
	$arr = db_select($con, $sql);
	foreach ($arr AS $rec) {
		
		$tablename = array_shift($rec);
		$tables[$tablename] = $tablename;
		
		// 未確定ログを削除
		$sql = "DELETE FROM {$tablename} WHERE is_fixed = 0 AND log_date < '{$delete_border_date}'";
		$res = db_exec($con, $sql);
//var_dump($sql, $res, db_error($con));
		
		// 古いログもここで削除
		$sql = "DELETE FROM {$tablename} WHERE log_date < '{$log_expire_date}'";
		$res = db_exec($con, $sql);
//var_dump($sql, $res, db_error($con));
	}
	
	// 各ログファイルを取り込み
	foreach ($files AS $f) {
		
		$basefilename = substr(basename($f), 0, -13);
		if (! preg_match('/^[0-9a-zA-Z_]+$/', $basefilename)) {
			continue;
		}
		if (! isset($loglist[$basefilename])) {
			continue;
		}
		$conf = $loglist[$basefilename];
		
		// テーブル名を生成
		$tablename = 'lt_' . $basefilename;
		
		$cols = array();
		foreach ($conf['cols'] as $col => $c) {
			$cols[] = $col;
		}
		$cols[] = 'is_fixed';
		
		$tpl = "INSERT INTO {$tablename}(" . implode(',', $cols) . ") VALUES";
		$values = array();
		
		$fp = @fopen($f, 'r');
		if ($fp) {
			while ($rec = fgets($fp)) {
				
				$s = rtrim($rec, "\r\n");
				
				// 空ならスキップ
				if (strlen($s) == 0) {
					continue;
				}
				
				// 行を分断
				$a = explode("\t", $s);
				
				// 先頭項目が想定する日付時刻でなければスキップ
				if (! preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $a[0])) {
					continue;
				}
				// UIDは英数字64文字まで
				if (strlen($a[2]) > 64) {
					continue;
				}
				//  PF区分、ユーザID、キャラIDは空欄もしくは数値文字列のみ
				if (! preg_match('/^[0-9]*$/', $a[1])) {
					continue;
				}
				if (! preg_match('/^[0-9]*$/', $a[3])) {
					continue;
				}
				if (! preg_match('/^[0-9]*$/', $a[4])) {
					continue;
				}
				
				// VALUES の部分の作成
				$vals = array();
				reset($a);
				foreach ($conf['cols'] as $col => $c) {
					list(,$val) = each($a);
					if (strpos(strtolower($c['type']), 'int') !== false) {
						$vals[] = intval($val);
					} elseif (strpos(strtolower($c['type']), 'date') !== false) {
						$vals[] = "'{$val}'";
					} else {
						$vals[] = "'" . db_qs($con, $val) . "'";
					}
				}
				reset($a);
				$vals[] = $is_fixed;
				$values[] = '(' . implode(',', $vals) . ')';
				
				// レコードがたまってきたら挿入
				if (sizeof($values) > 50) {
					$sql = $tpl . implode(',', $values);
					$res = db_exec($con, $sql);
//var_dump($sql, $res, db_error($con));
					$values = array();
				}
			}
			
			// 入れてないものが残っていれば挿入
			if ($values) {
				$sql = $tpl . implode(',', $values);
				$res = db_exec($con, $sql);
//var_dump($sql, $res, db_error($con));
				$values = array();
			}
		}
		
	}
