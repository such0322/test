<?php
	
	// 先にクエスト集計を
	$sql = "INSERT INTO quest_stat(log_date, quest_id, begin_total, begin_unique, commit_total, commit_unique) SELECT '{$exec_datetime}', quest_id, count(*), count(distinct user_id), (SELECT count(*) FROM lt_quest_commit WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND quest_id = lt_quest_begin.quest_id), (SELECT count(distinct user_id) FROM lt_quest_commit WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND quest_id = lt_quest_begin.quest_id) FROM lt_quest_begin WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' GROUP BY quest_id";
	$res = db_exec($con, $sql);
//var_dump($sql, $arr, db_error($con));
	
	
	
	// まずはほぼ生データを確保
	$sql = "INSERT INTO quest_card_helper(log_date, quest_id, card_id, player_id, level) SELECT log_date, quest_id, helper_card_id AS card_id, user_id, level FROM lt_quest_begin WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND helper_card_id > 0";
	$res = db_exec($con, $sql);
//var_dump($sql, $arr, db_error($con));
	
	$sql = "INSERT INTO quest_card_leader(log_date, quest_id, card_id, player_id, level) SELECT log_date, quest_id, leader_card_id AS card_id, user_id, level FROM lt_quest_begin WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND helper_card_id > 0";
	$res = db_exec($con, $sql);
//var_dump($sql, $arr, db_error($con));
	
	$sql = <<<_SQL_
INSERT INTO quest_card_member(log_date, quest_id, card_id, player_id, level)
SELECT log_date, quest_id, slot1_card_id  AS card_id, user_id, level FROM lt_quest_begin WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND slot1_card_id > 0
UNION
SELECT log_date, quest_id, slot2_card_id  AS card_id, user_id, level FROM lt_quest_begin WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND slot2_card_id > 0
UNION
SELECT log_date, quest_id, slot3_card_id  AS card_id, user_id, level FROM lt_quest_begin WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND slot3_card_id > 0
UNION
SELECT log_date, quest_id, slot4_card_id  AS card_id, user_id, level FROM lt_quest_begin WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND slot4_card_id > 0
UNION
SELECT log_date, quest_id, slot5_card_id  AS card_id, user_id, level FROM lt_quest_begin WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND slot5_card_id > 0
UNION
SELECT log_date, quest_id, slot6_card_id  AS card_id, user_id, level FROM lt_quest_begin WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND slot6_card_id > 0
UNION
SELECT log_date, quest_id, helper_card_id AS card_id, user_id, level FROM lt_quest_begin WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND slot6_card_id > 0
_SQL_;
	$res = db_exec($con, $sql);
//var_dump($sql, $arr, db_error($con));
	
	
	
	
	// メンバーとしての利用状況を集計
	$sql = "INSERT INTO quest_card_stat(log_date, quest_id, card_id, member_unique, member_total) SELECT '{$exec_date}', quest_id, card_id, count(distinct player_id), count(*) FROM quest_card_member WHERE log_date = '{$exec_datetime}' GROUP BY quest_id, card_id ORDER BY quest_id, card_id";
	$res = db_exec($con, $sql);
//var_dump($sql, $arr, db_error($con));
	
	
	// リーダーの集計とそれで更新
	$sql = "SELECT '{$exec_datetime}', quest_id, card_id, count(distinct player_id) AS u, count(*) AS c FROM quest_card_leader WHERE log_date = '{$exec_datetime}' GROUP BY quest_id, card_id ORDER BY quest_id, card_id";
	$arr = db_select($con, $sql);
	foreach ($arr as $rec) {
		$tpl = "UPDATE quest_card_stat SET leader_unique=%d, leader_total=%d WHERE log_date = '%s' AND quest_id = %d AND card_id = %d";
		$sql = sprintf($tpl, $rec['u'], $rec['c'], $exec_datetime, $rec['quest_id'], $rec['card_id']);
		$res = db_exec($con, $sql);
		
		// 構造上リーダーも助っ人もメンバーとして含まれているので更新対象が無い事は想定していない
	}
	
	// 助っ人の集計とそれで更新
	$sql = "SELECT '{$exec_datetime}', quest_id, card_id, count(distinct player_id) AS u, count(*) AS c FROM quest_card_helper WHERE log_date = '{$exec_datetime}' GROUP BY quest_id, card_id ORDER BY quest_id, card_id";
	$arr = db_select($con, $sql);
	foreach ($arr as $rec) {
		$tpl = "UPDATE quest_card_stat SET helper_unique=%d, helper_total=%d WHERE log_date = '%s' AND quest_id = %d AND card_id = %d";
		$sql = sprintf($tpl, $rec['u'], $rec['c'], $exec_datetime, $rec['quest_id'], $rec['card_id']);
		$res = db_exec($con, $sql);
		
		// 構造上リーダーも助っ人もメンバーとして含まれているので更新対象が無い事は想定していない
	}
