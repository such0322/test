<?php
	
	////////////////////////////////////////////////////////////////////////////
	// ここから共通処理
	
	// 最初に NOTICE だけは抑制しておく
	ini_set('display_errors', 'On');
	error_reporting(E_ALL ^ E_NOTICE);
	
	// ロケールの設定 (fgetcsv() がこれ設定しないと動かない)
	setlocale(LC_ALL, 'ja_JP.UTF-8');
	
	// 文字コードの設定
	ini_set("default_charset","UTF-8");
	ini_set("mbstring.http_input","UTF-8");
	ini_set("mbstring.http_output","UTF-8");
	ini_set("mbstring.internal_encoding","UTF-8");
	
	// magic_quote が ON の場合の対応、_GET も _COOKIEも対応しない
	if (get_magic_quotes_gpc()) {
		foreach ($_POST AS $k => $v) {
			if (! is_array($v)) {
				$_POST[$k] = stripslashes($v);
			}
		}
	}
	
	// スクリプト群の基準となるディレクトリ
	//$root = dirname($_SERVER['SCRIPT_FILENAME']);
	
	// その他ライブラリ等の読み込み
	include_once('spyc.php');
	include_once('smarty/Smarty.class.php');
	
	// ここまで共通処理
	////////////////////////////////////////////////////////////////////////////
	
	/** クライアントの言語を返す */
	function es2_client_lang() {
		
		$ret = 'ja';
		
		// 戻り値候補
		$ret_list = array(
			'ja', 
			'en', 
			'zh', 
			
			// 'de', 'fr', 'it', 'ru', ...
		);
		
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$a = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$primary_lang = strtolower($a[0]);
			list($lang) = explode('-', $primary_lang);
			foreach ($ret_list AS $v) {
				if (strpos($lang, $v) !== false) {
					$ret = $v;
					break;
				}
			}
		}
		
		return $ret;
	}
	
	/** ユーザが好むタイムゾーンの時間に直す */
	function to_user_time($datetime) {
		$timezone = es_conf('timezone');
		$dt = new DateTime($datetime);
		$dt->setTimezone(new DateTimeZone($timezone['name']));
		return $dt->format('Y-m-d H:i:s');
	}
	
	/** ユーザの好むタイムゾーンと現在のシステムのタイムゾーンが不一致かどうか調べる */
	function is_fav_tz() {
		$timezone = es_conf('timezone');
		$dt = new DateTime();
		return ($timezone['name'] == $dt->getTimezone()->getName());
	}
	
	/** iOS系などのバージョン依存な部分の文字列を置き換える */
	function version_replace($text, $version) {
		$ret = '';
		if (intval($version) > 0) {
			$ret = str_replace('{version}', intval($version), $text);
		} else {
			$ret = preg_replace('/.\{version\}/', '', $text);
		}
		return $ret;
	}
	
	/**
	 * 文字列リソースの読み込み
	 */
	function string_resource() {
		static $ret = null;
		
		$files = array(
			$GLOBALS['root'] . '/var/string_table_utf8.csv', 
			$GLOBALS['root'] . '/var/string_table_utf8_prj.csv', 
		);
		$files2 = array(
			$GLOBALS['root'] . '/var/string_table2_utf8.csv', 
			$GLOBALS['root'] . '/var/string_table2_utf8_prj.csv', 
		);
		//$filepath = $GLOBALS['root'] . '/var/string_table_utf8.csv';
		
		if (! $ret) {
			$ret = array();
			foreach ($files AS $filepath) {
				$ret = array_merge_recursive_overwrite($ret, string_resource_load($filepath));
			}
			foreach ($files2 AS $filepath) {
				$ret = array_merge_recursive_overwrite($ret, string_resource2_load($filepath));
			}
		}
		return $ret;
	}
	function string_resource_load($filepath, $is_all = 0) {
		$ret = array();
		if (file_exists($filepath)) {
			$fp = fopen($filepath, 'r');
			if ($fp) {
				$ret = string_resource_parse($fp, $is_all);
				fclose($fp);
			}
		}
		return $ret;
	}
	function string_resource_parse($fp, $is_all = 0) {
		$ret = array();
		
		if ($fp) {
			
			fgetcsv($fp);  // ラベル行なので先頭行は読み飛ばす
			
			// グループ省略時は直前のグループと同じにする、最初が空ならデフォルトは es2 グループとする
			$last_group = 'es2';
			
			while ($line = fgetcsv($fp)) {
//var_dump($line);
				
				// 番号、グループ、キー、値 の４点が最低でも必要
				if (sizeof($line) < 4) {
					continue;
				}
				
				// 各項目の値を確保
				$no      = $line[0];
				$group   = $line[1];
				$key     = $line[2];
				$val     = $line[3];
				
				// グループ欄が空なら前と同じ
				if (! $group) {
					$group = $last_group;
				}
				
				$last_group = $group;
				
				// するなら値を調整してから格納
				//$val = nl2br(esc($val));  // esc は htmlspecialchars() しかしない
				if (! is_array($ret[$group])) {
					$ret[$group] = array();
				}
				if ($is_all) {
					$ret[$group][$key] = $line;
				}
				else {
					$ret[$group][$key] = $val;
				}
			}
		}
		
		return $ret;
	}
	
	/** 文字列リソースの読み込み ver2 */
	function string_resource2_load($filepath, $lang = '') {
		$ret = array();
		
		$default_colnum = 4;  // 3:ja, 4:en, 5:zh
		
		if (file_exists($filepath)) {
			$fp = fopen($filepath, 'r');
			if ($fp) {
				
				// 先頭３バイト読み込み、BOMなら読み飛ばす
				$bom = fread($fp, 3);
				if ($bom != "\xEF\xBB\xBF") {
					// BOMでなかったのでシークポイントを先頭に
					fseek($fp, 0, SEEK_SET);
				}
				
				// ラベル行から取得する項目番号を確保
				$colnum = -1;
				$l = ($lang ? $lang : es2_client_lang());
				$line = fgetcsv($fp);
				foreach ($line As $k => $v) {
					if ($v == $l) {
						$colnum = $k;
						break;
					}
				}
				if ($colnum < 0) {
					// 希望する言語のリソースが無かった
					$colnum = $default_colnum;
				}
				
				// グループ省略時は直前のグループと同じにする、最初が空ならデフォルトは es2 グループとする
				$last_group = 'es2';
				
				while ($line = fgetcsv($fp)) {
//var_dump($line);
					
					// 番号、グループ、キー、値 の４点が最低でも必要
					if (sizeof($line) < 4) {
						continue;
					}
					
					// 各項目の値を確保
					$group   = $line[1];
					$key     = $line[2];
					$val     = $line[$colnum];
					
					// グループ欄が空なら前と同じ
					if (! $group) {
						$group = $last_group;
					}
					
					$last_group = $group;
					
					// するなら値を調整してから格納
					//$val = nl2br(esc($val));  // esc は htmlspecialchars() しかしない
					
					// 戻り値に値を格納
					if (! is_array($ret[$group])) {
						$ret[$group] = array();
					}
					$ret[$group][$key] = $val;
				}
				
				fclose($fp);
			}
		}
		
		return $ret;
	}
	
	
	/**
	 * ユーザ編集コードマスタを読み込む
	 */
	function user_vars_load($link_enable = 1) {
		$f = $GLOBALS['root'] . '/var/user_vars.yaml';
		$user_vars = spyc_load_file($f);
		if ($link_enable) {
			foreach ($user_vars as $k => $v) {
				if (sizeof($v) == 1 and isset($v['linkto'])) {
					$user_vars[$k] = $user_vars[$v['linkto']];
				}
			}
		}
		return $user_vars;
	}
	
	/**
	 * ユーザ編集コードマスタを更新する
	 */
	function user_vars_update($name, $vars) {
		$f = $GLOBALS['root'] . '/var/user_vars.yaml';
		
		$user_vars = user_vars_load(0);
		$user_vars[$name] = $vars;
		$s = Spyc::YAMLDump($user_vars);
		file_put_contents($f, $s);
		
		return $user_vars;
	}
	
	/**
	 * 設定ファイルの読み込み
	 */
	function es_conf($key = '') {
		static $ret = null;
		if (! $ret) {
			$ret = array(
				'Plugin' => array(), 
			);
			
			$conf_files = array(
				$GLOBALS['root'] . '/res/es_conf.yaml', 
				$GLOBALS['root'] . '/env/prj_conf.yaml', 
			);
			foreach ($conf_files AS $filepath) {
				if (file_exists($filepath)) {
					$a = Spyc::YAMLLoad($filepath);
					
					// Plugin だけはメニュー単位で管理したいので書き換え方が変わる
					if (isset($a['Plugin'])) {
						$ret['Plugin'] = array_merge($ret['Plugin'], $a['Plugin']);
					}
					
					$ret = array_merge_recursive_overwrite($ret, $a);
				}
			}
			
			// 環境依存 (dev1とか本番とか) で権限を変えたい場合
			$f = $GLOBALS['root'] . '/env/auth_conf.yaml';
			if (file_exists($f)) {
				$a = Spyc::YAMLLoad($f);
				if (is_array($a)) {
					if (isset($a['Plugin']) and is_array($a['Plugin'])) {
						foreach ($a['Plugin'] as $k => $v) {
							if (isset($ret['Plugin'][$k]) and isset($v['GroupNames'])) {
								$ret['Plugin'][$k]['GroupNames'] = $v['GroupNames'];
							}
						}
						unset($a['Plugin']);
					}
					if (sizeof($a) > 0) {
						foreach ($a as $k => $v) {
							$ret[$k] = $v;
						}
					}
				}
			}
		}
		
		if ($key) {
			return $ret[$key];
		}
		else {
			return $ret;
		}
	}
	
	/**
	 * ログの設定ファイルを読み込む
	 */
	function log_search_conf() {
		
		// 設定ファイルの読み込み
		$a = $b = array();
		if (file_exists('lib/ngp_basic_logs_conf.php')) {
			include_once('lib/ngp_basic_logs_conf.php');
			$a = $loglist;
		}
		if (file_exists('env/log_search_conf.php')) {
			include_once('env/log_search_conf.php');
			$b = $loglist;
		}
		$loglist = array_merge($a, $b);
		
		// ここで文字列リソース対応
		$strres = string_resource();
		
		if (isset($strres['LogName'])) {
			foreach ($loglist As $prefix => $log) {
				if (isset($strres['LogName'][$prefix])) {
					$loglist[$prefix]['name'] = $strres['LogName'][$prefix];
				}
			}
		}
		
		if (isset($strres['LogConfDefault'])) {
			foreach ($loglist As $prefix => $log) {
				foreach ($strres['LogConfDefault'] AS $k => $v) {
					if ($loglist[$prefix]['cols'][$k]) {
						$loglist[$prefix]['cols'][$k]['name'] = $v;
					}
				}
			}
		}
		
		if (isset($strres['LogConf'])) {
			foreach ($loglist As $prefix => $log) {
				foreach ($loglist[$prefix]['cols'] AS $k => $v) {
					if ($strres['LogConf']["{$prefix}.{$k}"]) {
						$loglist[$prefix]['cols'][$k]['name'] = $strres['LogConf']["{$prefix}.{$k}"];
					}
				}
			}
		}
		
		return $loglist;
	}
	
	/**
	 * ログを出力
	 * 
	 * 指定したファイル名は末尾に日付と拡張子を付ける
	 * 
	 * @param string $file ログファイルのプレフィックス
	 * @param string $log 記録する文字列
	 * @return null なし
	 * @todo ユーザの出力
	 */
	function es_log($file, $log){
		if ($_SESSION['session_login_account'] != 'root') {
			
			$str = date("Y-m-d H:i:s");
			if (is_array($log)) {
				foreach ($log As $l) {
					$s = strtr ($l, array(
						"\\" => "\\\\", 
						"\t" => "\\t", 
						"\r" => "\\r", 
						"\n" => "\\n", 
					));
					$str .= "\t{$s}";
				}
				$str .= "\n";
			}
			else {
				$log .= "\t{$log}\n";
			}
			$filepath = sprintf('%s/logs/%s_%s.log', $GLOBALS['root'], $file, date('Ymd'));
			
			file_put_contents($filepath, $str, FILE_APPEND);
			//error_log(3, $filepath, $str);
		}
/*
	enc
		$s = strtr ($s, array(
			"\\" => "\\\\", 
			"\t" => "\\t", 
			"\r" => "\\r", 
			"\n" => "\\n", 
		));
	
	dec
		$s = strtr ($s, array(
			"\\n"  => "\n", 
			"\\r"  => "\r", 
			"\\t"  => "\t", 
			"\\\\" => "\\", 
		));
*/
	}
	
	/** デバッグ用ログ出力 */
	function dlog($log){
		$str = date('Y-m-d H:i:s > ') . (is_array($log) ? print_r($log, true) : $log) . "\n";
		$filepath = $GLOBALS['root'] . '/logs/debug.log';
		
		file_put_contents($filepath, $str, FILE_APPEND);
		//error_log(3, $filepath, $str);
	}
	
	/**
	 * ファイルの読み込みを行う
	 * 
	 * ファイルパスを指定しその中身を返す、ファイルが無い場合は空文字列を返す。
	 * 
	 * @param string $filename 読み込みを行うファイル名
	 * @return mixed ファイルの中身、ファイルが存在しない場合は false
	 */
	function read_file($filename) {
		$ret = '';
		if (file_exists($filename)) {
			$ret = file_get_contents($filename);
		}
		return $ret;
	}
	
	/**
	 * パラメータを取得
	 * 
	 * 改行等で区切られた文字列から "パラメータ 値" の値を取得する。
	 * 例：<code>
	 * $data = <<<_DATA_
	 * hoge ほげほげ
	 * hige ひげひげ
	 * _DATA_;
	 * 
	 * get_parameter('hoge', $data); // ほげほげ
	 * </code>
	 * 主にコンフィグの読み込みで使用する。
	 * 
	 * @param string $parameter パラメータ名
	 * @param string $es_data 取得元のデータ
	 * @return string 取得した文字列
	 */
	function get_parameter($parameter,$es_data){
		preg_match_all("/{$parameter} (.*)/i", $es_data, $match);
		return trim($match[1][0]);
	}
	
	/**
	 * タグの中身をまとめて取得する
	 * 
	 * タグの中身を取得する処理を合致するタグすべてに対して実行。
	 * 
	 * @param string $tag_name タグの名称
v	 * @param string $es_data 取得元のデータ
	 * @return array 取得結果の配列
	 */
	function get_tag_array($tag_name,$es_data){
		preg_match_all("/<".$tag_name." (.*?)>(.*?)<\/".$tag_name.">/si",$es_data,$match);
		for($i=0;$i<count($match[1]);$i++){
			$value[$match[1][$i]]=$match[2][$i];
		}
		return $value;
	}
	
	/**
	 * タグの中身を取得する
	 * 
	 * <hoge>ほげ</hoge> といったタグで構成された文字列からタグの中身を取得する。
	 * ただし属性がある場合は取得できない。取得は最初に合致したもののみ取得。
	 * 
	 * @param string $tag_name タグの名称
	 * @param string $es_data 取得元のデータ
	 * @return string タグの中身
	 */
	function get_tag($tag_name,$es_data){
		preg_match_all("/<".$tag_name.">(.*?)<\/".$tag_name.">/si",$es_data,$match);
		$value=$match[1][0];
		return $value;
	}
	
	/**
	 * カスタムユーザ定義ファイルを分断
	 * 
	 * array(
	 *  array(
	 *   'user'  => アカウント名, 
	 *   'pass'  => パスワード, 
	 *   'group' => array(
	 *    グループ,
	 *    ...
	 *   ), 
	 *  ), 
	 *  ...
	 * )
	 * 
	 * @return array ユーザの一覧
	 */
	function parse_users(){
		$ret = array();
		$fn = $GLOBALS['root'] . '/var/users.dat';
		
		// ファイルの生存をチェックしてからファイルを読み込み
		if (file_exists($fn)) {
			$l = explode("\n", file_get_contents($fn));
			
			foreach ($l AS $rec) {
				if (trim($rec)) {
					list($user, $pass, $groups) = explode(':', $rec);
					
					$ret[$user] = array(
						'user'  => $user, 
						'pass'  => $pass, 
						'group' => explode(',', $groups), 
					);
				}
			}
			
		}
		
		return $ret;
	}
	
	
	/**
	 * アカウントとパスワードの組み合わせが正しいかチェック
	 * 
	 * 
	 * 
	 * @param string $accrount 調査対象のアカウント
	 * @param string $password 調査対象のパスワード
	 * @return boolean ログイン可能なら真、不可なら偽を返す
	 */
	function login_check($account, $password) {
		$ret = false;
		
		// まずローカルユーザをチェック
		$ret = (get_password($account) == $password);
		
		if ($ret) {
			// 大丈夫そうなのでグループを取得してセッションに乗せておく
			$_SESSION['session_groups'] = get_group($account);
		}
		else {
			
/* 色々不都合があるのでここは却下
			// ダメなら認証APIを見に行く
			$api_url = get_parameter('AuthApi', file_get_contents('es.conf'));
			
			$u = urlencode($account);
			$p = urlencode($password);
			$api_url .= "?user={$u}&pass={$p}";
			
			$res = json_decode(file_get_contents($api_url), true);
			if (is_array($res) && $res['_result'] == 'OK') {
				// 認証が通った
				$ret = true;
				
				// 権限情報のキャッシュ
				$_SESSION['session_groups'] = $res['groups'];
			}
*/
		}
		
		return $ret;
	}
	
	/**
	 * パスワード取得
	 * 
	 * .passwd ファイルからアカウントに対応するパスワードを取得する。
	 * 
	 * @param
	 * @return
	 */
	function get_password($account) {
		$root = $GLOBALS['root'];
		$ret = '';
		
		$passwd_file_path = "{$root}/var/.passwd";
		if (file_exists($passwd_file_path)) {
			$data = file_get_contents($passwd_file_path);
			$res = preg_match_all("/^{$account}:(.*?)\\n/ism", $data, $match);
			if ($res > 0) {
				// マスタユーザファイルにあったのでそこから取得
				list($ret) = explode(':', trim($match[1][0])); 
			}
		}
		
		// ユーザ定義ユーザファイルから検索
		$a = parse_users();
		if (isset($a[$account])) {
			$ret = $a[$account]['pass'];
		}
		
		
		return $ret;
	}
	
	/**
	 * パスワードのチェック
	 * 
	 * パスワードに問題がないかチェック
	 * 問題があった場合のコードは以下の通り
	 *  -1 : 文字数不足
	 *  -2 : 普通すぎるパスワード
	 *  -3 : 
	 * 
	 * @param string $pass チェック対象のパスワード
	 * @return integer 問題なければ 0 を返す
	 */
	function check_password($pass) {
		
		$ret = 0;
		
		// 禁止パスワード
		$deny_passwords = array(
			'password', 
			'00000000', 
			'12345678', 
			'01234567', 
		);
		
		do {
			// 文字数チェック
			if (strlen($pass) < 8) {
				$ret = -1;
				break;
			}
			
			// 禁止パスワードチェック
			if (in_array($pass, $deny_passwords)) {
				$ret = -2;
				break;
			}
			
		} while (0);
		
		return $ret;
	}
	
	/**
	 * グループを取得
	 * 
	 * アカウントから属するグループリストを取得する
	 * 
	 * @param $account 取得対象のアカウント
	 * @return 所属するグループの配列
	 */
	function get_group($account) {
		$root = $GLOBALS['root'];
		$ret = array();
		
		// グループマスタからの検索
		$data = explode("\n", file_get_contents("{$root}/var/.group"));
		foreach ($data AS $rec) {
			list($group, $users) = explode(':', trim($rec));
			if (in_array($account, preg_split('/\s*,\s*/', $users))) {
				list($group, $authrank) = explode('.', $group);
				if (! isset($authrank)) {
					$authrank = 1;
				}
				$ret[$group] = $authrank;
			}
		}
		
		// ユーザ定義ユーザファイルからの検索
		$a = parse_users();
		if (isset($a[$account])) {
			foreach ($a[$account]['group'] AS $g) {
				if (! in_array($g, $ret)) {
					list($group, $authrank) = explode('.', $g);
					if (! isset($authrank)) {
						$authrank = 1;
					}
					$ret[$group] = $authrank;
				}
			}
		}
		
		return $ret;
	}
	
	/**
	 * ユーザ追加
	 * 
	 * 
	 * 
	 * @param string $user 追加するユーザ
	 * @param string $pass 追加するユーザのパスワード
	 * @param array $group 追加するユーザの所属するグループ
	 * @return null なし
	 */
	function add_user($user, $pass, $group){
		$fn = $GLOBALS['root'] . '/var/users.dat';
		$ret = 0;
		
		do {
			$a = parse_users();
			if (isset($a[$user])) {
				$ret = -1;
				break;
			}
			
			// パラメータのチェック
			
			
			// ユーザが居ないので追加
			$l = sprintf("%s:%s:%s\n", $user, $pass, implode(',', $group));
			file_put_contents($fn, $l, FILE_APPEND);
			
		} while (0);
		
		return $ret;
	}
	
	/**
	 * ユーザ情報の更新
	 * 
	 * 既に存在するユーザ情報を更新する
	 * 
	 * @param string $user 対象のユーザ
	 * @param string $pass 変更後のパスワード、空文字の場合は変更しない
	 * @param array $group 変更後の所属グループ
	 * @return null なし
	 */
	function update_user($user, $pass, $group){
		$fn = $GLOBALS['root'] . '/var/users.dat';
		$ret = 0;
		
		do {
			// ユーザリストを読み込み、ユーザが存在すれば更新処理を行なう
			// TODO: ここでファイルのロックを行なうべき (ただ実質的になくても問題ないので今のところしてない)
			$a = parse_users();
			if (isset($a[$user])) {
				$fb = '';
				
				// ユーザリストを更新
				if ($pass) {
					$a[$user]['pass'] = $pass;
				}
				if (! is_null($group)) {
					$a[$user]['group'] = $group;
				}
				
				// ユーザファイルの中身を生成
				foreach ($a AS $u => $ud) {
					$fb .= sprintf("%s:%s:%s\n", $ud['user'], $ud['pass'], implode(',', $ud['group']));
				}
				
				// ユーザファイルを更新
				file_put_contents($fn, $fb);
			}
			
		} while (0);
		
		return $ret;
	}
	
	/**
	 * ユーザ情報をまとめて更新
	 * 
	 * 
	 * 
	 * @param string $users 対象のユーザ
	 * @return null なし
	 */
	function update_users($users){
		$fn = $GLOBALS['root'] . '/var/users.dat';
		$ret = 0;
		
		do {
			// ユーザファイルの中身を生成
			foreach ($users AS $u => $ud) {
				$fb .= sprintf("%s:%s:%s\n", $ud['user'], $ud['pass'], implode(',', $ud['group']));
			}
			
			// ユーザファイルを更新
			file_put_contents($fn, $fb);
			
		} while (0);
		
		return $ret;
	}
	
	/**
	 * グループリストを取得
	 * 
	 * 
	 * 
	 * @return グループの配列
	 */
	function get_group_list() {
		$root = $GLOBALS['root'];
		$ret = array();
		
		// グループマスタからの検索
		$data = explode("\n", file_get_contents("{$root}/var/.group"));
		foreach ($data AS $rec) {
			if (trim($rec)) {
				list($group, $users) = explode(':', trim($rec), 3);
				if (! preg_match('/^(root)$/', $group)) {
					$ret[] = $group;
				}
			}
		}
		
		return $ret;
	}
	
	/**
	 * パスワードのハッシュを取る
	 * 
	 * # md5 で済ませるつもりだがそのうち済まなそうなので
	 * 
	 * @parma string $pass ハッシュを取るのパスワード
	 * @return string パスワードのハッシュ
	 */
	function password_hash($pass) {
		/*
			多分世界一簡単な md5 のクラック方法 (google で検索) によると
			一般名詞の場合は３段くらい md5 かけると判りにくくなる。
		*/
		return md5($pass);
	}
	
	// template
	
	function smarty_new() {
		
		$s = new Smarty();
		
		// 設置ディレクトリの設定
		$root = $GLOBALS['root'];
		$s->template_dir = "{$root}/res/";
		$s->compile_dir  = "{$root}/tmp/";
		
		// デリミタの変更
		$s->left_delimiter  = '{{';
		$s->right_delimiter = '}}';
		
		return $s;
	}
	
	/**
	 * エラー出力
	 * 
	 * タイトルとエラー内容を渡しエラー時に表示する HTML を返す。
	 * 
	 * @param string $title エラーの見出し
	 * @param string $contents エラーの詳細内容
	 * @return
	 */
	function print_error($title, $contents) {
		$error= <<< EOF
<HTML>
<HEAD>
<TITLE>ES ServerSystem</TITLE>
<META http-equiv=Content-Type content="text/html; charset=UTF-8">
<META HTTP-EQUIV="Page-Enter" CONTENT="blendTrans(duration=0)">
<META HTTP-EQUIV="Page-Exit" CONTENT="blendTrans(duration=0)">
</HEAD>
<BODY>
<H1><font face="Times New Roman, Times, serif">{$title}</font></H1>{$contents}
<P>
<HR>

<ADDRESS>
<font face="Times New Roman, Times, serif">
ES Server System Powered by EitaroSoft
</font>
</ADDRESS></BODY></HTML>
EOF;
		return $error;
	}
	
?>
