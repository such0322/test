<?php

/////////////////////////////////////////////////////////////////////////////
// 初期化とか

require_once('env.php');
$env = env();

$con = admin_con();

$type_list = array(
	1 => 1, 
	2 => 2, 
	3 => 3, 
);
$stat_group_list = array(
	'card_id' => 'card_id', 
	'character_id' => 'character_id', 
);

$defaults = array(
	'type'           => (isset($type_list[$LOCAL_SESSION['type']]) ? $LOCAL_SESSION['type'] : ''), 
	'log_date_begin' => (isset($LOCAL_SESSION['log_date_begin']) ? $LOCAL_SESSION['log_date_begin'] : ''), 
	'log_date_end'   => (isset($LOCAL_SESSION['log_date_end'])   ? $LOCAL_SESSION['log_date_end']   : ''), 
	'stat_group'     => (isset($stat_group_list[$LOCAL_SESSION['stat_group']]) ? $LOCAL_SESSION['stat_group'] : 'card_id'), 
);

$f1_submit = $LOCAL_SESSION['f1_submit'];

$card_names = res2kv(\Mag\Res::load('card'), 'card_id', 'name');
$character_names = res2kv(\Mag\Res::load('character'), 'id', 'name');

/////////////////////////////////////////////////////////////////////////////
// 更新とか

if (isset($_POST['submit'])) {
	// 検索条件の変更があった場合
	if ($_POST['f1_submit']) {
		if (isset($type_list[$_POST['type']])) {
			$LOCAL_SESSION['type'] = $_POST['type'];
		}
		if (preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $_POST['log_date_begin']) and strtotime($_POST['log_date_begin']) < time()) {
			$LOCAL_SESSION['log_date_begin'] = $_POST['log_date_begin'];
		}
		if (preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $_POST['log_date_end']) and strtotime($_POST['log_date_end']) > 0) {
			$LOCAL_SESSION['log_date_end'] = $_POST['log_date_end'];
		}
		if (isset($stat_group_list[$_POST['stat_group']])) {
			$LOCAL_SESSION['stat_group'] = $_POST['stat_group'];
		}
		
		$LOCAL_SESSION['f1_submit'] = 1;
	}
	
	return;
}

/////////////////////////////////////////////////////////////////////////////
// 表示内容作成

$psmarty->assign('type_list', $type_list);
$psmarty->assign('stat_group_list', $stat_group_list);

$psmarty->assign('defaults', $defaults);
$psmarty->assign('f1_submit', $f1_submit);


// 検索条件が指定されてなければここまで
if (! $f1_submit) {
	return;
}

// 色々計算する元となるデータを取得
$sql = "SELECT ";
if ($defaults['stat_group'] === 'character_id') {
	$sql .= " (SELECT character_id FROM res_card WHERE card_id = card1_id) AS c1, (SELECT character_id FROM res_card WHERE card_id = card2_id) AS c2, sum(total) as s FROM otetsudai_set ";
} else {
	$sql .= " card1_id AS c1, card2_id AS c2, sum(total) as s FROM otetsudai_set ";
}
$wa = array();
if ($defaults['log_date_begin']) {$wa[] = sprintf(" '%s' <= log_date ", $defaults['log_date_begin']);}
if ($defaults['log_date_end']) {$wa[] = sprintf(" log_date <= '%s' ", $defaults['log_date_end']);}
if ($defaults['type']) {$wa[] = sprintf(" type = %d ", $defaults['type']);}
if ($wa) {$sql .= " WHERE " . implode(' AND ', $wa);}
$sql .= " GROUP BY c1, c2 ORDER BY c1, c2";
$arr = db_select($con, $sql);
//var_dump($sql, db_error($con), $arr);


////////////////////////////////////////////////////////////
// 人気のもののみ抽出

// 各要素の総出現数と
$total = array();
$cross = array();
foreach ($arr as $rec) {
	
	// total側計算
	if (! isset($total[$rec['c1']])) {
		$total[$rec['c1']] = 0;
	}
	$total[$rec['c1']] += $rec['s'];
	if (! isset($total[$rec['c2']])) {
		$total[$rec['c2']] = 0;
	}
	$total[$rec['c2']] += $rec['s'];
	
	// リストを双方向に
	if (! isset($cross[$rec['c1']])) {$cross[$rec['c1']] = array();}
	if (! isset($cross[$rec['c1']][$rec['c2']])) {$cross[$rec['c1']][$rec['c2']] = 0;}
	$cross[$rec['c1']][$rec['c2']] += $rec['s'];
	if (! isset($cross[$rec['c2']])) {$cross[$rec['c2']] = array();}
	if (! isset($cross[$rec['c2']][$rec['c1']])) {$cross[$rec['c2']][$rec['c1']] = 0;}
	$cross[$rec['c2']][$rec['c1']] += $rec['s'];
}
ksort($total);

// 双方向の方を多い順にソート
$ranks = range(1, 3);
$favcomb = array();
$labels = ($defaults['stat_group'] === 'character_id' ? $character_names : $card_names);
ksort($labels);
foreach ($cross as $c1 => $records) {
	
	$favcomb[$c1] = array();
	arsort($records);
	foreach ($ranks as $rank) {
		list($c2, $t) = each($records);
		if ($c2 and $t) {
			$favcomb[$c1][$rank] = array('id' => $c2, 't' => $t, 'label' => $labels[$c2]);
		} else {
			$favcomb[$c1][$rank] = array('id' => 0, 't' => 0, 'label' => '');
		}
	}
}

$psmarty->assign('ranks', $ranks);
$psmarty->assign('labels', $labels);
$psmarty->assign('total', $total);
$psmarty->assign('favcomb', $favcomb);

////////////////////////////////////////////////////////////
// 組み合わせ全部
if ($defaults['stat_group'] === 'character_id') {
	$setgraph = array();
	$labels_rev = array_reverse($labels, true);
	$psmarty->assign('labels_rev', $labels_rev);
	
	$sets = array();
	foreach ($labels as $k => $v) {
		$sets[$k] = array();
		foreach ($labels_rev as $kk => $vv) {
			if ($k >= $kk) {
				$sets[$k][$kk] = null;
			} else {
				$sets[$k][$kk] = intval($cross[$k][$kk]);
			}
		}
	}
	
	$psmarty->assign('sets', $sets);
}


