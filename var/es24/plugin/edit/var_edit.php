<?PHP
//print_r($_SESSION);
//var_dump($_SESSION['__mylog']);
	
	require_once( 'env.php' );
	require_once( 'lib/common.php' );
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	
	$user_vars = user_vars_load(0);
	
	////////////////////////////////////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// 値の更新
	if (isset($_POST['f1_post'])) {
		
		$var_name  = $_POST['var_name'];
		$var_value = $_POST['var_value'];
		
		if (! isset($user_vars[$var_name])) {
			$user_vars[$var_name] = array();
		}
		$val = $var_value;
		if (is_array($user_vars[$var_name])) {
			// 配列なら改行とタブで切って書き直す
			$val = array();
			$a = explode("\n", $var_value);
			foreach ($a AS $b) {
				$c = trim($b);
				if ($c) {
					list($k, $v) = explode("\t", $c);
					if (strpos($k, '//') === 0 or strpos($k, '#') === 0) {   // コメント行の除外
						
					} else {
						$val[trim($k)] = $v;  // キーは trim() する
					}
				}
			}
		}
//mylog($var_name);mylog($val);
		user_vars_update($var_name, $val);
		
	}
	
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成 (ajax 経由のやつ)
	if (isset($_GET['ajax'])) {
		$json = array();
		
		if ($_POST['var_name']) {
			// 更新
			$data = $_POST['data'];
			
			$val = array();
			foreach ($data as $rec) {
				if ($rec[0] === 'null' and $rec[1] === 'null') {
					continue;
				}
				if ($rec[0] and $rec[1]) {
					$val[$rec[0]] = $rec[1];
				}
			}
			
			user_vars_update($_POST['var_name'], $val);
		}
		if ($_GET['var_name']) {
			// 取得
			$var_name = $_GET['var_name'];
			
			if (isset($user_vars[$var_name])) {
				$json = array(
					'var_name' => $var_name, 
					'data' => array(), 
				);
				foreach ($user_vars[$var_name] as $k => $v) {
					$json['data'][] = array($k, $v);
				}
			}
		}
		
		header('Content-Type: application/json');
		
		echo json_encode($json);
		
		return;
	}
	
	if ($_POST) {
		return ;
	}
	else {
		unset($_SESSION['__mylog']);
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成
	
	if (isset($user_vars['_comment']) && is_array($user_vars['_comment'])) {
		foreach ($user_vars['_comment'] as $k => $v) {
			if (! isset($user_vars[$k])) {
				$user_vars[$k] = array();
			}
		}
	}
	
	$psmarty->assign('user_vars', $user_vars);
?>
