<?PHP
//print_r($_SESSION);
//var_dump($_SESSION["__mylog"]);
	
	/////////////////////////////////////////////
	// パーツのインクルード
	
	/////////////////////////////////////////////
	// 広域変数定義
	
	$group_list = get_group_list();
	$user_list = parse_users();
	$msg = $_SESSION['msg'];
	
	
	/////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// 新規ユーザ登録
	if (isset($_POST['f1_submit'])) {
		$user = $_POST['user'];
		$pass = $_POST['pass'];
		$group = $_POST['group'];
		
		$result_type = '';
		do {
			if (isset($user_list[$user])) {
				$result_type = 'error_username_exists';
				break;
			}
			if (strlen($user) < 3) {
				$result_type = 'error_username_short';
				break;
			}
			if (! preg_match('/^[-_0-9a-zA-Z]+$/', $user)) {
				$result_type = 'error_username_invalid';
				break;
			}
			$res = check_password($pass);
			if ($res == -1) {
				$result_type = 'error_password_short';
				break;
			}
			if ($res == -2) {
				$result_type = 'error_password_invalid';
				break;
			}
			
			// チェックしたのでパスワードはハッシュの方に直す
			$pass = password_hash($pass);
			
			add_user($user, $pass, $group);
			$result_type = 'success_user_regist';
			
		} while(0);
		
		$_SESSION['msg'] = array('user' => $user, 'type' => $result_type);
	}
	
	// ユーザ情報更新
	if (isset($_POST['f2_submit'])) {
		$msg = array(
			'type' => 'chdata', 
			'chpass_error' => array(), 
			'chpass_success' => array(), 
			'chgrp' => array(), 
		);
		
		if ($_POST['operate'] == 'update') {
			foreach ($user_list AS $user => $ud) {
				
				if (strlen($_POST['pass'][$user]) > 0) {
					$pass = $_POST['pass'][$user];
					if (check_password($pass) == 0) {
						$user_list[$user]['pass'] = password_hash($_POST['pass'][$user]);
						
						$msg['chpass_success'][] = $user;
					}
					else {
						$msg['chpass_error'][] = $user;
					}
				}
				
				if ($_POST['group_update'][$user]) {
					if ($_POST['group'][$user]) {
						$user_list[$user]['group'] = $_POST['group'][$user];
					}
					else {
						$user_list[$user]['group'] = array();
					}
					
					$msg['chgrp'][] = $user;
				}
			}
			
			update_users($user_list);
		}
		elseif ($_POST['operate'] == 'delete') {
			$msg = array(
				'type' => 'unregist', 
				'deletes' => array(), 
			);
			
			if (is_array($_POST['delete'])) {
				foreach ($_POST['delete'] AS $delete_user) { 
					if (isset($user_list[$delete_user])) {
						unset($user_list[$delete_user]);
						$msg['deletes'][] = $delete_user;
					}
				}
				update_users($user_list);
			}
		}
		
		$_SESSION['msg'] = $msg;
	}
	
	if ($_POST) {
		return;
	}
	else {
		unset($_SESSION['msg']);
		unset($_SESSION["__mylog"]);
	}
	
	
	// 前画面からの引継ぎ情報があればここで設定
	$psmarty->assign('msg', $msg);
	
	/////////////////////////////////////////////
	// 表示内容作成、新規ユーザ追加
	
	$group_auths = array();
	foreach ($es_conf['Plugin'] as $filekey => $filevalue) {
		// 権限のあるグループリストのよみこみ
		foreach ($filevalue['GroupNames'] AS $g) {
			//$group_auths[trim($g)][] = $filevalue['PluginName'];
			$group_auths[trim($g)][] = $es_conf['Plugin'][$filekey]['PluginName'];
		}
	}
	
	$groups = array();
	foreach ($group_list AS $group) {
		$groups[$group] = (isset($group_auths[$group]) ? implode(',', $group_auths[$group]) : '');
	}
	$psmarty->assign('groups', $groups);
	
	/////////////////////////////////////////////
	// 表示内容作成、ユーザリスト
	
	$psmarty->assign('group_list', $group_list);
	$psmarty->assign('user_list', $user_list);
	
?>
