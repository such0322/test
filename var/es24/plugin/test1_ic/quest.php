<?php

/////////////////////////////////////////////////////////////////////////////
// 全体用の初期値とか

require_once('env.php');
$env = env();

$defaults = array(
	'date' => (isset($LOCAL_SESSION['date']) ? $LOCAL_SESSION['date'] : date('Y-m-d', strtotime('-1 day'))), 
	'quest_id' => (isset($LOCAL_SESSION['quest_id']) ? $LOCAL_SESSION['quest_id'] : 0), 
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
		if (preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $_POST['date']) and strtotime($_POST['date']) < time()) {
			$LOCAL_SESSION['date'] = $_POST['date'];
		}
		if (isset($quest_names[$_POST['quest_id']])) {
			$LOCAL_SESSION['quest_id'] = $_POST['quest_id'];
		}
		
		$LOCAL_SESSION['f1_submit'] = 1;
	}
	return;
}

/////////////////////////////////////////////////////////////////////////////
// 表示内容作成

$psmarty->assign('quest_names', $quest_names);
$psmarty->assign('card_names', $card_names);
$psmarty->assign('defaults', $defaults);
$psmarty->assign('f1_submit', $f1_submit);

// 検索条件が指定されてなければここまで
if (! $f1_submit) {
	return;
}

$con = admin_con();


// まずクエスト
$quest_stat = array(
	'begin_total' => 0, 
	'begin_unique' => 0, 
	'commit_total' => 0, 
	'commit_unique' => 0, 
);
$tpl = "SELECT begin_total,begin_unique,commit_total,commit_unique FROM quest_stat WHERE log_date = '%s' AND quest_id = '%d'";
$sql = sprintf($tpl, db_qs($con, $defaults['date']), $defaults['quest_id']);
$arr = db_select($con, $sql);
if ($arr) {
	$quest_stat = $arr[0];
}
$psmarty->assign('quest_stat', $quest_stat);


// カード利用状況
$leader = array();
$tpl = "SELECT card_id, leader_unique, leader_total, member_unique, member_total, helper_unique, helper_total FROM quest_card_stat WHERE log_date = '%s' AND quest_id = '%d'";
$sql = sprintf($tpl, db_qs($con, $defaults['date']), $defaults['quest_id']);
$arr = db_select($con, $sql);
$psmarty->assign('quest_card_stat', $arr);
