<?php

require_once('env.php');
require_once('lib/func.php');


$gacha_drop_master = \Mag\Master\GachaDrop::factory()->load();
$gacha_ides = $gacha_drop_master->distinct('gacha_id');
$gacha_id = (isset($LOCAL_SESSION['gacha_id']) ? $LOCAL_SESSION['gacha_id'] : 0);

$lot_unit = 100;

/////////////////////////////////////////////////////////////////////////////
// post の挙動
if ($_POST['f1_submit']) {
	if (isset($gacha_ides[$_POST['gacha_id']])) {
		$LOCAL_SESSION['gacha_id'] = $_POST['gacha_id'];
	}
}
if ($_POST['f2_reset']) {
	if (isset($gacha_ides[$_POST['gacha_id']])) {
		$admin_con = admin_con();
		$tpl = "DELETE FROM gachasim_drop WHERE gacha_id = %d";
		$sql = sprintf($tpl, $_POST['gacha_id']);
		$res = db_exec($admin_con, $sql);
	}
}

if ($_POST) {
	return;
}

/////////////////////////////////////////////////////////////////////////////
// ajax系処理

if (isset($_GET['ajax'])) {
	$ret = array();
	
	if ($_GET['ajax'] == 'gacha') {
		$admin_con = admin_con();
		$gacaha_id = $_GET['gacha_id'];
		
		// 抽選
		$gacha_drop_master->filter(array('gacha_id' => $gacha_id));
		$gacha_drop_ides = array();
		for ($i = 0;$i < $lot_unit;$i++) {
			$gacha_drop_id = $gacha_drop_master->rateChoose()->get('id');
			$gacha_drop_ides[] = $gacha_drop_id;
		}
		
		// ログに記録
		$tpl = "INSERT INTO gachasim_drop(gacha_id, gacha_drop_id) VALUES";
		$tpl_val = "(%d,%d)";
		$values = array();
		foreach ($gacha_drop_ides as $n) {
			$values[] = sprintf($tpl_val, $gacha_id, $n);
		}
		$sql = $tpl . implode(',', $values);
		$res = db_exec($admin_con, $sql);
	}
	if ($_GET['ajax'] == 'drop') {
		$admin_con = admin_con();
		$gacaha_id = $_GET['gacha_id'];
		
		$tpl = "SELECT gacha_drop_id, count(*) AS cnt FROM gachasim_drop WHERE gacha_id = %d GROUP BY gacha_drop_id ORDER BY gacha_drop_id";
		$sql = sprintf($tpl, $gacha_id);
		$arr = db_select($admin_con, $sql);
		$sum = array_reduce($arr, function($carry, $item){
			return $carry + $item['cnt'];
		}, 0);
		// $sum = array_sum(array_map(function($a){return $a['cnt'];}, $arr));
		
		foreach ($arr as $rec) {
			$ret[] = array(
				'gacha_drop_id' => intval($rec['gacha_drop_id']), 
				'cnt' => intval($rec['cnt']), 
				'rate' => sprintf('%2.2f %%', ($sum > 0 ? $rec['cnt'] / $sum : 0) * 100), 
			);
		}
	}
	
	header('Content-Type:application/json');
	echo json_encode($ret);
	exit;
}

/////////////////////////////////////////////////////////////////////////////
// 表示内容作成

$psmarty->assign('gacha_id', $gacha_id);
$psmarty->assign('gacha_ides', $gacha_ides);
$psmarty->assign('gacha_drop_master', $gacha_drop_master->getArray());
$psmarty->assign('lot_unit', $lot_unit);

