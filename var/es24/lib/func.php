<?PHP
	// ================================================================================================
	// ここから DB 関連の関数
	
	/**
	 * main db への接続を生成
	 */
	function main_con() {
		$env = env();
		$con = db_con($env['master_db']['db_host'], $env['master_db']['db_user'], $env['master_db']['db_pass'], $env['master_db']['db_name']);
		return $con;
	}
	
	/**
	 * master db への接続を生成
	 */
	function master_con() {
		$env = env();
		$con = db_con($env['master_db']['db_host'], $env['master_db']['db_user'], $env['master_db']['db_pass'], $env['master_db']['db_name']);
		return $con;
	}
	
	/**
	 * 管理用 DB への接続を生成する
	 * 
	 * 
	 * 
	 * @return resource DB接続
	 */
	function admin_con() {
		$env = env();
		$con = db_con($env['admin_db']['db_host'], $env['admin_db']['db_user'], $env['admin_db']['db_pass'], $env['admin_db']['db_name']);
		return $con;
	}
	
	/**
	 * 月替わりログ DB への接続を生成する
	 * 
	 * 
	 * 
	 * @return resource DB接続
	 */
	function logdb_con($ym = '') {
		static $con = null;
		if (! $con) {
			$env = env();
			$ym = (preg_match('/^[0-9]{6}$/', $ym) ? $ym : date('Ym'));
			$name = sprintf($env['log_db']['logdb_nametpl'], $ym);
			$con = @mysql_connect($env['log_db']['host'], $env['log_db']['user'], $env['log_db']['pass'], true);
			if ($con) {
				db_exec($con, 'SET NAMES utf8');
				$res = mysql_select_db($name, $con);
				if (! $res) {
					$res = db_exec($con, "CREATE DATABASE {$name}");
					if ($res) {
						$res = mysql_select_db($name, $con);
					}
				}
			}
		}
		return $con;
	}
	
	/**
	 * ndb への接続を生成
	 */
	function ndb_con() {
		$env = env();
		$con = db_con($env['master_db']['db_host'], $env['master_db']['db_user'], $env['master_db']['db_pass'], $env['master_db']['db_name']);
		return $con;
	}
	
	/**
	 * ndb のスレーブへの接続を生成
	 */
	function ndb_slave_con() {
		$env = env();
		
		$d = $env['master_db'];
		if ($env['slave_db']) {
			$d = $env['slave_db'];
		}
		$con = db_con($d['db_host'], $d['db_user'], $d['db_pass'], $d['db_name']);
		
		return $con;
	}
	function slave_con() {return ndb_slave_con();}
	
	/**
	 * 他環境の ndb への接続を生成
	 */
	function ndb_con_other($env) {
		include_once('env/env.ndb.php');
		
		$env_ndb = env_ndb();
		if (isset($env_ndb[$env])) {
			$a = $env_ndb[$env];
			$res = shuffle($a);
			foreach ($a As $b) {
				$con = db_con($b['db_host'], $b['db_user'], $b['db_pass'], $b['db_name']);
				if ($con) {
					return $con;
					break;
				}
			}
		}
	}
	
	/**
	 * データベースへの接続
	 * 
	 * T/O
	 * 
	 * @param string $host 接続先ホスト
	 * @param string $user 接続時に使用するユーザ名
	 * @param string $pass 同じくパスワード
	 * @param string $name 接続先DB名
	 * @return resource MySQL接続リソース
	 * @todo A: 受け取った値のエラーチェック
	 */
	function db_con($host, $user, $pass, $name) {
		static $connects = array();
		$k = "{$host}:{$user}:{$pass}:{$name}";
		
		if (! isset($connects[$k])) {
			$con = @mysql_connect($host, $user, $pass, true);
			if ($con) {
				$res = mysql_select_db($name, $con);
				if ($res) {
					// 本来よろしい方法ではないが文字コードは UTF-8 とする
					db_exec($con, 'SET NAMES utf8');
					
					// 色々細かく設定する場合
//					db_exec($con, 'SET character set utf8');
//					db_exec($con, 'SET character_set_database=utf8');
//					db_exec($con, 'SET character_set_connection=utf8');
					
					$connects[$k] = $con;
				}
				else {
					elog("DB select error ({$k})");
					elog(db_error($con));
				}
			}
			else {
				elog("DB connect error ({$k})");
			}
		}
		
		return $connects[$k];
	}
	
	/**
	 * クエリ発行して結果取得、SELECT
	 * 
	 * データの取得は数値とカラム名の両方
	 * 
	 * @param resource $con DB接続
	 * @param string $sql クエリ
	 * @return array 結果配列
	 * @todo A: エラーチェック
	 */
	function db_select($con, $sql) {
		$ret = array();
		
		$pre_query = microtime(true);
//		mylog("query : {$sql}");
		
		$res = db_exec($con, $sql);
		if ($res) {
			while ($rec = db_fetch($res)) {
				$ret[] = $rec;
			}
		}
		else {
			elog("クエリーの発行に失敗しました [{$sql}]");
			elog(db_error($con));
			$ret = null;
		}
		
		$query_cost = microtime(true) - $pre_query;
		if ($query_cost > 1.0) {log_push('query', array(basename($_SERVER['SCRIPT_FILENAME']), $query_cost, $sql));}
		
		return $ret;
	}
	
	/**
	 * 
	 */
	function db_fetch($res, $fetch_type = '') {
		if (! $fetch_type) {
			return mysql_fetch_array($res, MYSQL_ASSOC);
		} elseif ($fetch_type === 'assoc') {
			return mysql_fetch_array($res, MYSQL_ASSOC);
		} elseif ($fetch_type === 'array') {
			return mysql_fetch_array($res, MYSQL_ARRAY);
		} elseif ($fetch_type === 'row') {
			return mysql_fetch_row($res);
		} else {
			return mysql_fetch_array($res, MYSQL_ASSOC);
		}
	}
	
	/**
	 * クエリ発行して結果取得、非SELECT
	 * 
	 * T/O
	 * 
	 * @param resource $con DB接続
	 * @param string $sql クエリ
	 * @return boolean 結果
	 * @todo A: エラーチェック
	 */
	function db_exec($con, $sql) {
		$pre_query = microtime(true);
//		mylog("query : {$sql}");
		
//		mylog($sql);
		$res = mysql_query($sql, $con);
		if (! $res) {
			elog("クエリーの発行に失敗しました [{$sql}]");
			elog(db_error($con));
		}
		
		$query_cost = microtime(true) - $pre_query;
		if ($query_cost > 1.0) {log_push('query', array(basename($_SERVER['SCRIPT_FILENAME']), $query_cost, $sql));}
		
		return $res;
	}
	
	/** 
	 * insert
	 * 
	 * 
	 * 
	 * @param resource $con DB接続
	 * @param string $table 対象テーブル名
	 * @param array $fields 更新項目 (カラム名 => 値 のハッシュ)
	 */
	function db_insert($con, $table, $fields) {
		
		$res = false;
		
		// INSERT 分の作成
		$cols = array();
		$vals = array();
		foreach ($fields AS $k => $v) {
			$cols[] = $k;
			$vals[] = sprintf("'%s'", qs($con, $v));
		}
		if ($fields) {
			$tpl = "INSERT INTO %s(%s) VALUES(%s)";
			$sql = sprintf($tpl, $table, implode(',', $cols), implode(',', $vals));
			
			$res = db_exec($con, $sql);
		}
		
		return $res;
	}
	
	/** 
	 * update
	 * 
	 * 
	 * 
	 * @param resource $con DB接続
	 * @param string $table 対象テーブル名
	 * @param string $pk 主キーのカラム名
	 * @param integer $pkval 主キーの値
	 * @param array $fields 更新項目 (カラム名 => 値 のハッシュ)
	 */
	function db_update($con, $table, $pk, $pkval, $fields) {
		
		$res = false;
		
		// 更新用クエリの作成
		if ($fields) {
			$sets = array();
			foreach ($fields AS $k => $v) {
				$sets[] = sprintf("%s = '%s'", $k, qs($con, $v));
			}
			$tpl = "UPDATE %s SET %s WHERE %s = %d";
			$sql = sprintf($tpl, $table, implode(',', $sets), $pk, $pkval);
			$res = db_exec($con, $sql);
		}
		
		return $res;
	}
	
	/** 
	 * 主キーを指定して挿入もしくは更新
	 * 
	 * REPLACE にあたるものを自前で実装しているだけ、使えるなら REPLACE の方が都合が良い
	 でも REPLACE は auto_increment がガンガン進むので余り好きではない
	 * 
	 * @param resource $con DB接続
	 * @param string $table 対象テーブル名
	 * @param string $pk 主キーのカラム名
	 * @param integer $pkval 主キーの値
	 * @param array $fields 更新項目 (カラム名 => 値 のハッシュ)
	 */
	function db_replace($con, $table, $pk, $pkval, $fields) {
		
		$res = false;
		
		// 更新用クエリの作成
		if ($fields) {
			$sets = array();
			foreach ($fields AS $k => $v) {
				$sets[] = sprintf("%s = '%s'", $k, qs($con, $v));
			}
			$tpl = "UPDATE %s SET %s, last_update = '%s' WHERE %s = %d";
			$sql = sprintf($tpl, $table, implode(',', $sets), date('Y-m-d H:i:s'), $pk, $pkval);
			$res = db_exec($con, $sql);
			
			if (mysql_affected_rows($con) == 0) {
				// 無いので作成
				$cols = array();
				$vals = array();
				foreach ($fields AS $k => $v) {
					$cols[] = $k;
					$vals[] = sprintf("'%s'", qs($con, $v));
				}
				
				$tpl = "INSERT INTO %s(%s,%s) VALUES(%d,%s)";
				$sql = sprintf($tpl, $table, $pk, implode(',', $cols), $pkval, implode(',', $vals));
				
				$res = db_exec($con, $sql);
			}
		}
		
		return $res;
	}
	
	/**
	 * 文字列を SQL 本文に埋め込むようにエスケープ
	 * 
	 * T/O
	 * 
	 * @param resource $con DB接続
	 * @param string $str 対象文字列
	 * @return string 結果文字列
	 */
	function db_qs($con, $str) {
		if (is_resource($con) or is_object($con)) {
			return mysql_real_escape_string($str, $con);
		} else {
			return mysql_real_escape_string($con, $str);
		}
	}
	
	/** エラーがあれば返す */
	function db_error($con) {
		return mysql_error($con);
	}
	/** エラー番号があれば返す */
	function db_errno($con) {
		return mysql_errno($con);
	}
	
	// ここまで DB 関連の関数
	// ================================================================================================
	// ここからその他システム関連の関数
	
	// エラーのキャプチャ
	function error_handler($errno, $errmsg, $filename, $linenum) {
		
		if ($errno != E_NOTICE && $errno != E_STRICT) {
			$str = "[{$errno}] {$errmsg} in {$filename} on line {$linenum}";
			
			elog($str);
			
			// TODO：公開時にはコメント化すること
			// 画面に出力
			//print date("Y-m-d H:i:s > ").$str."\n";
		}
	}
	
	/**
	 * HTML表示用にエスケープ
	 */
	function esc($str) {
		if (is_array($str)) {
			$a = array();
			foreach ($str AS $k => $v) {
				$a[$k] = esc($v);
			}
			return $a;
		}
		else {
			return htmlspecialchars("" . $str . "", ENT_QUOTES, "UTF-8");
		}
	}
	
	/**
	 * デバッグ用のログ
	 */
	function mylog($str) {
		$s = (is_array($str) ? print_r($str, true) : $str);
		@error_log(date('Y-m-d H:i:s ') . $_SERVER['SCRIPT_NAME'] . " > {$s}\n", 3, '../../log/debug.log');
	}
	/**
	 * エラー用のログ
	 */
	function elog($str) {
		$s = (is_array($str) ? print_r($str, true) : $str);
		@error_log(date('Y-m-d H:i:s ') . $_SERVER['SCRIPT_NAME'] . " > {$s}\n", 3, '../../log/error.log');
	}
	
	/**
	 * ログを出力
	 * 
	 * 
	 * 
	 * @param string $logtype ログファイル名の接頭文字
	 * @param string $log 出力するログ本文 (改行文字は不要)
	 * @return int file_put_contents() の戻り値
	 */
	function log_push($logtype, $log) {
		$env = env();
		
		$filepath = sprintf('../../log/%s_%s.log',  $logtype, date('Ymd'));
		$l = $log;
		if (is_array($log)) {
			$l = implode("\t", $log);
		}
		return @file_put_contents($filepath, date('Y-m-d H:i:s') . "\t" . $l . "\r\n", FILE_APPEND );
	}
	
	/**
	 * HTTP接続と結果取得
	 * 
	 * T/O
	 * 
	 * @param string $url 接続先URL
	 * @param string $method 取得メソッド、省略時は GET
	 * @param string $header 追加ヘッダ、省略時はなし
	 * @param array $post POSTデータ、省略時はなし
	 * @return resource 結果本文
	 */
	function http($url, $method='GET', $headers='', $post=array('')){
		$ret = '';
		
		$URL = parse_url($url);
		$h = '';
		if (is_array($headers)) {
			foreach ($headers As $k => $v) {
				$h .= "{$k}: {$v}\r\n";
			}
		}
		else {
			$h = $headers;
		}
		$h .= "Host: {$URL['host']}\r\n";
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$h .= "User-Agent: {$_SERVER['HTTP_USER_AGENT']}\r\n";
		}
