<?PHP
/**
 * plugin/ のファイルを実行する処理
 */
	// カレントディレクトリをスクリプトの場所に持っていく
	chdir(dirname(__FILE__));
	umask(0002);
	ini_set('memory_limit','256M');
	
	include_once('../../var/es24/env/env.php');
	include_once('../../var/es24/lib/func.php');
	include_once('../../var/es24/lib/chara_parse.php');
	
	// 最初に NOTICE だけは抑制しておく
	ini_set('display_errors', 'On');
	error_reporting(E_ALL ^ E_NOTICE);
	
	// 文字コードの設定
	ini_set("default_charset","UTF-8");
	ini_set("mbstring.http_input","UTF-8");
	ini_set("mbstring.http_output","UTF-8");
	ini_set("mbstring.internal_encoding","UTF-8");
	
	// よくつかうDB接続を確保
	$con = admin_con();
	$ndb_con = $slave_con = slave_con();  // 変数名は歴史的事情により複数用意
	
	// 引数の受取
	$opts = getopt("d:t:s:f:");
	$log_dir  = $opts['d'];         // ログのあるディレクトリ、パスに * を付けて複数ディレクトリとなるのを許可する
	$is_fixed = intval($opts['t']); // 何日前のログを取り込むか、
	$file = $opts['f'];             // 実行対象のファイル
	
	// 取り込み日の生成
	$exec_ts = mktime(0, 0, 0, date('m'), date('d') - $is_fixed, date('Y'));
	$exec_date = date('Ymd', $exec_ts);
	$exec_datetime = date('Y-m-d H:i:s', $exec_ts);
	$delete_border_date = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m'), date('d') - $is_fixed + 1, date('Y')));
	
	// 取り込み日の終端の生成
	$exec_term_ts = mktime(0, 0, 0, date('m'), date('d') - $is_fixed + 1, date('Y'));
	$exec_term_date = date('Ymd', $exec_term_ts);
	$exec_term_datetime = date('Y-m-d H:i:s', $exec_term_ts);
	
	/////////////////////////////////////////////////////////////////////////
	// 以下処理
	
	// 処理対象プラグインを実行
	if (is_readable($file)) {
		
		// ngp_basic_log_import.php でもしてるが直接呼ばれた時用にここでも処理前に文法チェック
		ob_start();
		$res = exec("php -l {$file} > /dev/null 2>&1;echo $?");
		ob_end_clean();
		if ($res == "0") {
			include($file);
		}
	}
	