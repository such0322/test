#!/usr/bin/php
<?PHP
	chdir(dirname(__FILE__));
	include('../../var/es24/env/env.php');
	include('../../var/es24/lib/func.php');
	$env = env();
	// 作業予定を記したファイル
	$task_file = $env['crontask_file'];
/*
ver dev3

*/
	
	// キーとコマンドの対応表
	$commands = array(
/*
		'ver'  => sprintf('sh %s/sync_var.sh %s %s %s', dirname(__FILE__), 'dev3', 'var/version/*', ''), 
		'conf' => sprintf('sh %s/sync_var.sh %s %s %s', dirname(__FILE__), 'dev3', 'var/version/*', ''), 
*/
	);
	$command_tpls = array(
		'ver'     => sprintf('sh %s/sync_var.sh %s %s %s', dirname(__FILE__), '%s', 'var/version', '%s'), 
		'conf'    => sprintf('sh %s/sync_var.sh %s %s %s', dirname(__FILE__), '%s', 'conf', '%s'), 
		'infoimg' => sprintf('sh %s/sync_var.sh %s %s %s', dirname(__FILE__), '%s', 'htdocs/app/images/infoimg', '%s'), 
		'lb'      => sprintf('sh %s/sync_var.sh %s %s %s', dirname(__FILE__), '%s', 'htdocs/app/images/lb', '%s'), 
		'sb'      => sprintf('sh %s/sync_var.sh %s %s %s', dirname(__FILE__), '%s', 'htdocs/app/images/sb', '%s'), 
		'bonus'   => sprintf('sh %s/sync_file.sh %s %s %s',dirname(__FILE__), '%s', 'res/global_bonus_rate.php', '%s'), 
		'gbrs'    => sprintf('sh %s/sync_file.sh %s %s %s',dirname(__FILE__), '%s', 'res/global_bonus_rates.php', '%s'), 
	);
	
	// 作業予定リストを開く
	if (file_exists($task_file) and filesize($task_file) > 0) {
		$b = file_get_contents($task_file);  // 厳密にはここでロックを行うべきだが複数回行っても問題ない処理以外はここで実行しないので気にしない
		file_put_contents($task_file, '');
		$a = explode("\n", $b);
		if ($a) {
			$t = array();
			foreach ($a AS $b) {
				$aa = explode(' ', strtolower(trim($b)));
				if (sizeof($aa) > 1 and isset($command_tpls[$aa[0]])) {
					if (sizeof($aa) == 2) {
						$cmd = sprintf($command_tpls[$aa[0]], $aa[1], '');
						$t[$cmd] = $cmd;
					} else {
						for ($i = 2;$i < sizeof($aa);$i++) {
							$cmd = sprintf($command_tpls[$aa[0]], $aa[1], $aa[$i]);
							$t[$cmd] = $cmd;
						}
					}
				}
			}
			
			foreach ($t AS $cmd) {
				passthru($cmd);
			}
		}
	}
	