//mylog(__FILE__);mylog(__LINE__);
//mylog($url);mylog($postdata);mylog($h);
		
		$me = strtoupper($method);
		if ($me == 'POST') {
			
			$postdata = (is_array($post) ? http_build_query($post) : $post);
			
			if (is_array($headers) && ! isset($headers['Content-Type'])) {
				$h .= "Content-Type: application/x-www-form-urlencoded\r\n";
			}
			if (is_array($headers) && ! isset($headers['Content-Length'])) {
				$h .= "Content-Length: ".strlen($postdata)."\r\n";
			}
			
			$context = array(
				'http' => array(
					'method'  => 'POST', 
					'content' => $postdata, 
					'header'  => $h, 
					'timeout' => 4.0, 
					'max_redirects' => 0,    // follow_location : 0 でも可
					'ignore_errors' => true, 
					
					//'protocol_version' => 1.1,   // PHP 5.3.0 以降でのみ使える
				)
			);
			
			$ret = @file_get_contents($url, false, stream_context_create($context));
		}
		elseif ($me == 'GET') {
			$context = array(
				'http' => array(
					'method'  => 'GET', 
					'header'  => $h, 
					'timeout' => 4.0, 
					'max_redirects' => 0,    // follow_location : 0 でも可
					'ignore_errors' => true, 
					
					//'protocol_version' => 1.1,   // PHP 5.3.0 以降でのみ使える
				)
			);
			
			$ret = @file_get_contents($url, false, stream_context_create($context));
		}
		elseif ($me == 'DELETE') {
			
			// TODO: 動作チェックしてない
			
			$context = array(
				'http' => array(
					'method'  => 'DELETE', 
					'header'  => $h, 
					'timeout' => 4.0, 
					'max_redirects' => 0,    // follow_location : 0 でも可
					'ignore_errors' => true, 
					
					//'protocol_version' => 1.1,   // PHP 5.3.0 以降でのみ使える
				)
			);
			
			$ret = @file_get_contents($url, false, stream_context_create($context));
		}
