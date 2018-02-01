<?php

/////////////////////////////////////////////////////////////////////////////
// 初期化とか

require_once('env.php');
$env = env();

$con = admin_con();

$collection_groups = array(
	1 => '話', 
	2 => '声', 
	3 => '絵', 
	4 => '後', 
	5 => '服', 
);


$defaults = array(
//	'collection_group'           => (isset($collection_groups[$LOCAL_SESSION['collection_group']]) ? $LOCAL_SESSION['collection_group'] : ''), 
	'log_date_begin' => (isset($LOCAL_SESSION['log_date_begin']) ? $LOCAL_SESSION['log_date_begin'] : ''), 
	'log_date_end'   => (isset($LOCAL_SESSION['log_date_end'])   ? $LOCAL_SESSION['log_date_end']   : ''), 
);

$f1_submit = $LOCAL_SESSION['f1_submit'];

$character_names = res2kv(\Mag\Res::load('character'), 'id', 'name');


/////////////////////////////////////////////////////////////////////////////
// 更新とか

if (isset($_POST['submit'])) {
	// 検索条件の変更があった場合
	if ($_POST['f1_submit']) {
		if (isset($collection_groups[$_POST['collection_group']])) {
			$LOCAL_SESSION['collection_group'] = $_POST['collection_group'];
		} else {
			unset($LOCAL_SESSION['collection_group']);
		}
		if (preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $_POST['log_date_begin']) and strtotime($_POST['log_date_begin']) < time()) {
			$LOCAL_SESSION['log_date_begin'] = $_POST['log_date_begin'];
		}
		if (preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $_POST['log_date_end']) and strtotime($_POST['log_date_end']) > 0) {
			$LOCAL_SESSION['log_date_end'] = $_POST['log_date_end'];
		}
		
		$LOCAL_SESSION['f1_submit'] = 1;
	}
	
	return;
}

/////////////////////////////////////////////////////////////////////////////
// 表示内容作成

$psmarty->assign('collection_groups', $collection_groups);
$psmarty->assign('character_names', $character_names);
$psmarty->assign('defaults', $defaults);
$psmarty->assign('f1_submit', $f1_submit);


// 検索条件が指定されてなければここまで
if (! $f1_submit) {
	return;
}

// 色々計算する元となるデータを取得
$sql = "SELECT item_id, collection_type, collection_id, count(*) as c FROM collection_itemunlock";
$wa = array();
if ($defaults['log_date_begin']) {$wa[] = sprintf(" '%s' <= log_date ", $defaults['log_date_begin']);}
if ($defaults['log_date_end']) {$wa[] = sprintf(" log_date <= '%s' ", $defaults['log_date_end']);}
if ($defaults['collection_group']) {$wa[] = sprintf(" collection_type = %d ", $defaults['collection_group']);}
if ($wa) {$sql .= " WHERE " . implode(' AND ', $wa);}
$sql .= " GROUP BY item_id, collection_type, collection_id ORDER BY item_id, collection_type, collection_id";
$arr = db_select($con, $sql);

$psmarty->assign('collection_itemunlock', $arr);

