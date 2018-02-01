<?PHP
	// それぞれユーザが自分のパスワードを変える処理
	$root = dirname(__FILE__);
	chdir($root);
	
	include_once('lib/es2.php');
	
	do {
		
		// パスワード変更だったら
		if (! isset($_POST['_es_change_password'])) {
			break;
		}
		
		// 入力有無のチェック
		if (strlen($_POST['user']) == 0) {
			break;
		}
		if (strlen($_POST['cur']) == 0) {
			break;
		}
		if (strlen($_POST['new']) == 0) {
			break;
		}
		if (strlen($_POST['cnf']) == 0) {
			break;
		}
		
		// 入力結果の受取
		$user = trim($_POST['user']);
		$pass = $_POST['cur'];
		$new  = $_POST['new'];
		$cnf  = $_POST['cnf'];
		
		// まず現在のパスワードを確認
		if (password_hash($pass) != get_password($user)) {
			break;
		}
		
		// 新しいパスワードの一致を確認
		if ($new != $cnf) {
			break;
		}
		
		// パスワードを更新
		update_user($user, password_hash($new), null);
		
		// 終了
		print "OK";
		exit;
		
	} while (0);
	
	echo "NG";
?>
