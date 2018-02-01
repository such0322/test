<?php

/////////////////////////////////////////////////////////////////////////////
// 初期化とか

require_once('env.php');
$env = env();

$con = admin_con();
$defaults = array(
	'level' => array('min' => '', 'max' => ''), 
);
if (isset($LOCAL_SESSION['level'])) {$defaults['level'] = $LOCAL_SESSION['level'];}

$mission_master = \Mag\Res::load('mission');
$mission_names = array();
foreach ($mission_master as $k => $m) {
	$mission_names[$k] = str_replace('@n', ' ', $m['title']);
}

$f1_search = $LOCAL_SESSION['f1_search'];

/////////////////////////////////////////////////////////////////////////////
// 更新とか
if (isset($_POST['submit'])) {
	if ($_POST['f1_submit']) {
		
		$level = array('min' => '', 'max' => '');
		
		if (isset($_POST['level']) and isset($_POST['level']['min']) and intval($_POST['level']['min']) > 0) {
			$level['min'] = $_POST['level']['min'];
		}
		if (isset($_POST['level']) and isset($_POST['level']['max']) and intval($_POST['level']['max']) > 0) {
			$level['max'] = $_POST['level']['max'];
		}
		
		if ($level['min'] or $level['max']) {
			$LOCAL_SESSION['level'] = $level;
		} else {
			unset($LOCAL_SESSION['level']);
		}
		
		$LOCAL_SESSION['f1_search'] = 1;
	}
	
	return;
}

/////////////////////////////////////////////////////////////////////////////
// 表示とか

// ここでの処理の必要ないのの設定
$psmarty->assign('defaults', $defaults);

// 検索ボタン押してない時はここまで
if (! $f1_search) {
	return ;
}

// 
$missions = array();
$sql = "SELECT mission_id, count(*) as cnt FROM mission_clear ";
if ($defaults) {
	$wa = array();
	if (isset($defaults['level'])) {
		if ($defaults['level']['min'] > 0) {$wa[] = sprintf(" %d <= level ", $defaults['level']['min']);}
		if ($defaults['level']['max'] > 0) {$wa[] = sprintf(" level <= %d ", $defaults['level']['max']);}
	}
	if ($wa) {
		$sql .= " WHERE " . implode(' AND ', $wa);
	}
}
$sql .= " GROUP BY mission_id ORDER BY cnt DESC";
$arr = db_select($con, $sql);
$max = 0;
foreach ($arr as $rec) {
	$width = 0.0;
	if ($max == 0) {
		$width = 1;
		$max = ($rec['cnt']?:1);
	} else {
		$width = $rec['cnt'] / $max;
	}
	$missions[$rec['mission_id']] = array(
		'mission_id' => $rec['mission_id'], 
		'cnt' => $rec['cnt'], 
		'width' => $width, 
	);
}
ksort($missions);


$psmarty->assign('mission_names', $mission_names);
$psmarty->assign('missions', $missions);
$psmarty->assign('f1_search', $f1_search);
