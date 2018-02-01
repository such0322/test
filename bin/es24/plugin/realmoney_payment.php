<?PHP
/*

リアルマネーをゲーム内課金通貨に変換したことのログ

*/
	
	////////////////////////////////////
	// 商品IDと加算ポイントのハッシュを生成
	$root = realpath(getcwd() . '/../../var/es24');
	include_once('../../var/es24/lib/es2.php');
	include_once('../../var/es24/lib/spyc.php');
	$user_vars = user_vars_load();
	
	$goods_to_point = array();
	foreach ($user_vars['goods_id'] as $k => $v) {
		if (preg_match('/([0-9]+)/', mb_convert_kana(str_replace(',', '', $v), 'a', 'UTF-8'), $m)) {
			$goods_to_point[$k] = $m[0];
		}
	}
	
	////////////////////////////////////
	// realmoneyログから取り込み
	
	// 途中ログを削除
	$res = db_exec($con, "DELETE FROM l_realmoney_payment WHERE is_fixed = 0 AND last_update < '{$delete_border_date}'");
	
	// 対象ログファイルの一覧を確保
	$log_files = glob("{$log_dir}/realmoney_{$exec_date}.log");
	
	// テンプレートとかの作成
	$tpl = "INSERT INTO l_realmoney_payment(log_date, pftype, uid, user_id, chara_id, trade_val, trade_type, goods_id, add_point, is_fixed) VALUES";
	$tpl_val = "('%s', %d, '%s', '%s', '%s', %d, %d, '%s', %d, %d)";
	$values = array();
	$values_max = 50;
	
	// ログファイルの読み込み
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
			while (! feof($fp)) {
				$log = fgets($fp);
				
				// 空行は次へ
				if (! rtrim($log, "\r\n")) {
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
					$goods_id, 
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				if (! preg_match('/^[0-9]*$/', $user_id) || ! preg_match('/^[0-9]*$/', $chara_id)) {
					continue;
				}
				
				// 決済の記録のみなので trade_type = 1 限定
				if ($trade_type != 1) {
					continue;
				}
				
				// 特に細工せずそのまま格納
				$values[] = sprintf($tpl_val, db_qs($con, $log_date)
				                            , $pftype
				                            , db_qs($con, $uid)
				                            , db_qs($con, $user_id)
				                            , db_qs($con, $chara_id)
				                            , $trade_val
				                            , $trade_type
				                            , db_qs($con, $goods_id)
				                            , intval($goods_to_point[$goods_id])
				                            , $is_fixed
				);
				
				// 一定数たまったら投入
				if (sizeof($values) > $values_max) {
					$sql = $tpl . implode(',', $values);
					$res = db_exec($con, $sql);
					$values = array();
				}
			}
			
			fclose($fp);
		}
	}
	
	// まだ残ってれば投入
	if (sizeof($values) > 0) {
		$sql = $tpl . implode(',', $values);
		$res = db_exec($con, $sql);
		$values = array();
	}
	
	
	
	////////////////////////////////////
	// realmoney_paymentログから取り込み
	
	// 対象ログファイルの一覧を確保
	$log_files = glob("{$log_dir}/realmoney_payment_{$exec_date}.log");
	
	// テンプレートとかの作成
	$tpl = "INSERT INTO l_realmoney_payment(log_date, pftype, uid, user_id, chara_id, trade_val, trade_type, goods_id, add_point, trans_id, log_id, is_fixed) VALUES";
	$tpl_val = "('%s', %d, '%s', '%s', '%s', %d, %d, '%s', %d, '', '', %d)";
	$values = array();
	$values_max = 50;
	
	// ログファイルの読み込み
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
			while (! feof($fp)) {
				$log = fgets($fp);
				
				// 空行は次へ
				if (! rtrim($log, "\r\n")) {
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
					$goods_id, 
					$note, 
					$add_point
				) = explode("\t", rtrim($log, "\r\n"));
				
				// 日付の無いものは除外
				if (! $log_date) {
					continue;
				}
				if (! preg_match('/^[0-9]*$/', $user_id) || ! preg_match('/^[0-9]*$/', $chara_id)) {
					continue;
				}
				
				// 特に細工せずそのまま格納
				$values[] = sprintf($tpl_val, db_qs($con, $log_date)
				                            , $pftype
				                            , db_qs($con, $uid)
				                            , db_qs($con, $user_id)
				                            , db_qs($con, $chara_id)
				                            , $trade_val
				                            , $trade_type
				                            , db_qs($con, $goods_id)
				                            , $add_point
				                            , $is_fixed
				);
				
				// 一定数たまったら投入
				if (sizeof($values) > $values_max) {
					$sql = $tpl . implode(',', $values);
					$res = db_exec($con, $sql);
					$values = array();
				}
			}
			
			fclose($fp);
		}
	}
	
	// まだ残ってれば投入
	if (sizeof($values) > 0) {
		$sql = $tpl . implode(',', $values);
		$res = db_exec($con, $sql);
		$values = array();
	}
	