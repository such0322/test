<?php
/*
	ページ移動により消去されないセッション (言い方を変えるとプラグイン内で使ってはいけないセッションキー)
		session_login_account
			ログイン中のユーザのID
		session_groups
			ログイン中のユーザの所属グループ
		session_db_select
			ログイン中のユーザのDB (多分 ES1.0 時代の名残)
		session_menu
			同一メニュー内に限り自由に使えるセッション
		session_localsession
			タブごとに設定可能なセッション
	
	magic_quote のこと
		基本 POST の内容は常に stripcslashes している、
		ただし <input type="checkbox" name="hoge[]" value="foo">
		のように配列で受け取りたいものに関しては処理対象外としている
	
	$LOCAL_SESSION
		タブごとに設定可能なセッション、寿命設定は特にしていないので注意
		index.php 内で session_localsession から読み込みプラグインから戻れば勝手に設定する
	
	note:
		パスワードの事
			基本受け取ったら直ぐに password_hash() でハッシュにしてから使う、
			クッキーに保存するのはハッシュの方にしておく
*/
	$root = dirname(__FILE__);
	chdir($root);
	
	// 共通処理および共通関数のよみこみ
	require_once('lib/es2.php');
	require_once('lib/func.php');
	
	// リンクしたい時の基本パス
	$menukey = $_GET['menukey'];
	$include_file = $_GET['include_file'];
	$get_url = sprintf('http://%s%s?menukey=%s&include_file=%s', $_SERVER['HTTP_HOST'], $_SERVER['PHP_SELF'], htmlspecialchars($_GET['menukey']), htmlspecialchars($_GET['include_file']));
	define('GET_URL', $get_url);
	
	// クッキーの名前
	$cookie_key_user = 'cookie_login_user_es23';
	$cookie_key_pass = 'cookie_login_pass_es23';
	
	// セッションの開始とアカウント・パスワードの取得
	session_start();
	
	//**************************************************************
	// 必要で可能ならクッキーを基にした自動ログイン
	if (($_SESSION['session_login_account'] == '' && $_COOKIE[$cookie_key_user] != '') && $_COOKIE[$cookie_key_pass] != ''
	 && login_check(base64_decode($_COOKIE[$cookie_key_user]), base64_decode($_COOKIE[$cookie_key_pass]))
	) {
		$_SESSION['session_login_account'] = base64_decode($_COOKIE[$cookie_key_user]);
		
		// ついでにログイン情報クッキーの更新も行なう
		setcookie($cookie_key_user, $_COOKIE[$cookie_key_user], time() + 3600*24*31 );
		setcookie($cookie_key_pass, $_COOKIE[$cookie_key_pass], time() + 3600*24*31 );
		
		// ログインログを書き出す
		$log = array(
			$_SERVER['REMOTE_ADDR'], 
			gethostbyaddr($_SERVER['REMOTE_ADDR']), 
			$_SESSION['session_login_account'], 
			'',   // 失敗ログではここにパスワードを入れる
			$_SERVER['HTTP_USER_AGENT'], 
		);
		es_log('login', $log);
	}
	//
	//**************************************************************
	
	
	//**************************************************************
	// 設定ファイル読み込みと確認
	$es_conf = es_conf();
/*
	if (! file_exists("{$root}/.htaccess") ) {
		print print_error('Setting mistake', ' .htaccess ファイルが読み込めません！');
		exit;
	}
*/
	$user_vars = user_vars_load();
	//
	//**************************************************************
	
	
	//**************************************************************
	// $_FILEをtmpに格納
