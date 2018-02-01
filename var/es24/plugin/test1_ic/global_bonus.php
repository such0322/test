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
	$versions = $user_vars['version'];
	
	// var/manage のパス
	$env = env();
	$edit_version = $LOCAL_SESSION['edit_version'];
	$basepath = str_replace('{version}', ($edit_version > 0 ? ".{$edit_version}" : ""), $env['basepath']);
	
	$global_bonus_labels = array(
		'quest_mag' => 'クエスト報酬、金 (百分率)', 
		'quest_card_exp' => 'クエスト報酬、カード経験値 (百分率)', 
		'quest_player_exp' => 'クエスト報酬、プレイヤー経験値 (百分率)', 
		'quest_item' => 'クエスト報酬、アイテム数 (百分率)', 
		
		'otetsudai_mag' => 'おてつだい報酬、金 (百分率)', 
		'otetsudai_bit' => 'おてつだい報酬、かけら (百分率)', 
		'otetsudai_otetsudai_exp' => 'おてつだい報酬、おてつだい経験値 (百分率)', 
		'otetsudai_player_exp' => 'おてつだい報酬、プレイヤー経験値 (百分率)', 
		'otetsudai_friend_point' => 'おてつだい報酬、フレンドポイント (百分率)', 
		'otetsudai_item' => 'おてつだい報酬、アイテム数 (百分率)', 
	);
	
	// あれば res の読み込み
	$global_bonus_res = $basepath . '/res/global_bonus_rate.php';
	$global_bonus = (file_exists($global_bonus_res) ? include($global_bonus_res) : array_fill_keys(array_keys($global_bonus_labels), 100));
	
	/////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	if (isset($_POST['f1_submit'])) {
		// 編集対象バージョン選択
		$LOCAL_SESSION['edit_version'] = $_POST['edit_version'];
	}
	if (isset($_POST['f2_submit'])) {
		if (is_array($_POST['update_vers']) and sizeof($_POST['update_vers']) > 0) {
			// 既存の global_bonus を読み込み
			$f = str_replace('{version}', ($edit_version > 0 ? ".{$edit_version}" : ""), $env['basepath']) . '/res/global_bonus_rate.php';
			$a = (file_exists($f) ? include($f) : array_fill_keys(array_keys($global_bonus_labels), 100));
			
			// global_bonus の更新
			foreach ($global_bonus_labels as $key => $label) {
				$v = $_POST[$key];
				if (preg_match('/^[1-9][0-9]*$/', $v)) {
					$a[$key] = $v;
				}
			}
			$global_bonus_str = '<?php return ' . var_export($a, true) . ';';
			
			// 更新対象バージョン全部に依頼
			$update_vers = array();
			foreach ($_POST['update_vers'] as $ver) {
				if (preg_match('/^[0-9]{3,4}$/', $ver)) {
					$f = str_replace('{version}', ".{$ver}", $env['basepath']) . '/res/global_bonus_rate.php';
					$e = file_exists($f);
					file_put_contents($f, $global_bonus_str);
					if (! $e) {
						chmod($f, 0664);
					}
					$update_vers[] = $ver;
				}
			}
			if ($update_vers) {
				// crontask.txt にタスクの出力
				settask('bonus', $update_vers);
			}
			$_SESSION['result'] = 'f2ok';
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
	
	// ラベル
	$psmarty->assign('global_bonus_labels', $global_bonus_labels );
	$psmarty->assign('global_bonus', $global_bonus );
	
	
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
