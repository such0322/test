<?php

/////////////////////////////////////////////////////////////////////////////
// 全体用の初期値とか

require_once('env.php');
$env = env();

$cols = array('leader','member','helper');

$defaults = array(
	'date_begin' => (isset($LOCAL_SESSION['date_begin']) ? $LOCAL_SESSION['date_begin'] : date('Y-m-d', strtotime('-1 day'))), 
	'date_end' => (isset($LOCAL_SESSION['date_end']) ? $LOCAL_SESSION['date_end'] : date('Y-m-d', strtotime('-1 day'))), 
	'col' => (in_array($LOCAL_SESSION['col'], $cols) ? $LOCAL_SESSION['col'] : 'member')
);

$quest_master = \Mag\Res::load('quest');
$quest_names = array();
foreach ($quest_master as $k => $rec) {
	$quest_names[$k] = $rec['quest_name'];
}

$card_master = \Mag\Res::load('card');
$card_names = array();
foreach ($card_master as $k => $rec) {
	$card_names[$k] = $rec['name'];
}

$f1_submit = $LOCAL_SESSION['f1_submit'];

/////////////////////////////////////////////////////////////////////////////
// 更新とか

if (isset($_POST['submit'])) {
	// 検索条件の変更があった場合
	if ($_POST['f1_submit']) {
		if (preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $_POST['date_begin']) and strtotime($_POST['date_begin']) < time()) {
			$LOCAL_SESSION['date_begin'] = $_POST['date_begin'];
		}
		if (preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $_POST['date_end']) and strtotime($_POST['date_end']) < time()) {
			$LOCAL_SESSION['date_end'] = $_POST['date_end'];
		}
		
		if (in_array($_POST['col'], $cols)) {
			$LOCAL_SESSION['col'] = $_POST['col'];
		}
		
		$LOCAL_SESSION['f1_submit'] = 1;
	}
	return;
}

/////////////////////////////////////////////////////////////////////////////
// 表示内容作成

$psmarty->assign('quest_names', $quest_names);
$psmarty->assign('card_names', $card_names);
$psmarty->assign('cols', $cols);
$psmarty->assign('defaults', $defaults);
$psmarty->assign('f1_submit', $f1_submit);

// 検索条件が指定されてなければここまで
if (! $f1_submit) {
	return;
}

$con = admin_con();

// カード利用状況
$leader = array();
$tpl = "SELECT card_id, quest_id, sum(%s_total) as t FROM quest_card_stat WHERE '%s' <= log_date AND log_date <= '%s' GROUP BY card_id, quest_id";
$sql = sprintf($tpl, $defaults['col'], db_qs($con, $defaults['date_begin']), db_qs($con, $defaults['date_end']));
$arr = db_select($con, $sql);

$quest_card_stat = array();
$active_quests = array();
foreach ($arr as $rec) {
	if (! isset($quest_card_stat[$rec['card_id']])) {
		$quest_card_stat[$rec['card_id']] = array();
	}
	$quest_card_stat[$rec['card_id']][$rec['quest_id']] = $rec['t'];
	
	$active_quests[$rec['quest_id']] = $quest_names[$rec['quest_id']];
}
$psmarty->assign('quest_card_stat', $quest_card_stat);
$psmarty->assign('active_quests', $active_quests);
