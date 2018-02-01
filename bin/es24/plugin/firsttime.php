<?PHP

class my_insert {
	
	var $con   ;
	var $table ;
	var $cols  ;
	var $values;
	var $length;
	
	var $maxlength;
	
	function __construct($con, $table, $cols) {
		$this->con = $con;
		$this->table = $table;
		$this->cols = $cols;
		
		$this->values = array();
		$this->length = strlen($this->tpl_ins());
		
		$this->maxlength = 1024000;
	}
	
	function tpl_val() {return "(" . implode(',', array_fill(0, sizeof($this->cols), "'%s'")) . ")";}
	function tpl_ins() {return sprintf("INSERT INTO %s(%s) VALUES", $this->table, implode(',', $this->cols));}
	
	function value() {
		
		$args = array($this->tpl_val());
		foreach (func_get_args() as $v) {
			$args[] = db_qs($this->con, $v);
		}
		$v = call_user_func_array('sprintf', $args);
		$l = (strlen($v) + 1);
		if (($this->length + $l) > $this->maxlength) {
			$this->insert();
		}
		$this->length += $l;
		$this->values[] = $v;
	}
	
	function insert() {
		$res = true;
		if (sizeof($this->values) > 0) {
			$sql = $this->tpl_ins() . implode(',', $this->values);
			$res = db_exec($this->con, $sql);
			if ($res) {
				$this->values = array();
				$this->length = strlen($this->tpl_ins());
			} else {
				// エラー発生時の挙動
			}
		}
		return $res;
	}
}

class my_update {
	var $con = null;
	
	var $table = '';
	var $key = '';
	var $id  = '';
	
	var $default_cmp = '';
	
	var $values = array();
	var $updates = array();
	
	function __construct($con, $table, $key, $id = '') {
		
		// $u = new my_update($con, 'd_firsttime', 'user_id');
		
		$this->con = $con;
		
		$this->table = $table;
		$this->key   = $key;
		
		$this->default_cmp = 'lt';
		
		if ($id) {
			$this->load($id);
		}
	}
	
	function load($id) {
		
		$this->id = $id;
		$this->values = array();
		$this->updates = array();
		
		$sql = sprintf("SELECT * FROM %s WHERE %s = '%s' LIMIT 2", $this->table, $this->key, db_qs($this->con, $this->id));
		$arr = db_select($this->con, $sql);
		if ($arr and sizeof($arr) == 1) {
			$this->values = $arr[0];
		} else {
			// ロード失敗
		}
	}
	
	function set($col, $val, $cmp = '') {
		$c = '' . ($cmp ? $cmp : $this->default_cmp) . '';
		if ($c == 'force' or $c == 'f') {
			$this->updates[$col] = $val;
		} elseif ($c == 'gt' or $c == 'g') {
			if (isset($this->values[$col]) and $val > $this->values[$col]) {
				if (! isset($this->updates[$col]) or $val > $this->updates[$col]) {
					$this->updates[$col] = $val;
				}
			}
		} elseif ($c == 'lt' or $c == 'l') {
			if (isset($this->values[$col]) and $val < $this->values[$col]) {
				if (! isset($this->updates[$col]) or $val < $this->updates[$col]) {
					$this->updates[$col] = $val;
				}
			}
		}
	}
	
