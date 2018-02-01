<?PHP
/**
 * plugin/ のファイル一覧を読み込みまとめて実行させる処理
 */
	
	// カレントディレクトリをスクリプトの場所に持っていく
	chdir(dirname(__FILE__));
	
	// 引数の受取
	$opts = getopt("d:t:s:");
	$log_dir  = $opts['d'];         // ログのあるディレクトリ、パスに * を付けて複数ディレクトリとなるのを許可する
	$is_fixed = intval($opts['t']); // 何日前のログを取り込むか、
	
	// 処理対象
	$allow_plugin_list = array(
		// ここに設定したら設定した項目のみ実行
	);
	$deny_plugin_list = array(
		// ここに設定したらそれ以外を実行
	);
	
	/////////////////////////////////////////////////////////////////////////
	// 以下処理
	
	// 処理対象プラグインを実行
	$files = glob('./plugin*/*.php');
	if ($files) {
		sort($files);
		$errors = array();
		foreach ($files as $f) {
			if (is_readable($f)) {
				
				// 処理対象とか除外対象とかあればここで対処
				if (sizeof($allow_plugin_list) > 0 and ! in_array(basename($f), $allow_plugin_list)) {
					continue;
				}
				if (sizeof($deny_plugin_list) > 0 and in_array(basename($f), $deny_plugin_list)) {
					continue;
				}
				
				$bt = microtime(true);
				// ここで文法チェック
				exec("php -l {$f} 2>&1", $o, $s);
				if ($s == 0) {
					$tpl = 'php %s/ngp_basic_log_import_fire.php -f "%s" -d "%s" -t %d 2>&1';
					$cmd = sprintf($tpl, dirname(__FILE__), $f, $log_dir, $is_fixed);
					
					$o = array();
					$res = exec($cmd, $o, $s);
					if ($s != 0) {
						// エラーが発生
						$errors[] = array(
							'date'   => date('Y-m-d H:i:s'), 
							'plugin' => $f, 
							'cmd'    => $cmd, 
							'output' => $o, 
						);
					}
				}
				$cost = microtime(true) - $bt;
				
				// 所要時間ログを出力
				$logstr = implode("\t", array(date('Y-m-d H:i:s'), $f, $cost)) . "\n";
				file_put_contents('../../log/plugincost_'.date('Ym').'.log', $logstr, FILE_APPEND);
			}
		}
		
		// エラーがあった時の対処
		if ($errors) {
			
			// TODO: ここでメールとかしたい
			var_dump($errors);
			
		}
		
	} else {
		
		// 処理するものが何もなかった場合
		
		// なにもしない
		
	}
	