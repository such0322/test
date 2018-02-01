<?PHP
	////////////////////////////////////////////////////////////////////////////
	// 単純なログのの取り込み
//mylog(__LINE__);
//var_dump(__LINE__);
	
	$l = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m'), date('d') - $is_fixed, date('Y')));
	$r = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m'), date('d') - $is_fixed + 1, date('Y')));
	
	// 途中ログを削除
	$res = db_exec($con, "DELETE FROM regist_log WHERE '{$l}' <= log_date AND log_date < '{$r}'");
	$res = db_exec($con, "INSERT INTO regist_log(log_date, pftype, user_id) SELECT log_date, pf_type, user_id FROM lt_regist WHERE '{$l}' <= log_date AND log_date < '{$r}'");
	$res = db_exec($con, "DELETE FROM login_log WHERE '{$l}' <= log_date AND log_date < '{$r}'");
	$res = db_exec($con, "INSERT INTO login_log(log_date, pftype, user_id)  SELECT log_date, pf_type, user_id FROM lt_login  WHERE '{$l}' <= log_date AND log_date < '{$r}'");