/* [削除候補] es2 時代の処理、必要ないのでコメントアウト
	if ($_FILES) {
		foreach ($_FILES as $fileinfo => $value ){
			if ($_FILES[$fileinfo]['size'] != 0) {
				move_uploaded_file($_FILES[$fileinfo]['tmp_name'], "{$root}/tmp/{$fileinfo}.bin");
				chmod("{$root}/tmp/{$fileinfo}.bin", 0666);
				$_SESSION['esfile'][ $fileinfo ] = $value;
			}
		}
	}
	else {
		//ファイルがない時は破棄する
		unset($_SESSION['esfile']);
	}
*/
	//
	//**************************************************************
	
	
	//**************************************************************
	// ページ移動時にセッションの不要な変数をクリアする
	if ($_SERVER['HTTP_REFERER'] != '') {
		
		// NOTE: かつてはリファラー全体を見ていたが include_file だけを見るようにする
		$url = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($url['query'], $referer_args);
		
		// ページに違いがあればセッション変数を整理する
		if ($_GET['include_file'] != $referer_args['include_file']) {
			
			// 新しいページなのでアクセスログを生成する
			if ($_SESSION['session_login_account']) {
				$log = array(
					$_SESSION['session_login_account'], 
					$_SERVER['REMOTE_ADDR'], 
					gethostbyaddr($_SERVER['REMOTE_ADDR']), 
					$_GET['menukey'], 
					$_GET['include_file'], 
				);
				es_log('access', $log);
			}
			
			// 新しい _SESSION の中身を生成
			$new_session = array(
				'session_login_account'  => $_SESSION['session_login_account'], 
				'session_groups'         => $_SESSION['session_groups'], 
				'session_db_select'      => $_SESSION['session_db_select'], 
			);
			
			// menukey が同じ場合、メニューセッションとローカルセッションを維持する
			if ($referer_args['menukey'] == $_GET['menukey']) {
				$new_session['session_menu'] = $_SESSION['session_menu'];
				$new_session['session_localsession'] = $_SESSION['session_localsession'];
			}
			
			// 作成したセッションの中身を $_SESSION に戻す
			$_SESSION = $new_session;
		}
	}
	//
	//**************************************************************
	
	
	//**************************************************************
	// ログイン時の処理
	if (isset($_POST['account']) && ! isset($_SESSION['session_login_account'])) {
		$account = $_POST['account'];
		$password = $_POST['password'];
		
		
		// 受け取ったユーザのパスワードと受け取ったパスワードの一致をチェック
		// TODO: アカウントが存在するかのチェックが微妙に杜撰
		if ($password != '' && login_check($account, password_hash($password))) {
			// 認証突破につきログイン状態の生成
			$_SESSION['session_login_account'] = $account;
			
			// 古いログイン情報クッキーを削除
			if( $_COOKIE[$cookie_key_user] != '' ){
				setcookie($cookie_key_user, '', time() - 3600);
				setcookie($cookie_key_pass, '', time() - 3600);
			}
			
			// 新しいログイン情報クッキーを生成 (３１日間有効)
			setcookie($cookie_key_user, base64_encode($account), time() + 3600*24*31);
			setcookie($cookie_key_pass, base64_encode(password_hash($password)), time() + 3600*24*31);
			
			// 管理ツール利用者ログに書き出す内容を生成
			$log = array(
				$_SERVER['REMOTE_ADDR'], 
				gethostbyaddr($_SERVER['REMOTE_ADDR']), 
				$account, 
				'',   // 失敗ログではここにパスワードを入れる
				$_SERVER['HTTP_USER_AGENT'], 
			);
			es_log('login', $log);
		}
		else {
			// ログインしっぱいログに書き出す内容を生成
			$log = array(
				$_SERVER['REMOTE_ADDR'], 
				gethostbyaddr($_SERVER['REMOTE_ADDR']), 
				$account, 
				$password, 
				$_SERVER['HTTP_USER_AGENT'], 
			);
			es_log('login_failed', $log);
		}
	}
	//
	//**************************************************************
	
	
	//**************************************************************
	// ログアウト時の処理
	if (isset($_GET['logout'])) {
		//session_unregister('session_login_account');
		unset($_SESSION['session_login_account']);
		session_destroy();
		
		setcookie($cookie_key_user,  '', time() - 3600 );
		setcookie($cookie_key_pass, '', time() - 3600 );
	}
	//
	//**************************************************************
	
	
	// =============================================================================================
	
	
	//**************************************************************
	// これ以降表示周りの処理
	
	$smarty = smarty_new();
	
	// 常に入れておく値を設定
	// $smarty->assign('foo', $var);
	
	// 全体的に使う背景画像の指定があれば設定
	if (isset($es_conf['BackGroundImage'])) {
		$smarty->assign('bgimg', $es_conf['BackGroundImage']);
	}
	
	// 文字列リソースの読み込みと設定
	$strres = string_resource();
	$smarty->assign('strres', $strres['es2']);
	
	//$softname = $es_conf['SystemName'];
	$softname = '';
	
	// ログイン状態の確認
	if (isset($_SESSION['session_login_account'])) {
		// ログイン中である
		$smarty->assign('is_login', 1);
		
		// ログインしていればユーザ名を表示
		$smarty->assign('username', $_SESSION['session_login_account']);
		
		// プラグイン情報を読み込んで生成
		$plugins = array();
		$defined_plugins = $es_conf['Plugin'];
		$error_flag = 0;
		$groups = array_keys($_SESSION['session_groups']);  // TODO: get_group() の認証API構造対応
		//$groups = get_group($_SESSION['session_login_account']);
		
		foreach ($defined_plugins as $filekey => $filevalue) {
			
			// ユーザの所属グループ ($groups) の何れかの要素が $GroupList に含まれているかチェック
			$GroupList = $filevalue['GroupNames'];
			if ($groups != array_diff($groups, $GroupList)) {
				
				if (! is_dir("{$root}/plugin/{$filekey}")) {
					$plugin_error .= $filekey." はディレクトリがありません。<br>\n";
					$error_flag = 1;
				}
				else{
					
					// 該当グループ内で一番高い権限を取得
					$ar = 0;
					foreach ($_SESSION['session_groups'] AS $k => $v) {
						if (in_array($k, $GroupList) && $pm < $v) {
							$ar = $v;
						}
					}
					
// .htaccess は plugin の直下において各プラグインディレクトリには置かない
//					if (! file_exists("{$root}/plugin/{$filekey}/.htaccess")) {
//						$plugin_error .= $filekey." は .htaccess ファイルがありません。<br>\n";
//						$error_flag = 1;
//						continue;
//					}
					
					$plugins[$filekey] = array(
						'name'  => ($strres['PluginName'][$filekey] ? $strres['PluginName'][$filekey] : $filevalue['PluginName']), 
						'first' => $filevalue['FirstMenu'], 
						'menu'  => $filevalue['MainMenu'], 
						'bgimg' => $filevalue['BackGroundImage'], 
					);
				}
			}
		}
		
		if ($error_flag == 1) {
			print print_error('Setting mistake', $plugin_error );
			exit;
		}
		
		// menukey の指定が無ければ一番上のものにする
		if (! $menukey || ! isset($plugins[$menukey])) {
			list($menukey) = array_keys($plugins);
		}
		
		//-------------------------------------------------------------------------------------------------------
		// 左メニューの生成
		$leftmenu_items = array();
		foreach ($plugins as $mainkey => $plugin) {
			$mainvalue = $plugin['name'];
			$firstmenu = $plugin['first'];
			$firstkey = '';
			$is_active = 0;
			
			// 最初に表示する上タブが取得できれば設定する
			if (isset($plugin['menu'][$firstmenu])) {
				//$firstkey = get_parameter('FileName', $plugin['menu'][$firstmenu] );
				$firstkey = $plugin['menu'][$firstmenu]['FileName'];
			}
			
			// アクティブならフラグを立てる
			if ($menukey == $mainkey) {
				$is_active = 1;
				$softname .= " - {$mainvalue}";
				
				if ($plugin['bgimg']) {
					// 背景画像の指定あり
					$smarty->assign('bgimg', $plugin['bgimg']);
				}
			}
			
			// 一通りそろったので配列に押し込む
			$leftmenu_items[] = array(
				'is_active' => $is_active, 
				'menukey'   => $mainkey, 
				'firstkey'  => $firstkey, 
				'label'     => $mainvalue, 
			);
		}
		$smarty->assign('menuitems', $leftmenu_items);
		//
		//-------------------------------------------------------------------------------------------------------
		
		
		//-------------------------------------------------------------------------------------------------------
		// 上タブの生成
		$tabitems = array();
		$tabs = array();
		$tab_length = 0;
		$current_tab = '';
		
		foreach ($plugins[$menukey]['menu'] AS $subkey => $subvalue) {
			
			// ここで権限チェック
			if (isset($subvalue['GroupNames'])) {
				if ($groups == array_diff($groups, $subvalue['GroupNames'])) {
					continue;
				}
			}
			
			if (! isset($subvalue['FileName'])) {
				$subvalue['FileName'] = $subkey;
			}
			if (isset($strres['MenuString'][$subkey])) {
				$subvalue['MenuString'] = $strres['MenuString'][$subkey];
			}
			if (isset($strres['Notes'][$subkey])     ) {
				$subvalue['Notes']      = $strres['Notes'][$subkey];
			}
			
			$filename = $subvalue['FileName'];
			$title = $subvalue['MenuString'];
			$notes = $subvalue['Notes'];
			$is_active = 0;
			
			if ($include_file == "{$menukey}/{$filename}") {
				
				// 捜査対象タブのキーを確保しておく
				$current_tab = $subkey;
				
				// 操作対象のタブであればボタンの色が変わるのでフラグを立てる
				$is_active = 1;
				
				// 上タブのノートがあれば入れる
				$smarty->assign('notes', $notes);
				
				// 表題部分にタブの名前も追加
				$softname .= " - {$title}";
			}
			
			// include_file にパラメータが付いていた場合の対応
			// ちなみに PHP 5.0 以降なら str_replace('?', '&', $include_file, 1); で良い
			if (strpos($include_file, '?') > 0) {
				substr_replace($include_file, '&', strpos($include_file, '?'), 1);
			}
			
			$tab = array(
				'is_active' => $is_active, 
				'menukey'   => $menukey, 
				'filename'  => $filename, 
				'title'     => $title, 
				'notes'     => $notes, 
			);
			
			$tabs[] = $tab;
			if (($tab_length+=strlen($title)) > 160) {
				// 次の配列へ
				$tabitems[] = $tabs;
				$tabs = array();
				$tab_length = 0;
			}
		}
		
		// ループ後に上タブの残りがあれば格納
		if ($tabs) {
			$tabitems[] = $tabs;
			$tabs = array();
		}
		
		$smarty->assign('tabitems', $tabitems);
		
		//
		//-------------------------------------------------------------------------------------------------------
		
		//-------------------------------------------------------------------------------------------------------
		// 指定されたプラグインを動かす
		$mainstage = '';  // ←の変数に書き込みが行なわれる
		if (isset($_GET['include_file'])) {
			
			// 参照可能なファイルパスとしてしまう。
			$include_file = 'plugin/' . $_GET['include_file'];
			$template_file = $_GET['include_file'] . '.tpl';
			
			// ここで要求されたファイルを開く権限のチェック
			list(, $d, $f) = explode('/', $include_file);
			if (! isset($plugins[$d])
			 || ! preg_match('/^[a-zA-Z0-9\/_.-]+$/', $_GET['include_file'])
			 || strpos($include_file, '..') !== false
			 || ! file_exists($include_file)
			 
			 // ↓プラグイン単位権限チェック
			 || (isset($plugins[$menukey]['menu'][$f]['GroupNames']) && $groups == array_diff($groups, $plugins[$menukey]['menu'][$f]['GroupNames']))
			) {
				// エラーがあったと考えられるパターン
				$smarty->assign('is_plugin_error', 1);
			}
			else {
				
				// プラグイン内セッションの構築
				$LOCAL_SESSION = $_SESSION['session_localsession'][$include_file];
				if (is_null($LOCAL_SESSION)) {
					$LOCAL_SESSION = array();
				}
				
				// メニュー内セッションの構築
				$MENU_SESSION = $_SESSION['session_menu'];
				if (is_null($MENU_SESSION)) {
					$MENU_SESSION = array();
				}
				
				// プラグイン用のテンプレートエンジン
				$psmarty = smarty_new();
				$psmarty->assign('default_begin_date', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
				$psmarty->assign('default_end_date',   mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')));
				$psmarty->assign('menukey', $menukey);
				$psmarty->assign('file_basename', basename($include_file));
				$psmarty->assign('timezone_label', $es_conf['timezone']['label']);
				$psmarty->assign('is_change_tz', (is_fav_tz() ? 0 : 1));
				$psmarty->assign('strres', $strres['es2']);
				$psmarty->assign('pstrres', $strres[$current_tab]);
				$psmarty->assign('params', $user_vars['params']);
				
				// 本体側でデフォルトの jQuery バージョンを指定
				$smarty->assign('jquery_version', '1.6');
				
				// プラグインの実行
				include($include_file);
				
				// テンプレートファイルがあればそっちで実行
				if (file_exists('res/' . $template_file)) {
					$mainstage = $psmarty->fetch($template_file);
//var_dump($mainstage);
				}
				// プラグインの中身が生成した HTML を差し込む
				$smarty->assign('mainstage', $mainstage);
				
				// メニュー内セッションの退避
				if ($MENU_SESSION) {
					$_SESSION['session_menu'] = $MENU_SESSION;
				}
				
				// プラグイン内セッションの退避
				if ($LOCAL_SESSION) {
					$_SESSION['session_localsession'][$include_file] = $LOCAL_SESSION;
				}
			}
			
			// POST の場合は勝手にリダイレクト
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				
				// ついでに操作ログの記録も行う
				if ($_SESSION['session_login_account']) {
					$log = array(
						$_SESSION['session_login_account'], 
						$_SERVER['REMOTE_ADDR'], 
						gethostbyaddr($_SERVER['REMOTE_ADDR']), 
						$_GET['menukey'], 
						$_GET['include_file'], 
						serialize($_POST), 
					);
					es_log('post', $log);
				}
				
				//header("Location: http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}");
				header("Location: {$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}");
			}
		}
		
		//
		//-------------------------------------------------------------------------------------------------------
	}
	else{
		// ログイン中ではない
		$smarty->assign('is_login', 0);
	}
	
	// 表題の埋め込み
	$smarty->assign('softname', $softname);
	
	// ついでにサーバ時間も埋め込み
	$smarty->assign('svrdate', date('Y-m-d H:i:s'));
	
	//
	//------------------------------------------------------------------------------------------------------------------------
	
	
	//------------------------------------------------------------------------------------------------------------------------
	//以下HTML出力
	//
	
	$smarty->display('index.moz5.tpl');
	/* IE6対応とかで分けてたのを止める、問題なさそうなら削除
	// UA によってちょいと分ける
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE ') !== false) {
		$smarty->display('index.tpl');
	}
	elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla/5.0') !== false
	 || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false
	) {
		$smarty->display('index.moz5.tpl');
	}
	else {
		$smarty->display('index.tpl');
	}
	*/
	
	//
	//------------------------------------------------------------------------------------------------------------------------
	
