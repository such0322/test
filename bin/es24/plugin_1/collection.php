<?php
	
	// コレクション関連のあれこれ
	
	
	// キャラクターコレクションをキャラクター単位で集計
	$sql = "INSERT INTO cc_stat(log_date, character_id, collection_group, unlock_count) SELECT '{$exec_datetime}', character_id, collection_type, count(*) FROM lt_collection_unlock WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND character_id > 0 GROUP BY character_id, collection_type";
	$res = db_exec($con, $sql);
	
	
	// アイテムによる共通コレクションの開放
	$sql = "INSERT INTO collection_itemunlock(log_date,player_id,item_id,collection_type,collection_id) SELECT log_date, user_id, item_id, collection_type, collection_id FROM lt_collection_itemunlock WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' ";
	$res = db_exec($con, $sql);