	function update() {
		$res = true;
		if (sizeof($this->updates) > 0) {
			$sets = array();
			foreach ($this->updates as $k => $v) {
				$sets[] = sprintf("%s='%s'", $k, db_qs($this->con, $v));
			}
			$tpl = "UPDATE %s SET %s WHERE %s='%s'";
			$sql = sprintf($tpl, $this->table, implode(',', $sets), $this->key, db_qs($this->con, $this->id));
			$res = db_exec($this->con, $sql);
		}
		return $res;
	}
}



	////////////////////////////////////////////////////////////////////////////
	// 初期化とか
	$con = admin_con();
	
	
	
	////////////////////////////////////////////////////////////////////////////
	// まずは regist で登録のみのレコードを用意
	$ins = new my_insert($con, 'd_firsttime', array('pftype', 'user_id', 'uid', 'regist_date'));
	
	$log_files = glob("{$log_dir}/regist_{$exec_date}.log");
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
			while (! feof($fp)) {
				$log = rtrim(fgets($fp), "\r\n");
				
				// 空行は次へ
				if (! $log) {
					continue;
				}
				
				list(
					$log_date, 
					$pftype, 
					$uid, 
					$user_id, 
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				
				// ユーザIDの無いものは除外
				if (! $user_id) {
					continue;
				}
				
				// INSERT予約
				$ins->value($pftype, $user_id, $uid, $log_date);
			}
			fclose($fp);
		}
	}
	
	// 最後にINSERT
	$ins->insert();
	
	
	
	////////////////////////////////////////////////////////////////////////////
	// 各種項目が初回であれば更新
	
	$users = array();
	
	////////////////////////////////////
	// ログイン
	$log_files = glob("{$log_dir}/login_{$exec_date}.log");
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
			while (! feof($fp)) {
				$log = rtrim(fgets($fp), "\r\n");
				
				// 空行は次へ
				if (! $log) {
					continue;
				}
				
				list(
					$log_date, 
					$pftype, 
					$uid, 
					$user_id, 
					$chara_id, 
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				
				if (! $user_id) {
					$user_id = (0xFFFFFF & $chara_id);
				}
				
				$id = ($user_id ? $user_id : $uid);
				if (! isset($users[$id])) {
					$users[$id] = new my_update($con, 'd_firsttime', ($user_id ? 'user_id' : 'uid'), $id);
				}
				$users[$id]->set('login_date', $log_date);
			}
		}
	}
	
	////////////////////////////////////
	// 課金通貨利用
	$log_files = glob("{$log_dir}/kakin_{$exec_date}.log");
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
			while (! feof($fp)) {
				$log = rtrim(fgets($fp), "\r\n");
				
				// 空行は次へ
				if (! $log) {
					continue;
				}
				
				list(
					$log_date, 
					$pftype, 
					$uid, 
					$user_id, 
					$chara_id, 
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				
				if (! $user_id) {
					$user_id = (0xFFFFFF & $chara_id);
				}
				
				$id = ($user_id ? $user_id : $uid);
				if (! isset($users[$id])) {
					$users[$id] = new my_update($con, 'd_firsttime', ($user_id ? 'user_id' : 'uid'), $id);
				}
				$users[$id]->set('kakin_date', $log_date);
			}
		}
	}
	
	////////////////////////////////////
	// 課金通貨利用
	$log_files = glob("{$log_dir}/gacha_{$exec_date}.log");
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
			while (! feof($fp)) {
				$log = rtrim(fgets($fp), "\r\n");
				
				// 空行は次へ
				if (! $log) {
					continue;
				}
				
				list(
					$log_date, 
					$pftype, 
					$uid, 
					$user_id, 
					$chara_id, 
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				
				if (! $user_id) {
					$user_id = (0xFFFFFF & $chara_id);
				}
				
				$id = ($user_id ? $user_id : $uid);
				if (! isset($users[$id])) {
					$users[$id] = new my_update($con, 'd_firsttime', ($user_id ? 'user_id' : 'uid'), $id);
				}
				$users[$id]->set('gacha_date', $log_date);
			}
		}
	}
	
	////////////////////////////////////
	// 無料付与通貨
	$log_files = glob("{$log_dir}/freemoney_{$exec_date}.log");
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
			while (! feof($fp)) {
				$log = rtrim(fgets($fp), "\r\n");
				
				// 空行は次へ
				if (! $log) {
					continue;
				}
				
				list(
					$log_date, 
					$pftype, 
					$uid, 
					$user_id, 
					$chara_id, 
					$trade_val, 
					$trade_type, 
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				
				if (! $user_id) {
					$user_id = (0xFFFFFF & $chara_id);
				}
				
				$id = ($user_id ? $user_id : $uid);
				if (! isset($users[$id])) {
					$users[$id] = new my_update($con, 'd_firsttime', ($user_id ? 'user_id' : 'uid'), $id);
				}
				if ($trade_type == 1) {
					$users[$id]->set('fm_add_date', $log_date);
				} elseif ($trade_type == 2 or $trade_type == 3) {
					$users[$id]->set('fm_sub_date', $log_date);
				}
			}
		}
	}
	
	////////////////////////////////////
	// 両替
	$log_files = glob("{$log_dir}/realmoney_payment_{$exec_date}.log");
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
			while (! feof($fp)) {
				$log = rtrim(fgets($fp), "\r\n");
				
				// 空行は次へ
				if (! $log) {
					continue;
				}
				
				list(
					$log_date, 
					$pftype, 
					$uid, 
					$user_id, 
					$chara_id, 
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				
				if (! $user_id) {
					$user_id = (0xFFFFFF & $chara_id);
				}
				
				$id = ($user_id ? $user_id : $uid);
				if (! isset($users[$id])) {
					$users[$id] = new my_update($con, 'd_firsttime', ($user_id ? 'user_id' : 'uid'), $id);
				}
				$users[$id]->set('charge_date', $log_date);
			}
		}
	}
	
	////////////////////////////////////
	// 課金通貨利用
	$log_files = glob("{$log_dir}/realmoney_trade_{$exec_date}.log");
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
			while (! feof($fp)) {
				$log = rtrim(fgets($fp), "\r\n");
				
				// 空行は次へ
				if (! $log) {
					continue;
				}
				
				list(
					$log_date, 
					$pftype, 
					$uid, 
					$user_id, 
					$chara_id, 
					$trade_val, 
					$trade_type, 
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				
				if (! $user_id) {
					$user_id = (0xFFFFFF & $chara_id);
				}
				
				$id = ($user_id ? $user_id : $uid);
				if (! isset($users[$id])) {
					$users[$id] = new my_update($con, 'd_firsttime', ($user_id ? 'user_id' : 'uid'), $id);
				}
				if ($trade_type == 1) {
					$users[$id]->set('rm_add_date', $log_date);
				} elseif ($trade_type == 2 or $trade_type == 3) {
					$users[$id]->set('rm_sub_date', $log_date);
				}
			}
		}
	}
	
	////////////////////////////////////
	// 旧リアルマネー
	$log_files = glob("{$log_dir}/realmoney_{$exec_date}.log");
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
			while (! feof($fp)) {
				$log = rtrim(fgets($fp), "\r\n");
				
				// 空行は次へ
				if (! $log) {
					continue;
				}
				
				list(
					$log_date, 
					$pftype, 
					$uid, 
					$user_id, 
					$chara_id, 
					$trade_val, 
					$trade_type, 
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				
				if (! $user_id) {
					$user_id = (0xFFFFFF & $chara_id);
				}
				
				$id = ($user_id ? $user_id : $uid);
				if (! isset($users[$id])) {
					$users[$id] = new my_update($con, 'd_firsttime', ($user_id ? 'user_id' : 'uid'), $id);
				}
				if ($trade_type == 1) {
					$users[$id]->set('charge_date', $log_date);
					$users[$id]->set('rm_add_date', $log_date);
				} elseif ($trade_type == 2 or $trade_type == 3) {
					$users[$id]->set('rm_sub_date', $log_date);
				}
			}
		}
	}
	
	
	////////////////////////////////////
	// 更新対象ユーザ全員を更新する
	foreach ($users as $id => $mu) {
		$mu->update();
	}
	