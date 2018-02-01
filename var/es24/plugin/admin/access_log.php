<?PHP
//print_r($_SESSION);
//var_dump($_SESSION["__mylog"]);
	
	/////////////////////////////////////////////
	// パーツのインクルード
	
	
	/////////////////////////////////////////////
	// 広域変数定義
	$es_conf = es_conf();
	$log_types = array(
		'access', 
		'post', 
	);
	$where = array(
		'type' => 'access', 
		'user' => '', 
		'date' => date('Y-m-d'), 
		'user_mt' => 'full', 
	);
	if (isset($_SESSION['where'])) {
		$where = $_SESSION['where'];
	}
	
	/////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// 検索条件の設定
	if (isset($_POST['f1_submit'])) {
		$_SESSION['where'] = array(
			'type' => $_POST['type'], 
			'user' => $_POST['user'], 
			'date' => $_POST['date'], 
			'user_mt' => $_POST['user_mt'], 
		);
	}
	
	if ($_POST) {
		return;
	}
	else {
		unset($_SESSION['msg']);
		unset($_SESSION["__mylog"]);
	}
	
	/////////////////////////////////////////////
	// 表示内容作成、検索フォーム
	
	$psmarty->assign('where', $where);
	
	/////////////////////////////////////////////
	// 表示内容作成、ログ
	$access_logs = array();
	
	// 検索条件が正しいか確認
	if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $where['date']) && in_array($where['type'], $log_types)) {
		list($y, $m, $d) = explode('-', $where['date']);
		if (checkdate($m, $d, $y)) {
			
			// ログファイルへのパスの作成
			$filepath = sprintf('logs/%s_%d%02d%02d.log', $where['type'], $y, $m, $d);
			if (file_exists($filepath)) {
				$arr = explode("\n", file_get_contents($filepath));
				foreach ($arr AS $log) {
					list(
						$datetime, 
						$account, 
						$ip, 
						$host, 
						$menukey, 
						$include_file, 
					) = explode("\t", trim($log));
					
					if (! $datetime) {
						continue;
					}
					
					// ユーザ名での絞りがあればそれを適用
					if (strlen($where['user']) > 0) {
						if ($where['user_mt'] == 'head') {
							// 行頭一致
							if (strpos($account, $where['user']) !== 0) {
								continue;
							}
						}
						elseif ($where['user_mt'] == 'part') {
							// 部分一致
							if (strpos($account, $where['user']) === false) {
								continue;
							}
						}
						else {
							// 完全一致
							if ($account != $where['user']) {
								continue;
							}
						}
					}
					
					// ページ名の変換
					$menu = $es_conf['Plugin'][$menukey]['PluginName'];
					list(, $f) = explode('/', $include_file);
					$tab = $es_conf['Plugin'][$menukey]['MainMenu'][$f]['MenuString'];
					
					$access_logs[] = array(
						'date' => $datetime, 
						'user' => $account, 
						'menu' => $menu, 
						'tab'  => $tab, 
					);
				}
			}
			else {
				$psmarty->assign('error', 'n');
			}
		}
		else {
			$psmarty->assign('error', 'd');
		}
	}
	else {
		$psmarty->assign('error', 'w');
	}
	
	$psmarty->assign('access_logs', $access_logs);
?>
