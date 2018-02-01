<?PHP
//print_r($_SESSION);
//var_dump($_SESSION['__mylog']);
	
	require_once('env.php');
	require_once('lib/func.php');
	
	////////////////////////////////////////////////////////////////////////////
	// パーツのインクルード
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	$user_vars = user_vars_load();
	
	// var/manage のパス
	$env = env();
	$edit_version = $LOCAL_SESSION['edit_version'];
	$basepath_plain = str_replace('{version}', "", $env['basepath']);
	$basepath_ver = str_replace('{version}', ($edit_version > 0 ? ".{$edit_version}" : ""), $env['basepath']);
	
	$version_json_path = $basepath_plain . '/var/version/version.json';
	$version_android_json_path = $basepath_plain . '/var/version/version.android.json';
	$version_ios_json_path = $basepath_plain . '/var/version/version.ios.json';
	$mainte_conf_path = $basepath_ver . '/conf/mainte.php';
	
	$versions = $user_vars['version'];
	
	////////////////////////////////////
	// バージョン設定とその中身
	
	$version_conf = (file_exists($version_json_path) ? json_decode(file_get_contents($version_json_path), true) : array());
	$version_android_conf = (file_exists($version_android_json_path) ? json_decode(file_get_contents($version_android_json_path), true) : array());
	$version_ios_conf = (file_exists($version_ios_json_path) ? json_decode(file_get_contents($version_ios_json_path), true) : array());
	
	////////////////////////////////////
	// メンテお知らせファイルとその中身
	
	$mainte_conf = (file_exists($mainte_conf_path) ? include($mainte_conf_path) : array());
	
	$mainte_flg    = $mainte_conf['is_mainte'];
	$is_close      = $mainte_conf['is_close'];
	$mainte_label  = $mainte_conf['mainte_label'];
	$mainte_info   = $mainte_conf['mainte_info'];
	$pass_accounts = (is_array($mainte_conf['pass_accounts']) ? $mainte_conf['pass_accounts'] : array());
	
	/////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	if (isset($_POST['f2_submit'])) {
		// バージョンとか更新
		$svrvers = array();
		$a = explode("\n", $_POST['svrvers']);
		foreach ($a as $l) {
			list($appver, $svrver) = preg_split('/(\s|:)+/', $l);
			$svrvers[$appver] = $svrver;
		}
		$versioninfo = rtrim($_POST['versioninfo']);
		$versioninfo = str_replace("\r\n", '|', $versioninfo);
		$versioninfo = str_replace("\n", '|', $versioninfo);
		$version_conf = array(
			'version' => $_POST['version'], 
			'versioninfo' => $versioninfo, 
			'guide_enable' => $_POST['guide_enable'], 
			'guide_url' => $_POST['guide_url'], 
			'guide_img' => $_POST['guide_img'], 
			'guide_comment' => $_POST['guide_comment'], 
			'svrvers' => $svrvers, 
		);
		
		file_put_contents($version_json_path, json_encode($version_conf));
		chmod($version_json_path, 0664);
		
		$_SESSION['result'] = 'f2ok';
	}
	if (isset($_POST['f2a_submit'])) {
		// バージョンとか更新
		$svrvers = array();
		$a = explode("\n", $_POST['svrvers']);
		foreach ($a as $l) {
			list($appver, $svrver) = preg_split('/(\s|:)+/', $l);
			if ($svrver) {
				$svrvers[$appver] = $svrver;
			}
		}
		$versioninfo = rtrim($_POST['versioninfo']);
		$versioninfo = str_replace("\r\n", '|', $versioninfo);
		$versioninfo = str_replace("\n", '|', $versioninfo);
		$version_android_conf = array(
			'version' => $_POST['version'], 
			'versioninfo' => $versioninfo, 
			'guide_enable' => $_POST['guide_enable'], 
			'guide_url' => $_POST['guide_url'], 
			'guide_img' => $_POST['guide_img'], 
			'guide_comment' => $_POST['guide_comment'], 
			'svrvers' => $svrvers, 
		);
		
		// バージョン設定の更新
		file_put_contents($version_android_json_path, json_encode($version_android_conf));
		chmod($version_android_json_path, 0664);
		
		// crontask.txt にタスクの出力
		settask('ver', array(''));  // 要素が空文字 (=バージョン指定なし) の値一つ入った配列をバージョンとして指定
		
		$_SESSION['result'] = 'f2aok';
	}
	if (isset($_POST['f2i_submit'])) {
		// バージョンとか更新
		$svrvers = array();
		$a = explode("\n", $_POST['svrvers']);
		foreach ($a as $l) {
			list($appver, $svrver) = preg_split('/(\s|:)+/', $l);
			if ($svrver) {
				$svrvers[$appver] = $svrver;
			}
		}
		$versioninfo = rtrim($_POST['versioninfo']);
		$versioninfo = str_replace("\r\n", '|', $versioninfo);
		$versioninfo = str_replace("\n", '|', $versioninfo);
		$version_ios_conf = array(
			'version' => $_POST['version'], 
			'versioninfo' => $versioninfo, 
			'guide_enable' => $_POST['guide_enable'], 
			'guide_url' => $_POST['guide_url'], 
			'guide_img' => $_POST['guide_img'], 
			'guide_comment' => $_POST['guide_comment'], 
			'svrvers' => $svrvers, 
		);
		
		// バージョン設定の更新
		file_put_contents($version_ios_json_path, json_encode($version_ios_conf));
		chmod($version_ios_json_path, 0664);
		
		// crontask.txt にタスクの出力
		settask('ver', array(''));  // 要素が空文字 (=バージョン指定なし) の値一つ入った配列をバージョンとして指定
		
		$_SESSION['result'] = 'f2iok';
	}
	if (isset($_POST['f3_submit'])) {
		// メンテとか更新
		
		$mainte_info = rtrim($_POST['mainte_info']);
		$mainte_info = str_replace("\r\n", '|', $mainte_info);
		$mainte_info = str_replace("\n", '|', $mainte_info);
		$mainte_label = rtrim($_POST['mainte_label']);
		$mainte_conf = array(
			'is_mainte' => intval($_POST['mainte_flg']), 
			'is_close' => intval($_POST['is_close']), 
			'mainte_info' => $mainte_info, 
			'mainte_label' => $mainte_label, 
			'pass_accounts' => explode("\r\n", rtrim($_POST['pass_accounts'])), 
		);
		
		if (is_array($_POST['update_vers']) and sizeof($_POST['update_vers']) > 0) {
			foreach ($_POST['update_vers'] as $ver) {
				$s = '<?php return ' . var_export($mainte_conf, true) . ';';
				$filepath = str_replace('{version}', ($ver ? ".{$ver}":''), $env['basepath']) . '/conf/mainte.php';
				file_put_contents($filepath, $s);
				chmod($filepath, 0664);
			}
			
			// crontask.txt にタスクの出力
			settask('conf', $_POST['update_vers']);
			
			$_SESSION['result'] = 'f3ok';
		} else {
			$_SESSION['result'] = 'f3ng';
		}
	}
	if (isset($_POST['f3v_submit'])) {
		// 編集対象バージョン選択
		$LOCAL_SESSION['edit_version'] = $_POST['edit_version'];
	}
	if (isset($_POST['f4_submit'])) {
		// セッション削除
		
		$sql = '';
		if ($_POST['pf_type'] === 'all') {
			$sql = 'TRUNCATE session';
		} elseif (preg_match('/^[0-9]+$/', $_POST['pf_type'])) {
			$sql = sprintf("DELETE FROM session WHERE pf_type = %d", $_POST['pf_type']);
		}
		
		if ($sql) {
			$connect_confs = \Mag\DB::getConnectConf('session');
			$truncated = array();
			foreach ($connect_confs as $db) {
				$k = implode(':', array($db['host'], $db['user'], $db['pass'], $db['name']));
				if (! isset($truncated[$k])) {
					$con = db_con($db['host'], $db['user'], $db['pass'], $db['name']);
					$res = db_exec($con, $sql);
					$truncated[$k] = $k;
				}
			}
			$_SESSION['result'] = 'f4ok';
		}
	}
	
	if ($_POST) {
		return ;
	}
	else {
		unset($_SESSION['__mylog']);
	}
	
	/////////////////////////////////////////////
	// 表示内容作成
	
	// 何かの結果が設定result表示に載せる
	if (isset($_SESSION['result'])) {
		$psmarty->assign('result', $_SESSION['result']);
		unset($_SESSION['result']);
	}
	
	// バージョン
	$psmarty->assign('versions', $versions );
	$psmarty->assign('edit_version', $edit_version );
	
	// バージョン設定
	$psmarty->assign('version', $version_conf );
	$psmarty->assign('version_android', $version_android_conf );
	$psmarty->assign('version_ios', $version_ios_conf );
	
	// メンテ告知関連
	$psmarty->assign('mainte_flg',  $mainte_flg  );
	$psmarty->assign('is_close',  $is_close);
	$psmarty->assign('mainte_label', $mainte_label );
	$psmarty->assign('mainte_info', $mainte_info );
	
	// メンテパス端末リスト
	$psmarty->assign('pass_accounts', implode("\n", $pass_accounts));
	
	
	/////////////////////////////////////////////
	// 関数とか
	
	/** crontask に登録する */
	function settask($cmd, $vers) {
		$env = env();
		
		$task = '';
		$p = $env['host_prefix']?:$env['env'];
		foreach ($vers as $ver) {
			$task .= "{$cmd} {$p} {$ver}\n";
		}
		
		file_put_contents($env['crontask_file'], $task, FILE_APPEND);
	}
	
	/** 転送処理が残っているか調べる */
	function is_lefttask() {
		$env = env();
		
		// 素直にファイルが空かそうでないかで確認
		return (filesize($env['crontask_file']) > 0 ? 1 : 0);
		
		/* 指定したコマンドがあるかどうかの確認
		$crontask_cmd  = 'conf';
		$crontask_cmd  = 'ver';
		$crontask_file = $env['crontask_file'];
		$c = file_get_contents($crontask_file);
		if (strpos($c, $crontask_cmd) !== false) {
			return true;
		}
		return false;
		*/
	}
