<?PHP
//print_r($_SESSION);
//var_dump($_SESSION['__mylog']);
	
	require_once( 'env.php' );
	require_once( 'lib/common.php' );
	
	////////////////////////////////////////////////////////////////////////////
	// パーツのインクルード
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	$con = admin_con();
	$mainstage = "";
	
	$max = 100;
	
	$uids = (isset($LOCAL_SESSION['uids']) ? $LOCAL_SESSION['uids'] : '');
	
	/////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	if (isset($_POST['f1_submit'])) {
		$LOCAL_SESSION['uids'] = $_POST['uids'];
	}
	
	/////////////////////////////////////////////
	// 表示内容作成、検索・抽出条件フォーム
	$psmarty->assign('uids', $uids);
	if (! $uids) {
		return;
	}
	
	/////////////////////////////////////////////
	// 表示内容作成、結果リスト
	
	// 処理対象UIDリストの生成
	$a = explode("\n", $uids);
	if (sizeof($a) > $max) {
		$psmarty->assign('uids_sizeover', 1);
		return;
	}
	
	// 一度 uid の重複を除外してからエスケープして配列に押し込む
	$uid_list = array();
	foreach ($a AS $rec) {
		$uid = trim($rec);
		if ($uid) {
			$uid_list[$uid] = $uid;
		}
	}
	$a = array();
	foreach ($uid_list AS $uid) {
		$a[] = "'" . db_qs($con, $uid) . "'";
	}
	
	// 処理対象のデータを取得
	$results = array();
	$u = implode(',', $a);
	$sql = "SELECT unique_id,user_id FROM d_unique_id WHERE unique_id IN ({$u}) ";
	$arr = db_select($con, $sql);
	foreach ($arr AS $rec) {
		
		$results[$rec['unique_id']] = $rec['user_id'];
		
		// 処理済 uid を除去
		unset($uid_list[$rec['unique_id']]);
	}
	
	$psmarty->assign('results', $results);
	$psmarty->assign('nokori', $uid_list);
	