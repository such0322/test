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
	'collection_group'           => (isset($collection_groups[$LOCAL_SESSION['collection_group']]) ? $LOCAL_SESSION['collection_group'] : ''), 
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
$sql = "SELECT character_id, collection_group, sum(unlock_count) as s FROM cc_stat";
$wa = array();
if ($defaults['log_date_begin']) {$wa[] = sprintf(" '%s' <= log_date ", $defaults['log_date_begin']);}
if ($defaults['log_date_end']) {$wa[] = sprintf(" log_date <= '%s' ", $defaults['log_date_end']);}
if ($defaults['collection_group']) {$wa[] = sprintf(" collection_group = %d ", $defaults['collection_group']);}
if ($wa) {$sql .= " WHERE " . implode(' AND ', $wa);}
$sql .= " GROUP BY character_id, collection_group ORDER BY character_id, collection_group";
$arr = db_select($con, $sql);

// 結果を整形
$cc = array();
$default_record = array_map(function(){return 0;}, $collection_groups);
$default_record['total'] = 0;
$max = 0;
foreach ($character_names as $c => $n) {
	$cc[$c] = $default_record;
}
foreach ($arr as $rec) {
	$cc[$rec['character_id']][$rec['collection_group']] = $rec['s'];
	$cc[$rec['character_id']]['total'] += $rec['s'];
	
	if ($cc[$rec['character_id']]['total'] > $max) {
		$max = $cc[$rec['character_id']]['total'];
	}
}

// グラフ用の計算
$ccg = array();
foreach ($cc as $character_id => $rec) {
	$ccg[$character_id] = array();
	foreach ($collection_groups as $cg => $cn) {
		$ccg[$character_id][$cg] = $rec[$cg] / $max;
	}
}

$psmarty->assign('cc', $cc);
$psmarty->assign('ccg', $ccg);

