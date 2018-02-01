<?php
	
	// 日毎の使用端末情報を記録
	
	
	// 素直に SQL で実装
	$sql = "INSERT INTO ua_stat(log_date, ua, total) SELECT '{$exec_datetime}', ua, count(*) FROM lt_login_ua WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' GROUP BY ua ORDER BY ua";
	$res = db_exec($con, $sql);
	
