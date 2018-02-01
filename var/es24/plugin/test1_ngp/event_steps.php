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
	
	$user_vars = user_vars_load();
	$event_id_list = $user_vars['event_id'];
	$step_names = $user_vars['event_steps'];
	
	$order_list = array('step', 'count', 'total');
	$default_order = $order_list[0];
	
	// グラフの最大横幅
	$graph_max_width = 300;
	
	$begin_date = (isset($LOCAL_SESSION['begin_date']) ? $LOCAL_SESSION['begin_date'] : '');
	$end_date   = (isset($LOCAL_SESSION['end_date'])   ? $LOCAL_SESSION['end_date']   : '');
	$event_id = (isset($LOCAL_SESSION['event_id']) ? $LOCAL_SESSION['event_id'] : 0);
	
	$order = (in_array($LOCAL_SESSION['order'], $order_list) ? $LOCAL_SESSION['order'] : $default_order);
	$desc  = ($LOCAL_SESSION['desc'] == 'desc' ? 'desc' : 'asc');
	
	////////////////////////////////////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// 検索条件の変更
	if (isset($_POST['f1_submit'])) {
		
		// 検索条件の確保
		if (preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $_POST['begin_date'])) {
			$LOCAL_SESSION['begin_date'] = $_POST['begin_date'];
		}
		else {
			unset($LOCAL_SESSION['begin_date']);
		}
		if (preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $_POST['end_date'])) {
			$LOCAL_SESSION['end_date']   = $_POST['end_date'];
		}
		else {
			unset($LOCAL_SESSION['end_date']);
		}
		
		// 検索条件の確保
		if (isset($_POST['event_id'])) {
			$LOCAL_SESSION['event_id'] = $_POST['event_id'];
			$LOCAL_SESSION['f1_search'] = 1;
		}
		else {
			unset($LOCAL_SESSION['event_id']);
			unset($LOCAL_SESSION['f1_search']);
		}
	}
	if (isset($_POST['f2_submit'])) {
		if (in_array($_POST['order'], $order_list)) {
			$LOCAL_SESSION['order'] = $_POST['order'];
		}
		if (in_array($_POST['desc'], array('asc', 'desc'))) {
			$LOCAL_SESSION['desc'] = $_POST['desc'];
		}
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、検索・抽出条件フォーム
	
	$psmarty->assign('begin_date', $begin_date);
	$psmarty->assign('end_date', $end_date);
	$psmarty->assign('event_id_list', $event_id_list);
	$psmarty->assign('event_id', $event_id);
	
	// 検索条件が指定されていない場合はここで終わらせる
	if (! isset($LOCAL_SESSION['f1_search'])) {
		return ;
	}
	$psmarty->assign('is_f1_search', 1);
	$psmarty->assign('order', $order);
	$psmarty->assign('desc', $desc);
	
	// MongoDB への接続とか
	$mongo = new Mongo();
	$db = $mongo->selectDB($env['admin_db']['db_name']);
	$default_timeout = 1000;
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成
	
	// 検索条件の設定
	$condition = array();
	if (strtotime($begin_date) > 0) {
		if (! isset($condition['log_date'])) {
			$condition['log_date'] = array();
		}
		$condition['log_date']['$gte'] = new MongoDate(strtotime($begin_date));
	}
	if (strtotime($end_date) > 0) {
		if (! isset($condition['log_date'])) {
			$condition['log_date'] = array();
		}
		$condition['log_date']['$lt'] = new MongoDate(strtotime($end_date) + 86400);  // 日単位なので終端は翌日 00:00 とする
	}
	
	// ログの生成
	$logs = array();
	
	// 積み上げ進行状況を出す
	$collection = $db->selectCollection(sprintf('d_event_steps_%d', $event_id));
	$keys = array('step' => 1);
	$initial = array('cnt' => 0);
	$reduce = 'function(obj, prev){prev.cnt++;}';
	$option = array();
	if (sizeof($condition) > 0) {
		$option['condition'] = $condition;
	}
	
	$arr = $collection->group($keys, $initial, $reduce, $option);
//var_dump($arr);
	foreach ($arr['retval'] AS $rec) {
		$logs[$rec['step']] = array(
			'step'      => $rec['step'], 
			'step_name' => $step_names[$rec['step']], 
			'total'     => $rec['cnt'], 
			'width_t'   => $rec['cnt'], 
		);
	}
	
	// 進捗状況を出す
	$collection = $db->selectCollection(sprintf('d_event_step_current_%d', $event_id));
	$keys = array('step' => 1);
	$initial = array('cnt' => 0);
	$reduce = 'function(obj, prev){prev.cnt++;}';
	$option = array();
	if (sizeof($condition) > 0) {
		$option['condition'] = $condition;
	}
	
	$arr = $collection->group($keys, $initial, $reduce, $option);
//var_dump($arr);
	foreach ($arr['retval'] AS $rec) {
		if (isset($logs[$rec['step']])) {
			$logs[$rec['step']]['count'] = $rec['cnt'];
			$logs[$rec['step']]['width_c'] = $rec['cnt'];
			$logs[$rec['step']]['width_t'] -= $rec['cnt'];
		}
		else {
			$logs[$rec['step']] = array(
				'step'      => $rec['step'], 
				'step_name' => $step_names[$rec['step']], 
				'total'     => 0, 
				'width_t'   => 0, 
				'count'     => $rec['cnt'], 
				'width_c'   => $rec['cnt'], 
			);
		}
	}
	
	// 必要ならここでソート
	function mycmp($a,$b) {
		if ($GLOBALS['desc'] == 'desc') {
			return ($a[$GLOBALS['order']] < $b[$GLOBALS['order']] ? 1 : -1);
		}
		else {
			return ($a[$GLOBALS['order']] < $b[$GLOBALS['order']] ? -1 : 1);
		}
	}
	uasort($logs, mycmp);
	
	
	// グラフの幅を計算
	$max = 1;
	foreach ($logs As $k => $v) {
		if ($max < ($v['total'] + $v['count'])) {
			$max = ($v['total'] + $v['count']);
		}
	}
	$rate = $graph_max_width / $max;
	foreach ($logs As $k => $v) {
		$logs[$k]['width_t'] = (($v['total'] - $v['count']) > 0 ? ($v['total'] - $v['count']) : 0) * $rate;
		$logs[$k]['width_c'] = $v['count'] * $rate;
	}
	
	// テンプレートに入れる
	$psmarty->assign('logs', $logs);
?>
