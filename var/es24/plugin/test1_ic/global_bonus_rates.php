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
		'start_date' => '開始日時', 
		'end_date' => '終了日時', 
		
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
	
	$global_bonus_cols = array(
		'start_date'              => 'datetime', 
		'end_date'                => 'datetime', 
		
		'quest_mag'               => 'int', 
		'quest_card_exp'          => 'int', 
		'quest_player_exp'        => 'int', 
		'quest_item'              => 'int', 
		
		'otetsudai_mag'           => 'int', 
		'otetsudai_bit'           => 'int', 
		'otetsudai_otetsudai_exp' => 'int', 
		'otetsudai_player_exp'    => 'int', 
		'otetsudai_friend_point'  => 'int', 
		'otetsudai_item'          => 'int', 
	);
	
	/**
	 * @param array $rec
	 * @param array $global_bonus_cols
	 * @return array
	 */
	function valid($rec, $global_bonus_cols) {
		$ret = array();
		$cases = array(
			'int' => '/^[0-9]+$/', 
			'datetime' => '/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/', 
		);
		foreach ($global_bonus_cols as $col => $type) {
			if (! isset($rec[$col]) or ! isset($cases[$type])) {
				return;
			}
			if (! preg_match($cases[$type], $rec[$col])) {
				return;
			}
			$ret[$col] = $rec[$col];
		}
		return $ret;
	}
	
	
	// あれば res の読み込み
	$global_bonus_filepath = '/res/global_bonus_rates.php';
	$global_bonus_res = $basepath . $global_bonus_filepath;
	$global_bonus_rates = (file_exists($global_bonus_res) ? include($global_bonus_res) : array());
	
	/////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	if (isset($_POST['f1_submit'])) {
		// 編集対象バージョン選択
		$LOCAL_SESSION['edit_version'] = $_POST['edit_version'];
	}
	if (isset($_POST['f2_submit'])) {
		if (is_array($_POST['update_vers']) and sizeof($_POST['update_vers']) > 0) {
			// 既存の global_bonus を読み込み
			$f = str_replace('{version}', ($edit_version > 0 ? ".{$edit_version}" : ""), $env['basepath']) . $global_bonus_filepath;
			$a = (file_exists($f) ? include($f) : array());
			
			// global_bonus の更新
			if (isset($_POST['update'])) {
				foreach ($_POST['update'] as $k => $v) {
					
					$res = valid($v, $global_bonus_cols);
					if ($res) {
						$res['id'] = $k;
						$a[$k] = $res;
					}
				}
			}
			if (isset($_POST['insert'])) {
				$res = valid($_POST['insert'], $global_bonus_cols);
				if ($res) {
					$res['id'] = max(array_keys($global_bonus_cols)) + 1;
					$a[$res['id']] = $res;
				}
			}
			$global_bonus_str = '<?php return ' . var_export($a, true) . ';';
			
			// 更新対象バージョン全部に依頼
			$update_vers = array();
			foreach ($_POST['update_vers'] as $ver) {
				if (preg_match('/^[0-9]{3,4}$/', $ver)) {
					$f = str_replace('{version}', ".{$ver}", $env['basepath']) . $global_bonus_filepath;
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
				settask('gbrs', $update_vers);
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
	
	// マスタの状況
	$psmarty->assign('global_bonus_rates', $global_bonus_rates );
	
	
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