//mylog(__FILE__);mylog(__LINE__);
//mylog($http_response_header);
//mylog($ret);
		
		
		return $ret;
	}
	
	/**
	 * 昔使ってたHTTP接続と結果取得
	 * 
	 * 今は基本 http() を使う、外部仕様は完全に同じ
	 * ただ DELETE リクエストが http() だと上手く行かないのでこっちが残ってる
	 * 
	 * @param string $url 接続先URL
	 * @param string $method 取得メソッド、省略時は GET
	 * @param string $header 追加ヘッダ、省略時はなし
	 * @param array $post POSTデータ、省略時はなし
	 * @return resource 結果本文
	 */
	function old_http($url, $method="GET", $headers="", $post=array("")){
		$ret = http2($url, $method, $headers, $post);
		return $ret['body'];
	}
	
	/**
	 * 色々取得する HTTP リクエスト
	 * 
	 * レスポンスコードやヘッダも取得する
	 *  array(
	 *    'code' => n, 
	 *    'header' => str, 
	 *    'body' => str, 
	 *  );
	 * その他色々
	 * 諸事情により結果の生成に成功した場合の戻り値は $GLOBALS['http2_results'] にも格納される
	 * 
	 * @param string $url 接続先URL
	 * @param string $method 取得メソッド、省略時は GET
	 * @param string $header 追加ヘッダ、省略時はなし
	 * @param array $post POSTデータ、省略時はなし
	 * @return array 各種結果リスト
	 */
	function http2($url, $method="GET", $headers="", $post=array("")){
		
		$ret = array(
			'code'   => '', 
			'header' => array(), 
			'body'   => '', 
		);
		
		do {
			////////////////////////////////////////////////
			// リクエストの下準備
			$URL = parse_url($url);
			if (isset($URL['query'])) {
				$URL['query'] = "?".$URL['query'];
			} else {
				$URL['query'] = "";
			}
			if (!isset($URL['port'])) $URL['port'] = 80;
			
			////////////////////////////////////////////////
			// リクエストヘッダの作成
			$request  = $method." ".$URL['path'].$URL['query']." HTTP/1.0\r\n";
			$request .= "Host: ".$URL['host']."\r\n";
			$request .= "User-Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\r\n";
			if (isset($URL['user']) && isset($URL['pass'])) {
				$request .= "Authorization: Basic ".base64_encode($URL['user'].":".$URL['pass'])."\r\n";
			}
			
			if (is_array($headers)) {
				foreach ($headers AS $k => $v) {
					$request .= "{$k}:{$v}\r\n";
				}
			}
			else {
				$request .= $headers;
			}
			
			if (strtoupper($method) == "POST" || strtoupper($method) == "PUT") {
				$postdata = '';
				if (is_array($post)) {
					while (list($name, $value) = each($post)) {
						$POST[] = $name."=".urlencode($value);
					}
					$postdata = implode("&", $POST);
				}
				else {
					$postdata = $post;
				}
				
				// 不足してるならヘッダを追加
				if (! strpos($request, 'Content-Type')) {
					$request .= "Content-Type: application/x-www-form-urlencoded\r\n";
				}
				if (! strpos($request, 'Content-Length')) {
					$request .= "Content-Length: ".strlen($postdata)."\r\n";
				}
				
				$request .= "\r\n";
				$request .= $postdata;
			} else {
				$request .= "\r\n";
			}
//mylog('------------------------------------------------------');
//mylog(sprintf('%s : %d', __FILE__, __LINE__));
//mylog($request);
//mylog('------------------------------------------------------');
			
			
			////////////////////////////////////////////////
			// リクエストヘッダの送信
			$fp = @fsockopen($URL['host'], $URL['port']);   // ソケット開くときは色々警告が出やすいので無理やり抑制
			if (!$fp) {
				return null;
				//die("ERROR\n");
			}
			fputs($fp, $request);
			
			
			////////////////////////////////////////////////
			// レスポンスヘッダの受信
			$response_code = '';
			$response_header = array();
			$content_length = -1;
			
			// 先頭行は書式が違うので先に読み込み
			$l = fgets($fp, 4096);
			list($http_version, $response_code, $response_code_str) = explode(' ', $l, 3);
//mylog('------------------------------------------------------');
//mylog(sprintf('%s : %d', __FILE__, __LINE__));
//mylog($l);
//mylog('------------------------------------------------------');
			
			// ヘッダ全部を読み込み
			while (!feof($fp)) {
				$l = fgets($fp, 4096);
				$head .= $l;
				$l = trim($l);
				if (stripos($l, 'Content-Length') !== false) {
					list(, $content_length) = explode(':', $l);
					$content_length = trim($content_length);
				}
				if ($l == '') {
					break;
				}
				
				list($k,$v) = explode(':', $l, 2);
				$response_header[$k] = $v;
			}
//mylog('------------------------------------------------------');
//mylog(sprintf('%s : %d', __FILE__, __LINE__));
//mylog($headers);
//mylog('------------------------------------------------------');
			
			////////////////////////////////////////////////
			// 本文の受信
			$body = '';
			
			if ($content_length > 0) {
				$body = fread($fp, $content_length);
			}
			else {
				while (! feof($fp)) {
					$body .= fread($fp, 4096);
				}
			}
			fclose($fp);
//mylog('------------------------------------------------------');
//mylog(sprintf('%s : %d', __FILE__, __LINE__));
//mylog($body);
//mylog('------------------------------------------------------');
			
			////////////////////////////////////////////////
			// 戻り値の作成
			$ret['code'] = $response_code;
			$ret['codestr'] = $response_code_str;
			$ret['header'] = $response_header;
			$ret['body'] = $body;
			
			// グローバル空間にも確保
			$GLOBALS['http2_results'] = $ret;
			
		} while(0);
		
		return $ret;
	}
	
	/**
	 * xs:dateTime 書式の GMT を良く使う日付時刻に変換
	 * 
	 * @param string $gmt xs:dateTime書式のGMT
	 * @return string Y-m-d H:i:s
	 */
	function gmt_to_date($gmt) {
		list($d, $t) = explode('T', $gmt);
		list($year, $mon, $day) = explode('-', $d);
		list($hour, $min, $sec) = explode(':', $t);
		list($sec) = explode('.', $sec);
		$ts = gmmktime($hour, $min, $sec, $mon, $day, $year);
		$datetime = date('Y-m-d H:i:s', $ts);
		
		return $datetime;
	}
	
	/**
	 * 日付時刻の差を日数で出す
	 * 
	 * タイムスタンプで計算しているので 2038 年問題がある
	 * 端数は切り捨て
	 * 
	 * よく使う "ｎ日経過しているか" という処理は
	 *  $d = my_date_diff_day($begin_date, $cur_date);
	 *  if ($d < $n_days) {
	 *   // $n_days 経過していなかった場合の処理
	 *  }
	 * といった感じ
	 * 
	 * タイムゾーンの設定をしていないと警告が出るので出たら
	 *  ini_set('date.timezone', 'Asia/Tokyo');
	 * とか書き加えておく
	 * 
	 * @param string $date1 日付時刻文字列
	 * @param string $date2 日付時刻文字列
	 * @param integer 差分日数
	 */
	function my_date_diff_day($date1, $date2) {
		
		$ut1 = strtotime($date1);
/* 色々考えたけど止めた
		$ut1 = 0;
		if (preg_match('[0-9]+^$', $date1)) {
			$ut1 = $date1;
		}
		elseif (preg_match('[0-9]+^$', $date1)) {
			$ut1 = $date1;
		}
		else {
			$ut1 = strtotime($date1);
		}
*/
		
		$ut2 = strtotime($date2);
		
		$diff = ($ut2 - $ut1);
		
		return intval($diff / 86400);  // 60*60*24 = 86400
	}
	
	/**
	 * 配列を再帰的にマージする
	 * 
	 * array_merge_recursive() との違いは同じキーなら後者で上書きすること
	 */
	function array_merge_recursive_overwrite($a, $b) {
		$ret = $a;
		if (is_array($a) && is_array($b)) {
			foreach ($b AS $k => $v) {
				if (is_array($v) && is_array($a[$k])) {
					$ret[$k] = array_merge_recursive_overwrite($a[$k], $b[$k]);
				}
				else {
					$ret[$k] = $v;
				}
			}
		}
		return $ret;
	}
	
	// ここまでその他システム関連の関数
	// ================================================================================================
?>
