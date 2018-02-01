<?PHP
	$root = dirname(__FILE__);
	chdir($root);
	
	// 共通処理および共通関数のよみこみ
	require_once('lib/es2.php');
	require_once('lib/func.php');
	
	// セッションの開始とか
	session_start();
	
	// ログイン情報が含まれていればログイン
	$key_user = 'login_user_es23';
	$key_pass = 'login_pass_es23';
	if (! isset($_SESSION['session_login_account'])) {
		if (isset($_GET[$key_user]) and isset($_GET[$key_pass])
		 and login_check($_GET[$key_user], $_GET[$key_pass])
		) {
			$_SESSION['session_login_account'] = $_GET[$key_user];
		}
	}
	
	// ログインされていれば開始
	if (isset($_SESSION['session_login_account']) and $_SESSION['session_login_account']) {
		if (isset($_GET['include_file'])) {
			$include_file = $_GET['include_file'];
			$menukey = (isset($_GET['menukey']) ? $_GET['menukey'] : '');
			
			if (substr($include_file, 0, 6) == 'plugin' && strpos($include_file, '..') === false && file_exists($include_file)) {
				
				// 必要なら操作ログの記録も行う
				if ($_SESSION['session_login_account'] && isset($_GET['f1_submit'])) {
					$log = array(
						$_SESSION['session_login_account'], 
						$_SERVER['REMOTE_ADDR'], 
						gethostbyaddr($_SERVER['REMOTE_ADDR']), 
						$_GET['menukey'], 
						$_GET['include_file'], 
						serialize($_POST), 
						serialize($_GET), 
					);
					es_log('post', $log);
				}
				
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
				
				// プラグインの実行
				include($include_file);
				
				// プラグイン内セッションの退避
				if ($LOCAL_SESSION) {
					$_SESSION['session_localsession'][$include_file] = $LOCAL_SESSION;
				}
			}
		}
	}
