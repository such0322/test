<?PHP
	$strres = string_resource();
	$code_master = code_master();
	$tables = tables();
	
	$stat_units = array(
/*
		'H' => array(
			'name'    => $strres['statunit']['hour'],       // 表示名称
			'ts_unit' => 3600,                              // 秒単位での集計間隔
			
			'format'  => '%Y year %m month %d day %H hour', // mysql の GROUP BY で使って後で表示用に変換するもの
			'dformat' => '%Y-%m-%d %H:00:00',               // mysql 側の date_format で使うテンプレート
			'pformat' => 'Y 年 m 月 d 日 H 時',             // PHP の date() に渡す元文字列
			
			'df'      => '%Y-%m-%d %H:00:00',               // phpのstrtotime で解釈する用の mysql で動かす date_format 用の書式
			'range' => '+1 hour', 
		), 
*/
		'd' => array(
			'name' => $strres['statunit']['day'],
			'df' => '%Y-%m-%d', 
			'range' => '+1 day', 
		), 
		'W' => array(
			'name' => $strres['statunit']['week'],
			'df' => '%YW%u', // mysql の date_format(now(),'%u') と php の date('W') が同じ値になる
			'range' => '+1 week', 
		), 
		'm' => array(
			'name' => $strres['statunit']['month'],
			'df' => '%Y-%m-01', 
			'range' => '+1 month', 
		), 
		'y' => array(
			'name' => $strres['statunit']['year'], 
			'df' => '%Y-01-01', 
			'range' => '+1 year', 
		), 
	);
	
	
	/** 番号と名前の対照表 */
	function code_master() {
		$user_vars = user_vars_load();
		$strres = string_resource();
		
		$ret = array_merge($user_vars, ngp_code_master());
		
		return $ret;
	}
	
	/** 検索とかしたい項目のまとめ */
	function tables() {
		
		$tables_filename = 'env/tables.php';
		$ret = ngp_tables();
		
		if (file_exists($tables_filename)) {
			$strres = string_resource();
			
			include($tables_filename);
			
			foreach ($tables AS $k => $v) {
				foreach ($v AS $col => $conf) {
					if (isset($strres['TableMaster'][$col])) {
						$tables[$k][$col]['name'] = $strres['TableMaster'][$col];
					}
				}
			}
			
			$ret = array_merge($ret, $tables);
		}
		return $ret;
	}
	