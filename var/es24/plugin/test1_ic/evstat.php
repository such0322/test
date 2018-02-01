<?php

require_once('env.php');
require_once('lib/func.php');


$labels = array(
	'rank_min'         => '順位上限', 
	'rank_max'         => '順位下限', 
	'level_min'        => 'RANK上限', 
	'level_max'        => 'RANK下限', 
	'user_count'       => 'ユーザー数', 
	'avg_point'        => '平均最終ポイント', 
	'avg_clear_total'  => '平均総クリア回数', 
	'avg_clear_normal' => '平均クリア回数（通常）', 
	'avg_clear_bonus'  => '平均クリア回数（ボーナス）', 
	
	'avg_clear_1'      => '平均クリア回数（クエスト１）', 
	'avg_clear_2'      => '平均クリア回数（クエスト２）', 
	'avg_clear_3'      => '平均クリア回数（クエスト３）', 
	'avg_clear_4'      => '平均クリア回数（クエスト４）', 
	'avg_clear_5'      => '平均クリア回数（クエスト５）', 
	'avg_clear_6'      => '平均クリア回数（クエスト６）', 
	'avg_clear_7'      => '平均クリア回数（クエスト７）', 
	'avg_clear_8'      => '平均クリア回数（クエスト８）', 
	'avg_clear_9'      => '平均クリア回数（クエスト９）', 
	'avg_clear_10'     => '平均クリア回数（クエスト１０）', 
	'avg_clear_11'     => '平均クリア回数（クエスト１１）', 
	'avg_clear_12'     => '平均クリア回数（クエスト１２）', 
	'avg_clear_13'     => '', 
	'avg_clear_14'     => '', 
	'avg_clear_15'     => '', 
	'avg_clear_16'     => '', 
	
	'avg_login_days'   => '平均プレイ日数', 
	'login_days_1'     => 'プレイ日数1', 
	'login_days_2'     => 'プレイ日数2', 
	'login_days_3'     => 'プレイ日数3', 
	'login_days_4'     => 'プレイ日数4', 
	'login_days_5'     => 'プレイ日数5', 
	'login_days_6'     => 'プレイ日数6', 
	'login_days_7'     => 'プレイ日数7', 
	'login_days_8'     => 'プレイ日数8', 
	'login_days_9'     => 'プレイ日数9', 
	'login_days_10'    => 'プレイ日数10', 
	'login_days_11'    => '', 
	'login_days_12'    => '', 
	'login_days_13'    => '', 
	'login_days_14'    => '', 
	
	'stheal_18'        => '総スタミナ回復数（メロンパン）', 
	'stheal_19'        => '総スタミナ回復数（限定メロンパン）', 
	'stheal_mc'        => '総スタミナ回復数（ダイヤ）', 
	
	'card_1'           => '特攻カード１所持', 
	'card_2'           => '特攻カード２所持', 
	'card_3'           => '特攻カード３所持', 
	'card_4'           => '特攻カード４所持', 
	'card_5'           => '', 
	'card_6'           => '', 
);
$floatcols = array(
	'avg_point', 
	'avg_clear_total', 
	'avg_clear_normal', 
	'avg_clear_bonus', 
	'avg_clear_1', 
	'avg_clear_2', 
	'avg_clear_3', 
	'avg_clear_4', 
	'avg_clear_5', 
	'avg_clear_6', 
	'avg_clear_7', 
	'avg_clear_8', 
	'avg_clear_9', 
	'avg_clear_10', 
	'avg_clear_11', 
	'avg_clear_12', 
	'avg_clear_13', 
	'avg_clear_14', 
	'avg_clear_15', 
	'avg_clear_16', 
	'avg_login_days', 
);


/////////////////////////////////////////////////////////////////////////////
// post の挙動
if ($_POST['f1_submit']) {
	// 検索条件の設定
	if (preg_match('/^[0-9]+$/', $_POST['event_id'])) {
		$LOCAL_SESSION['event_id'] = $_POST['event_id'];
	} else {
		$LOCAL_SESSION['event_id'] = '';
	}
	
}
if ($_POST) {
	return;
}


$con = admin_con();
$event_id = $LOCAL_SESSION['event_id'];


/////////////////////////////////////////////////////////////////////////////
// 表示内容作成 (イベント一覧選択)
$sql = "SELECT distinct event_id FROM evstat_cache";
$event_ides = array_map(function($rec){return $rec['event_id'];}, db_select($con, $sql));

$psmarty->assign('event_id', $event_id);
$psmarty->assign('event_ides', $event_ides);

/////////////////////////////////////////////////////////////////////////////
// 表示内容作成 (詳細)

if (! $event_id) {
	return;
}

$evstat = array();
$sql = sprintf("SELECT * FROM evstat_cache WHERE event_id = %d ORDER BY rank_min, level_min", $event_id);
$evstat = db_select($con, $sql);
$psmarty->assign('labels', $labels);
$psmarty->assign('evstat', $evstat);
$psmarty->assign('floatcols', $floatcols);
