<?PHP
//print_r($_SESSION);
//var_dump($_SESSION['__mylog']);
	
	require_once( 'env.php' );
	require_once( 'lib/common.php' );
	
	////////////////////////////////////////////////////////////////////////////
	// パーツのインクルード
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	$con = admin_con();
	$user_vars = user_vars_load();
	
	$pftypes = $user_vars['pftype'];
	$units = array(
		'hour'  => '時間', 
		'day'   => '日', 
		'week'  => '週', 
		'month' => '月', 
	);
	
	$group_labels = array(
		'kikan'  => '期間 (以上～未満)', 
		'users'  => 'ユーザ数', 
		'charge' => '課金売上', 
		'fm_add' => '無料付与取得', 
		'kakin'  => '課金アイテム売上', 
		'gacha'  => 'ガチャ売上', 
		'rm_sub' => '課金通貨消費', 
		'fm_sub' => '無料付与通貨消費', 
	);
	
	$col2label = array(
		'start'             => '始端日時', 
		'end'               => '終端日時', 
		
		'regist_count'      => '登録数', 
		'leave_count'       => '退会数', 
		'login_count'       => 'ログイン数', 
		
		'charge_user'       => '課金ユーザ数', 
		'charge_user_first' => '課金ユーザ数 (初回)', 
		'charge_total'      => '課金売上総額', 
		'rm_add_total'      => '課金通貨加算量', 
		'charge_arpu'       => 'ARPU', 
		'charge_arppu'      => 'ARPPU', 
		'charge_user_rate'  => '課金率', 
		
		'fm_add_user'       => '無料付与通貨取得者数', 
		'fm_add_user_first' => '無料付与通貨取得者数 (初回)', 
		'fm_add_total'      => '無料付与通貨総額', 
		
		'kakin_user'        => '課金アイテム購入者数', 
		'kakin_user_first'  => '課金アイテム購入者数 (初回)', 
		'kakin_total'       => '課金アイテム売上総額', 
		
		'gacha_user'        => 'ガチャ利用者数', 
		'gacha_user_first'  => 'ガチャ利用者数 (初回)', 
		'gacha_total'       => 'ガチャ売上総額', 
		
		'rm_sub_user'       => '課金通貨消費ユーザ数', 
		'rm_sub_user_first' => '課金通貨消費ユーザ数 (初回)', 
		'rm_sub_total'      => '課金通貨消費総額', 
		
		'fm_sub_user'       => '無料付与通貨消費ユーザ数', 
		'fm_sub_user_first' => '無料付与通貨消費ユーザ数 (初回)', 
		'fm_sub_total'      => '無料付与通貨消費総額', 
	);
	
	$prots = array(
		'charge' => array(
			'charge_user', 
			'charge_user_first', 
			'charge_total', 
		), 
		'fm_add' => array(
			'fm_add_user', 
			'fm_add_user_first', 
			'fm_add_total', 
		), 
		'kakin'  => array(
			'kakin_user', 
			'kakin_user_first', 
			'kakin_total', 
		), 
		'gacha'  => array(
			'gacha_user', 
			'gacha_user_first', 
			'gacha_total', 
		), 
		'rm_sub' => array(
			'rm_sub_user', 
			'rm_sub_user_first', 
			'rm_sub_total', 
		), 
		'fm_sub' => array(
			'fm_sub_user', 
			'fm_sub_user_first', 
			'fm_sub_total', 
		), 
	);
	
	$pftype     = (isset($pftypes[$LOCAL_SESSION['pftype']]) ? $LOCAL_SESSION['pftype'] : 0);
	$unit       = (isset($units[$LOCAL_SESSION['unit']]) ? $LOCAL_SESSION['unit'] : 'day');
	$begin_date = (isset($LOCAL_SESSION['begin_date']) ? $LOCAL_SESSION['begin_date'] : date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d") - 7, date("Y"))));
	$end_date   = (isset($LOCAL_SESSION['end_date'])   ? $LOCAL_SESSION['end_date']   : date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d") - 0, date("Y"))));
	
	$is_cache_use = 1;
	if ($LOCAL_SESSION['is_reload']) {
		$is_cache_use = 0;
		unset($LOCAL_SESSION['is_reload']);
	}
	
	////////////////////////////////////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// 検索条件の変更
	if (isset($_POST['f1_submit'])) {
		
		if (isset($_POST['begin_date'])) {
			list($y, $m, $d) = explode('-', $_POST['begin_date']);
			if (checkdate($m, $d, $y)) {
				$begin_date = $LOCAL_SESSION['begin_date'] = $_POST['begin_date'];
			}
		}
		if (isset($_POST['end_date'])) {
			list($y, $m, $d) = explode('-', $_POST['end_date']);
			if (checkdate($m, $d, $y)) {
				$end_date = $LOCAL_SESSION['end_date'] = $_POST['end_date'];
			}
		}
		
		if (isset($_POST['pftype'])) {
			$pftype = $LOCAL_SESSION['pftype'] = $_POST['pftype'];
		}
		if (isset($_POST['unit'])) {
			$unit = $LOCAL_SESSION['unit'] = $_POST['unit'];
			
			// 週単位の場合は始端終端を週頭週末に書き直す
			if ($LOCAL_SESSION['unit'] == 'week') {
				$w = intval(date('w', strtotime($LOCAL_SESSION['begin_date'])));
				if ($w > 0) {
					$begin_date = $LOCAL_SESSION['begin_date'] = date('Y-m-d', strtotime("{$LOCAL_SESSION['begin_date']} -{$w} day"));
				}
				
				$w = intval(date('w', strtotime($LOCAL_SESSION['end_date'])));
				if ($w > 0) {
					$w = 7 - $w;
					$end_date = $LOCAL_SESSION['end_date'] = date('Y-m-d', strtotime("{$LOCAL_SESSION['end_date']} +{$w} day"));
				}
			}
			
			// 月単位の場合は始端終端を月初月末に合わせる
			if ($LOCAL_SESSION['unit'] == 'month') {
				$d = intval(date('j', strtotime($LOCAL_SESSION['begin_date'])));
				if ($d > 1) {
					$begin_date = $LOCAL_SESSION['begin_date'] = date('Y-m-01', strtotime($LOCAL_SESSION['begin_date']));
				}
				
				$d = intval(date('j', strtotime($LOCAL_SESSION['end_date'])));
				if ($d > 1) {
					$end_date = $LOCAL_SESSION['end_date'] = date('Y-m-01', strtotime("{$LOCAL_SESSION['end_date']} +1 month"));
				}
			}
			
		}
		
		if ($_POST['is_reload']) {
			$LOCAL_SESSION['is_reload'] = $_POST['is_reload'];
			$is_cache_use = 0;
		}
		
		$LOCAL_SESSION['f1_search'] = 1;
	}
	
	// CSV出力の場合
	if (isset($_POST['export']) && $_POST['export'] == 1) {
		
		// 出力
		header("Content-Type: application/octet-stream");
//		header("Content-Type: application/x-csv");
		header("Content-Disposition: attachment; filename=report.csv");
		header("Cache-Control: public");
		header("Pragma: public");
		
		$logs = report_generate($pftype, $begin_date, $end_date, $unit, $is_cache_use);
		if ($logs) {
			
			// ヘッダ行の出力
			echo implode(',', $col2label) . "\r\n";
			
			// 本文の出力
			foreach ($logs as $log) {
				$rec = array();
				foreach ($col2label as $k => $v) {
					$rec[] = $log[$k];
				}
				echo implode(",", $rec) . "\r\n";
			}
		}
		
		exit;
	}
	
	if ($_POST) {
		return ;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、検索・抽出条件フォーム
	
	$psmarty->assign('pftypes', $pftypes);
	$psmarty->assign('units',   $units);
	
	$psmarty->assign('group_labels', $group_labels);
	$psmarty->assign('col2label', $col2label);
	$psmarty->assign('prots', $prots);
	
	$psmarty->assign('begin_date', $begin_date);
	$psmarty->assign('end_date',   $end_date);
	$psmarty->assign('pftype', $pftype);
	$psmarty->assign('unit',   $unit);
	
	// 検索条件が指定されていない場合はここで終わらせる
	if (! isset($LOCAL_SESSION['f1_search'])) {
		return ;
	}
	$psmarty->assign('post_data', $post_data);
	$psmarty->assign('is_f1_search', 1);
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成
	
	// ログの生成
	$logs = report_generate($pftype, $begin_date, $end_date, $unit, $is_cache_use);
	
	$psmarty->assign('logs', $logs);