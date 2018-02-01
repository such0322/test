<?php
	
	require_once( 'env.php' );
	require_once( 'lib/common.php' );
	
	// イベント結果の取得
	$cols = array(
		'event_id'    => 'イベントID', 
//		'raidboss_id' => 'レイドボスID', 
		'rank'        => '順位', 
		'player_id'   => 'プレイヤーID', 
		'point'       => 'ポイント', 
		'last_point'  => 'ポイント最終取得日時', 
	);
	
	$event_master = \Mag\Res::load('event');
	$events = array_combine(array_keys($event_master), array_keys($event_master));
	
	if ($_POST['f1_submit']) {
		$env = env();
		$con = db_con($env['slave_db']['db_host'], $env['slave_db']['db_user'], $env['slave_db']['db_pass'], $env['slave_db']['db_name']);
		$event_id = intval($_POST['event_id']);
		$sql = sprintf("SELECT * FROM event_result WHERE event_id = %d ORDER BY event_id, rank", $event_id);
		$dbh = db_exec($con, $sql);
		
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=event_result.csv");
		header("Cache-Control: public");
		header("Pragma: public");
		
		echo mb_convert_encoding(implode(',', $cols), 'SJIS', 'UTF-8') . "\r\n";
		while ($rec = db_fetch($dbh)) {
			
			$a = array();
			foreach ($cols as $col => $label) {
				$a[] = $rec[$col];
			}
			echo implode(',', $a) . "\r\n";
		}
		exit;
	}
	if ($_POST['f2_submit']) {
		$event_id = intval($_POST['event_id']);
		try {
			\Mag\Page\Work\Event::factory()->rebuildRanking($event_id);
			\Mag\DB::commitStack();
			
		} catch (Exception $e) {
mylog(sprintf('%s:%d > %s', basename(__FILE__), __LINE__, $e->getMessage()));
		}
	}
	
	$psmarty->assign('events', $events);
