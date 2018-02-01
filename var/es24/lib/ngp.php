<?PHP
	
	/** CSV出力の時の */
	function csv_encode($s) {
		$ret = '';
		$l = es2_client_lang();
		
		if ($l == 'ja') {
			$ret = mb_convert_encoding($s, 'SJIS', 'UTF-8');
		}
		else {
			$ret = $s;
		}
		
		return $ret;
	}
	
	
	
	
	/** 必要になりそうなログファイル一式を取得 */
	function report_logfiles($dir, $dates, $prefix) {
		$ret = array();
		foreach ($dates As $date => $ymd) {
			$a = glob("{$dir}/{$prefix}_{$ymd}.log");
			$ret = array_merge($ret, $a);
		}
		return $ret;
	}
	
	
	/**
	 * ログ一覧から所定時間内の所定項目のユニークを取る
	 */
	function report_logchoose($logs, $start_date, $end_date, $ope, $where = null, $group = null) {
/*
$ope = array(
	col => 'unique|sum'
	...
)
$where => array(
	col => array(...)  // in_array($log[col], $v)
)
$group => aray(
	col => グループ化する文字数 (substr(str, 0, ここ))
)
*/
		$ret = array();
		foreach ($logs as $log) {
			$fp = fopen($log, 'r');
			if ($fp) {
				while ($line = fgets($fp)) {
					$l = explode("\t", $line);
					if (sizeof($l) < max(array_keys($ope))) {
						continue;
					}
					if (! preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $l[0])) {
						continue;
					}
					if (strcmp($start_date, $l[0]) <= 0 and strcmp($l[0], $end_date) < 0) {
						
						// 検索条件があれば加味
						if (is_array($where)) {
							foreach ($where as $col => $val) {
								if (is_array($val)) {
									if (! in_array($l[$col], $val)) {
										continue 2;
									}
								} elseif ($l[$col] != $val) {
									continue 2;
								}
							}
						}
						
						// グループ化の必要があれば対応
						$groupkey = null;
						if (! is_null($group)) {
							foreach ($group as $col => $length) {
								$groupkey = substr($l[$col], 0, $length);
								break;  // TODO: GROUP BY の複数指定もいつか対応してみたい
							}
						}
						
						// 取得項目の処理
						foreach ($ope as $col => $o) {
							if (is_null($groupkey)) {
								if ($o == 'unique') {
									if (! isset($ret[$col])) {
										$ret[$col] = array();
									}
									$ret[$col][$l[$col]] = $l[$col];
								} elseif ($o == 'sum') {
									if (! isset($ret[$col])) {
										$ret[$col] = 0;
									}
									$ret[$col] += $l[$col];
								}
							} else {
								// TODO: 力技って感じで好きじゃない、リファレンス使って綺麗にグループ化の対応してみたい
								if (! isset($ret[$groupkey])) {
									$ret[$groupkey] = array();
								}
								if ($o == 'unique') {
									if (! isset($ret[$groupkey][$col])) {
										$ret[$groupkey][$col] = array();
									}
									$ret[$groupkey][$col][$l[$col]] = $l[$col];
								} elseif ($o == 'sum') {
									if (! isset($ret[$groupkey][$col])) {
										$ret[$groupkey][$col] = 0;
									}
									$ret[$groupkey][$col] += $l[$col];
								}
							}
						}
					}
				}
				fclose($fp);
			}
		}
		return $ret;
	}
	
	/**
	 * ログ一覧から所定時間内の所定項目のユニークを取る
	 */
	function report_logchoose_group($logs, $start_date, $end_date, $ope, $where = null, $group = null) {
/*
$ope = array(
	col => 'unique|sum'
	...
)
$where => array(
	col => array(...)  // in_array($log[col], $v)
)
$group => aray(
	col => グループ化する文字数 (substr(str, 0, ここ))
)
*/
		$ret = array();
		foreach ($logs as $log) {
			$fp = fopen($log, 'r');
			if ($fp) {
				while ($line = fgets($fp)) {
					$l = explode("\t", $line);
					if (sizeof($l) < max(array_keys($ope))) {
						continue;
					}
					if (! preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $l[0])) {
						continue;
					}
					if (strcmp($start_date, $l[0]) <= 0 and strcmp($l[0], $end_date) < 0) {
						
						// 検索条件があれば加味
						if (is_array($where)) {
							foreach ($where as $col => $val) {
								if (is_array($val)) {
									if (! in_array($l[$col], $val)) {
										continue 2;
									}
								} elseif ($l[$col] != $val) {
									continue 2;
								}
							}
						}
						
						// グループ化の必要があれば対応
						$groupkey = null;
						$groupkeys = array();
						if (! is_null($group)) {
							foreach ($group as $col => $length) {
								$groupkeys[$col] = substr($l[$col], 0, $length);
								$groupkey = substr($l[$col], 0, $length);
							}
						}
						
						// 取得項目の処理
						$r = &$ret;
						foreach ($groupkeys as $col => $key) {
							if (! isset($r[$key])) {
								$r[$key] = array();
							}
							$r = &$r[$key];
						}
						foreach ($ope as $col => $o) {
							if ($o == 'unique') {
								if (! isset($r[$col])) {
									$r[$col] = array();
								}
								$r[$col][$l[$col]] = $l[$col];
							} elseif ($o == 'sum') {
								if (! isset($r[$col])) {
									$r[$col] = 0;
								}
								$r[$col] += $l[$col];
							}
						}
					}
				}
				fclose($fp);
			}
		}
		return $ret;
	}
	
	/** DBからログレポート用のログを抽出 */
	function report_dblogchoose($sum_col, $table, $start, $where, $addwhere) {
		$ret = array(
			'count' => 0, 
			'first' => 0, 
			'total' => 0, 
		);
		
		// ガチャ購入
		$tpl = "SELECT count(distinct uid) AS uid_count, sum(%s) AS s FROM %s WHERE %s %s";
		$sql = sprintf($tpl, $sum_col, $table,  $where, ($addwhere ? " AND {$addwhere} " : ''));
		$arr = db_select($con, $sql);
		if ($arr) {
			$ret['total'] = $arr[0]['s'];
			$ret['count'] = $arr[0]['uid_count'];
			$sql = sprintf("SELECT distinct uid FROM %s WHERE %s", $table, $where);
			$sth = db_exec($con, $sql);
			$uids = array();
			while ($r = db_fetch($sth)) {
				$uids[] = "'" . db_qs($con, $r['uid']) . "'";
				if (sizeof($uids) > 100) {
					$tpl = "SELECT count(distinct uid) as cnt FROM %s WHERE log_date < '%s' AND uid IN (%s)";
					$sql = sprintf($tpl, $table, $start, implode(',', $uids));
					$a = db_select($con, $sql);
					if ($a) {
						$ret['first'] += (sizeof($uids) - $a[0]['cnt']);
					}
					$uids = array();
				}
			}
			if (sizeof($uids) > 0) {
				$tpl = "SELECT count(distinct uid) as cnt FROM %s WHERE log_date < '%s' AND uid IN (%s)";
				$sql = sprintf($tpl, $table, $start, implode(',', $uids));
				$a = db_select($con, $sql);
				if ($a) {
					$ret['first'] += (sizeof($uids) - $a[0]['cnt']);
				}
				$uids = array();
			}
		}
		
		return $ret;
	}
	
	
	/** 各種機関集計の結果を取得 */
	function report_generate($pftype, $start_date, $end_date, $unit, $is_cache_use = 1) {
		
		$cache_cols = array(
			'regist_count'     , 
			'leave_count'      , 
			'login_count'      , 
			'charge_user'      , 
			'charge_user_first', 
			'charge_total'     , 
			'rm_add_total'     , 
			'fm_add_user'      , 
			'fm_add_user_first', 
			'fm_add_total'     , 
			'kakin_user'       , 
			'kakin_user_first' , 
			'kakin_total'      , 
			'gacha_user'       , 
			'gacha_user_first' , 
			'gacha_total'      , 
			'rm_sub_user'      , 
			'rm_sub_user_first', 
			'rm_sub_total'     , 
			'fm_sub_user'      , 
			'fm_sub_user_first', 
			'fm_sub_total'     , 
		);
		
		$calc_cols = array(
			'charge_arpu'      => 0.0, // charge_total / login_count
			'charge_arppu'     => 0.0, // charge_total / charge_user
			'charge_user_rate' => 0.0, // charge_user / login_count
			
			'kain_userate'  => 0.0, // kakin_total / (gacha_total + kakin_total)
			'gacha_userate' => 0.0, // gacha_total / (gacha_total + kakin_total)
			
			'coin_add_total' => 0, // rm_add_total + fm_add_total
			'coin_sub_total' => 0, // rm_sub_total + fm_sub_total
			'coin_subtotal'  => 0, // (rm_add_total + fm_add_total) - (rm_sub_total + fm_sub_total)
		);
		
		
		// 許可する単位
		$allow_unit = array(
			'hour'  => 'l_report_hour', 
			'day'   => 'l_report_day', 
			'week'  => 'l_report_week', 
			'month' => 'l_report_month', 
		);
		
		$ret = array(
			/*
			'is_loaded' => キャッシュからの読み込み済みフラグ,
			'start' => 始端日時 (以上)
			'end'   => 終端日時 (未満)
			'dates' => array(
				処理対象日, 
				...
			), 
			*/
		);
		
		$env = env();
		$con = admin_con();
		
		////////////////////////////////////////////////////
		// パラメータのチェック
		if (! preg_match('/^[0-9]+$/', $pftype)) {
			return array();
		}
		if (! isset($allow_unit[$unit])) {
			return array();
		}
		if (strtotime($start_date) == 0 or strtotime($end_date) == 0 or strtotime($start_date) > strtotime($end_date)) {
			return array();
		}
		
		// 処理対象テーブルの確定
		$table = $allow_unit[$unit];
		
		////////////////////////////////////////////////////
		// 処理対象日リストの生成
		for ($i = 0, $j = 1;strtotime("{$start_date} +{$i}{$unit}") < strtotime($end_date);$i++, $j++) {
			$ts = strtotime("{$start_date} +{$i}{$unit}");
			$ts_end = strtotime("{$start_date} +{$j}{$unit}");
			
			$k = date('Y-m-d H:00:00', $ts);
			
			$dates = array();
			for ($d = $ts;$d < $ts_end;$d += 86400) {
				$dates[date('Y-m-d', $d)] = date('Ymd', $d);
			}
			
			$ret[$k] = array(
				'is_loaded' => 0, 
				'start' => date('Y-m-d H:00:00', $ts), 
				'end'   => date('Y-m-d H:00:00', $ts_end), 
				'dates' => $dates, 
			);
			foreach ($cache_cols as $col) {
				$ret[$k][$col] = 0;
			}
			foreach ($calc_cols as $col => $default_value) {
				$ret[$k][$col] = $default_value;
			}
		}
		//$ret = array_reverse($ret);
		
		////////////////////////////////////////////////////
		// キャッシュからの読み込み
		if ($is_cache_use) {
			$wa = array(
				" '{$start_date}' <= log_date ", 
				" log_date < '{$end_date}' ", 
				" pftype = {$pftype} ", 
			);
			
			$tpl = "SELECT log_date, pftype, %s FROM %s WHERE %s";
			$sql = sprintf($tpl, implode(',', $cache_cols), $table, implode(' AND ', $wa));
			$arr = db_select($con, $sql);
			foreach ($arr as $rec) {
				if (isset($ret[$rec['log_date']])) {
					foreach ($cache_cols as $col) {
						$ret[$rec['log_date']][$col] = $rec[$col];
					}
					$ret[$rec['log_date']]['is_loaded'] = 1;
				}
			}
		}
		
		
		
		////////////////////////////////////////////////////
		// 生データからの読み込みとキャッシュへの格納
		
//mylog(func_get_args());
		foreach ($ret as $log_date => $rec) {
			if (! $rec['is_loaded']) {
set_time_limit(300);
$bt = microtime(true);
				foreach ($cache_cols as $col) {
					$rec[$col] = 0;
				}
				
				/////////////////////////
				// ファイル用検索条件の準備
				$where = array();
				if ($pftype) {
					$where[1] = $pftype;
				}
				
				/*
				// 登録
				$logs = report_logfiles($env['log_bak'], $rec['dates'], 'regist');
				$a = report_logchoose($logs, $rec['start'], $rec['end'], array(2 => 'unique'), $where);
				$ret[$log_date]['regist_count'] = (isset($a[2])?sizeof($a[2]):0);
				
				// 退会
				$logs = report_logfiles($env['log_bak'], $rec['dates'], 'unregist');
				$a = report_logchoose($logs, $rec['start'], $rec['end'], array(2 => 'unique'), $where);
				$ret[$log_date]['leave_count'] = (isset($a[2])?sizeof($a[2]):0);
				
				// ログイン
				$logs = report_logfiles($env['log_bak'], $rec['dates'], 'login');
				$a = report_logchoose($logs, $rec['start'], $rec['end'], array(3 => 'unique', 5 => 'unique'), $where);
				$ret[$log_date]['login_count'] = (isset($a[3])?sizeof($a[3]):0);
				*/
				
				////////////////////////
				// DB用検索条件の準備
				$wa = array(
					sprintf(" '%s' <= log_date ", $rec['start']), 
					sprintf(" log_date < '%s'  ", $rec['end']), 
				);
				if ($pftype) {
					$wa[] = sprintf(" pftype = %d ", $pftype);
				}
				$where = implode(' AND ', $wa);
				
				
				// 登録
				$tpl = "SELECT count(*) AS c FROM regist_log WHERE %s";
				$sql = sprintf($tpl, $where);
				$arr = db_select($con, $sql);
				if ($arr) {
					$ret[$log_date]['regist_count'] = $arr[0]['c'];
				}
				
				// 累計登録数
				$wa = array(
					sprintf(" log_date < '%s'  ", $rec['end']), 
				);
				if ($pftype) {
					$wa[] = sprintf(" pftype = %d ", $pftype);
				}
				$w = implode(' AND ', $wa);
				$tpl = "SELECT count(*) AS c FROM regist_log WHERE %s";
				$sql = sprintf($tpl, $w);
				$arr = db_select($con, $sql);
				if ($arr) {
					$ret[$log_date]['regist_total'] = $arr[0]['c'];
				}
				
				// 退会 (想定されてないので取らない)
				$ret[$log_date]['leave_count'] = 0;
				
				// ログイン
				$tpl = "SELECT count(distinct user_id) AS c FROM login_log WHERE %s";
				$sql = sprintf($tpl, $where);
				$arr = db_select($con, $sql);
				if ($arr) {
					$ret[$log_date]['login_count'] = $arr[0]['c'];
				}
				
				
				// 両替
				$tpl = "SELECT count(distinct uid) AS uid_count, count(distinct user_id) AS user_count, sum(trade_val) AS s FROM l_realmoney_payment WHERE %s";
				$sql = sprintf($tpl, $where);
				$arr = db_select($con, $sql);
				if ($arr) {
					$ret[$log_date]['charge_total'] = $arr[0]['s'];
					if ($arr[0]['uid_count'] > 1) {
						$ret[$log_date]['charge_user'] = $arr[0]['uid_count'];
					} else {
						$ret[$log_date]['charge_user'] = $arr[0]['user_count'];
					}
					$sql = sprintf("SELECT count(*) AS c FROM d_firsttime WHERE %s", str_replace('log_date', 'charge_date', $where));
					$arr = db_select($con, $sql);
					if ($arr) {
						$ret[$log_date]['charge_user_first'] = $arr[0]['c'];
					}
				}
				
				// 課金通貨増減
				$tpl = "SELECT sum(trade_val) AS s FROM l_realmoney_trade WHERE %s AND trade_type = 1";
				$sql = sprintf($tpl, $where);
				$arr = db_select($con, $sql);
				if ($arr) {
					$ret[$log_date]['rm_add_total'] = $arr[0]['s'];
				}
				
				$tpl = "SELECT count(distinct uid) AS uid_count, count(distinct user_id) AS user_count, sum(trade_val) AS s FROM l_realmoney_trade WHERE %s AND trade_type IN (2,3)";
				$sql = sprintf($tpl, $where);
				$arr = db_select($con, $sql);
				if ($arr) {
					$ret[$log_date]['rm_sub_total'] = abs($arr[0]['s']);
					if ($arr[0]['uid_count'] > 1) {
						$ret[$log_date]['rm_sub_user'] = $arr[0]['uid_count'];
						$tpl = "SELECT count(distinct uid) AS c FROM l_realmoney_trade WHERE log_date < '%s' AND uid IN (SELECT uid FROM l_realmoney_trade WHERE %s AND trade_type IN (2,3))";
					} else {
						$ret[$log_date]['rm_sub_user'] = $arr[0]['user_count'];
						$tpl = "SELECT count(distinct user_id) AS c FROM l_realmoney_trade WHERE log_date < '%s' AND user_id IN (SELECT user_id FROM l_realmoney_trade WHERE %s AND trade_type IN (2,3))";
					}
/*
					$sql = sprintf($tpl, $rec['start'], $where);
					$a = db_select($con, $sql);
					if ($a) {
						$ret[$log_date]['rm_sub_user_first'] = $ret[$log_date]['rm_sub_user'] - $a[0]['c'];
					}
*/
					
					$sql = sprintf("SELECT count(*) AS c FROM d_firsttime WHERE %s", str_replace('log_date', 'rm_sub_date', $where));
					$arr = db_select($con, $sql);
					if ($arr) {
						$ret[$log_date]['rm_sub_user_first'] = $arr[0]['c'];
					}
				}
				
				
				// 無料付与通貨増減
				$tpl = "SELECT count(distinct uid) AS uid_count, count(distinct user_id) AS user_count, sum(trade_val) AS s FROM l_freemoney WHERE %s AND trade_type IN (1)";
				$sql = sprintf($tpl, $where);
				$arr = db_select($con, $sql);
				if ($arr) {
					$ret[$log_date]['fm_add_total'] = $arr[0]['s'];
					if ($arr[0]['uid_count'] > 1) {
						$ret[$log_date]['fm_add_user'] = $arr[0]['uid_count'];
					} else {
						$ret[$log_date]['fm_add_user'] = $arr[0]['user_count'];
					}
					$sql = sprintf("SELECT count(*) AS c FROM d_firsttime WHERE %s", str_replace('log_date', 'fm_add_date', $where));
					$arr = db_select($con, $sql);
					if ($arr) {
						$ret[$log_date]['fm_add_user_first'] = $arr[0]['c'];
					}
				}
				
				$tpl = "SELECT count(distinct uid) AS uid_count, count(distinct user_id) AS user_count, sum(trade_val) AS s FROM l_freemoney WHERE %s AND trade_type IN (2,3)";
				$sql = sprintf($tpl, $where);
				$arr = db_select($con, $sql);
				if ($arr) {
					$ret[$log_date]['fm_sub_total'] = abs($arr[0]['s']);
					if ($arr[0]['uid_count'] > 1) {
						$ret[$log_date]['fm_sub_user'] = $arr[0]['uid_count'];
					} else {
						$ret[$log_date]['fm_sub_user'] = $arr[0]['user_count'];
					}
					$sql = sprintf("SELECT count(*) AS c FROM d_firsttime WHERE %s", str_replace('log_date', 'fm_sub_date', $where));
					$arr = db_select($con, $sql);
					if ($arr) {
						$ret[$log_date]['fm_sub_user_first'] = $arr[0]['c'];
					}
				}
				
				
				// 課金アイテム購入
				$tpl = "SELECT count(distinct uid) AS uid_count, count(distinct user_id) AS user_count, sum(subtotal) AS s FROM l_kakin WHERE %s";
				$sql = sprintf($tpl, $where);
				$arr = db_select($con, $sql);
				if ($arr) {
					$ret[$log_date]['kakin_total'] = $arr[0]['s'];
					if ($arr[0]['uid_count'] > 1) {
						$ret[$log_date]['kakin_user'] = $arr[0]['uid_count'];
					} else {
						$ret[$log_date]['kakin_user'] = $arr[0]['user_count'];
					}
					
					$sql = sprintf("SELECT count(*) AS c FROM d_firsttime WHERE %s", str_replace('log_date', 'kakin_date', $where));
					$arr = db_select($con, $sql);
					if ($arr) {
						$ret[$log_date]['kakin_user_first'] = $arr[0]['c'];
					}
				}
				
				
				// ガチャ購入
				$tpl = "SELECT count(distinct uid) AS uid_count, count(distinct user_id) AS user_count, sum(shop_point) AS s FROM l_gacha WHERE %s";
				$sql = sprintf($tpl, $where);
				$arr = db_select($con, $sql);
				if ($arr) {
					$ret[$log_date]['gacha_total'] = $arr[0]['s'];
					if ($arr[0]['uid_count'] > 1) {
						$ret[$log_date]['gacha_user'] = $arr[0]['uid_count'];
					} else {
						$ret[$log_date]['gacha_user'] = $arr[0]['user_count'];
					}
					
					$sql = sprintf("SELECT count(*) AS c FROM d_firsttime WHERE %s", str_replace('log_date', 'gacha_date', $where));
					$arr = db_select($con, $sql);
					if ($arr) {
						$ret[$log_date]['gacha_user_first'] = $arr[0]['c'];
					}
				}
				
				// キャッシュへの登録
//mylog(sprintf('%s > %d : %d', $log_date, mktime(0,0,0), strtotime($rec['end'])));
				if (mktime(0,0,0) > strtotime($rec['end'])) {
					$sql = sprintf("DELETE FROM %s WHERE log_date = '%s' AND pftype = %d", $table, $log_date, $pftype);
					$res = db_exec($con, $sql);
					
					$cols = array();
					$vals = array();
					foreach ($cache_cols as $col) {
						$cols[] = $col;
						$vals[] = intval($ret[$log_date][$col]);
					}
					$tpl = "INSERT INTO %s(log_date, pftype, %s) VALUES('%s', %d, %s)";
					$sql = sprintf($tpl, $table, implode(',', $cols), $log_date, $pftype, implode(',', $vals));
					$res = db_exec($con, $sql);
				}
//mylog(sprintf('%s : %2.2f', $log_date, (microtime(true) - $bt)));
			}
		}
		
		
		////////////////////////////////////////////////////
		// 計算項目の計上
		
		foreach ($ret as $log_date => $rec) {
			$ret[$log_date]['charge_arpu']      = ($rec['login_count'] <= 0 ? 0 : round($rec['charge_total'] / $rec['login_count'], 4));
			$ret[$log_date]['charge_arppu']     = ($rec['charge_user'] <= 0 ? 0 : round($rec['charge_total'] / $rec['charge_user'], 4));
			$ret[$log_date]['charge_user_rate'] = ($rec['login_count'] <= 0 ? 0 : round($rec['charge_user'] / $rec['login_count'], 4));
			$ret[$log_date]['kain_userate']     = (($rec['kakin_total'] + $rec['gacha_total']) <= 0 ? 0 : round($rec['kakin_total'] / ($rec['kakin_total'] + $rec['gacha_total']), 4));
			$ret[$log_date]['gacha_userate']    = (($rec['kakin_total'] + $rec['gacha_total']) <= 0 ? 0 : round($rec['gacha_total'] / ($rec['kakin_total'] + $rec['gacha_total']), 4));
			$ret[$log_date]['coin_add_total']   = ($rec['rm_add_total'] + $rec['fm_add_total']);
			$ret[$log_date]['coin_sub_total']   = ($rec['rm_sub_total'] + $rec['fm_sub_total']);
			$ret[$log_date]['coin_subtotal']    = ($rec['rm_add_total'] + $rec['fm_add_total']) - ($rec['rm_sub_total'] + $rec['fm_sub_total']);
		}
		
		return $ret;
	}
	
	/** 各種機関集計の結果を取得 */
	function report_generate_serverlogin($pftype, $start_date, $end_date, $unit, $is_cache_use = 1) {
		
		// 許可する単位
		$allow_unit = array(
			'hour'  => 'l_report_hour', 
			'day'   => 'l_report_day', 
			'week'  => 'l_report_week', 
			'month' => 'l_report_month', 
		);
		
		$env = env();
		$con = admin_con();
		
		////////////////////////////////////////////////////
		// パラメータのチェック
		if (! preg_match('/^[0-9]+$/', $pftype)) {
			return array();
		}
		if (! isset($allow_unit[$unit])) {
			return array();
		}
		if (strtotime($start_date) == 0 or strtotime($end_date) == 0 or strtotime($start_date) > strtotime($end_date)) {
			return array();
		}
		
		// 処理対象テーブルの確定
		$table = $allow_unit[$unit];
		
		////////////////////////////////////////////////////
		// 処理対象日リストの生成
		for ($i = 0, $j = 1;strtotime("{$start_date} +{$i}{$unit}") < strtotime($end_date);$i++, $j++) {
			$ts = strtotime("{$start_date} +{$i}{$unit}");
			$ts_end = strtotime("{$start_date} +{$j}{$unit}");
			
			$k = date('Y-m-d H:00:00', $ts);
			
			$dates = array();
			for ($d = $ts;$d < $ts_end;$d += 86400) {
				$dates[date('Y-m-d', $d)] = date('Ymd', $d);
			}
			
			$ret[$k] = array(
				'is_loaded' => 0, 
				'start' => date('Y-m-d H:00:00', $ts), 
				'end'   => date('Y-m-d H:00:00', $ts_end), 
				'dates' => $dates, 
			);
		}
		//$ret = array_reverse($ret);
		
		////////////////////////////////////////////////////
		// キャッシュからの読み込み
		if ($is_cache_use) {
			$wa = array(
				" '{$start_date}' <= log_date ", 
				" log_date < '{$end_date}' ", 
				" pftype = {$pftype} ", 
				" reporttype = '{$table}' ", 
			);
			
			$sql = "SELECT log_date, server, login FROM l_report_login WHERE " . implode(' AND ', $wa);
			$arr = db_select($con, $sql);
			foreach ($arr as $rec) {
				if (isset($ret[$rec['log_date']])) {
					$ret[$rec['log_date']][$rec['server']] = $rec['login'];
					$ret[$rec['log_date']]['is_loaded'] = 1;
				}
			}
		}
		
		////////////////////////////////////////////////////
		// 生データからの読み込みとキャッシュへの格納
		foreach ($ret as $log_date => $rec) {
			if (! $rec['is_loaded']) {
				
				/////////////////////////
				// ファイル用検索条件の準備
				$where = array();
				if ($pftype) {
					$where[1] = $pftype;
				}
				
				// ログイン
				$logs = report_logfiles($env['log_bak'], $rec['dates'], 'login');
				$a = report_logchoose($logs, $rec['start'], $rec['end'], array(3 => 'unique', 5 => 'unique'), $where);
				$ret[$log_date]['login_count'] = sizeof($a[3]);
				if (sizeof($a[5]) > 0) {
					foreach ($a[5] as $server) {
						$w = $where;
						$w[5] = $server;
						$arr = report_logchoose($logs, $rec['start'], $rec['end'], array(3 => 'unique'), $w);
						
						// キャッシュの削除と作成
						$tpl = "DELETE FROM l_report_login WHERE reporttype = '%s' AND log_date = '%s' AND pftype = %d AND server = %d";
						$sql = sprintf($tpl, $table, $log_date, $pftype, $server);
						db_exec($con, $sql);
						$tpl = "INSERT INTO l_report_login(reporttype,log_date,pftype,server,login,is_fixed) VALUES('%s','%s',%d,%d,%d,%d)";
						$sql = sprintf($tpl, $table, $log_date, $pftype, $server, sizeof($arr[3]), 1);
						db_exec($con, $sql);
						
						// 戻り値に設定
						$ret[$log_date][$server] = sizeof($arr[3]);
					}
				}
			}
		}
		
		return $ret;
	}
