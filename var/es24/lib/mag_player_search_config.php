<?PHP
	
/*
	$table_name = 'player_game';
	
	$default_order  = 'player_id';
	$default_display_cols = array('player_id', 'level', 'leader_card_id', 'name', 'comment');
	
	$select_columns = array(
		'player_id'         => array('type' => 'primary',  'size' =>  9, 'name' => 'プレイヤーID',), 
		'step'              => array('type' => 'key',      'size' =>  8, 'name' => '進捗度合',), 
		'level'             => array('type' => 'num',      'size' =>  4, 'name' => 'レベル',), 
		'exp'               => array('type' => 'num',      'size' =>  9, 'name' => '経験値',), 
		'mag'               => array('type' => 'num',      'size' =>  8, 'name' => '所持ゲーム内通貨',), 
		'st'                => array('type' => 'num',      'size' =>  4, 'name' => 'ST',), 
		'st_max'            => array('type' => 'num',      'size' =>  4, 'name' => 'ST最大値',), 
		'st_updated'        => array('type' => 'datetime', 'size' => 20, 'name' => 'ST回復日時',), 
		'bp'                => array('type' => 'num',      'size' =>  4, 'name' => 'BP',), 
		'bp_max'            => array('type' => 'num',      'size' =>  4, 'name' => 'BP最大',), 
		'bp_updated'        => array('type' => 'datetime', 'size' => 20, 'name' => 'BP回復日時',), 
		'login_date'        => array('type' => 'datetime', 'size' => 20, 'name' => '最終ログイン',), 
		'begin_date'        => array('type' => 'datetime', 'size' => 20, 'name' => '開始日時',), 
		
		'continue_days'     => array('type' => 'num',      'size' =>  4, 'name' => '継続日数',), 
		'total_login_days'  => array('type' => 'num',      'size' =>  4, 'name' => '累計ログイン日数',), 
		'recv_dists'        => array('type' => 'num',      'size' =>  8, 'name' => '全体配布受け取り状況',), 
		
		'name'              => array('type' => 'string',   'size' => 16, 'name' => '名前',), 
		'scenario_name'     => array('type' => 'string',   'size' => 16, 'name' => 'シナリオ表示用の名前',), 
		'comment'           => array('type' => 'string',   'size' => 30, 'name' => 'ひとことコメント',), 
		'invite_player_id'  => array('type' => 'num',      'size' =>  9, 'name' => '招待プレイヤーID',), 
		
		'home_character_id' => array('type' => 'num',      'size' =>  9, 'name' => 'ホーム画面キャラID',), 
		'birthday'          => array('type' => 'date',     'size' => 10, 'name' => '誕生日',), 
		
		'created'           => array('type' => 'datetime', 'size' => 20, 'name' => '登録日時',), 
		'updated'           => array('type' => 'datetime', 'size' => 20, 'name' => '更新日時',), 
	);
	
	// マスタデータ、キーの名称は $select_columns の type=enum の場合はそれに合わせる、それ以外はすきに
	$master = array();
	
	
	// 集計対象
	$stat_columns = array(
		'last_login' => array(
			'type' => 'datetime',
			'size' => 8, 
			'name' => '最終ログイン日時',
			'stat' => 'date(last_login)', 
		), 
		'step' => array(
			'type' => 'num', 
			'name' => '進捗度合', 
		), 
		'level' => array(
			'type' => 'num', 
			'name' => 'レベル', 
		), 
		'home_character_id' => array(
			'type' => 'enum',
			'name' => 'ホーム画面キャラ',
		), 
	);

*/




	$table_name = ' player_game LEFT JOIN player_otetsudai USING (player_id) LEFT JOIN player_achievement USING (player_id) ';
	
	$default_order  = 'player_id';
	$default_display_cols = array('player_id', 'player_game.level', 'player_game.leader_card_id', 'player_game.name', 'player_game.comment', 'player_otetsudai.level');
	
	$select_columns = array(
		'player_id' => array('type' => 'primary',  'size' =>  9, 'name' => 'プレイヤーID',), 
		
		'player_game.step'              => array('type' => 'key',      'size' =>  8, 'name' => '進捗度合',), 
		'player_game.level'             => array('type' => 'num',      'size' =>  4, 'name' => 'プレイヤーレベル',), 
		'player_game.player_exp'        => array('type' => 'num',      'size' =>  9, 'name' => '経験値',), 
		'player_game.mag'               => array('type' => 'num',      'size' =>  8, 'name' => '所持ゲーム内通貨',), 
		'player_game.st'                => array('type' => 'num',      'size' =>  4, 'name' => 'ST',), 
		'player_game.st_max'            => array('type' => 'num',      'size' =>  4, 'name' => 'ST最大値',), 
		'player_game.st_updated'        => array('type' => 'datetime', 'size' => 20, 'name' => 'ST回復日時',), 
		'player_game.bp'                => array('type' => 'num',      'size' =>  4, 'name' => 'BP',), 
		'player_game.bp_max'            => array('type' => 'num',      'size' =>  4, 'name' => 'BP最大',), 
		'player_game.bp_updated'        => array('type' => 'datetime', 'size' => 20, 'name' => 'BP回復日時',), 
		'player_game.login_date'        => array('type' => 'datetime', 'size' => 20, 'name' => '最終ログイン',), 
		'player_game.begin_date'        => array('type' => 'datetime', 'size' => 20, 'name' => '開始日時',), 
		'player_game.continue_days'     => array('type' => 'num',      'size' =>  4, 'name' => '継続日数',), 
		'player_game.total_login_days'  => array('type' => 'num',      'size' =>  4, 'name' => '累計ログイン日数',), 
		'player_game.recv_dists'        => array('type' => 'num',      'size' =>  8, 'name' => '全体配布受け取り状況',), 
		'player_game.name'              => array('type' => 'string',   'size' => 16, 'name' => '名前',), 
		'player_game.scenario_name'     => array('type' => 'string',   'size' => 16, 'name' => 'シナリオ表示用の名前',), 
		'player_game.comment'           => array('type' => 'string',   'size' => 30, 'name' => 'ひとことコメント',), 
		'player_game.invite_player_id'  => array('type' => 'num',      'size' =>  9, 'name' => '招待プレイヤーID',), 
		'player_game.home_character_id' => array('type' => 'num',      'size' =>  9, 'name' => 'ホーム画面キャラID',), 
		'player_game.birthday'          => array('type' => 'date',     'size' => 10, 'name' => '誕生日',), 
		'player_game.created'           => array('type' => 'datetime', 'size' => 20, 'name' => '登録日時',), 
		'player_game.updated'           => array('type' => 'datetime', 'size' => 20, 'name' => '更新日時',), 

		'player_otetsudai.level'       => array('type' => 'num',      'size' =>  4, 'name' => 'おてつだいレベル',), 
		'player_otetsudai.exp'         => array('type' => 'num',      'size' =>  4, 'name' => 'おてつだい経験値',), 
		'player_otetsudai.registed'    => array('type' => 'datetime', 'size' => 20, 'name' => 'おてつだい開始日時',), 
		'player_otetsudai.slot_1_1'    => array('type' => 'key',      'size' =>  4, 'name' => 'おてつだい中カードスロット 1_1',), 
		'player_otetsudai.slot_1_2'    => array('type' => 'key',      'size' =>  4, 'name' => 'おてつだい中カードスロット 1_2',), 
		'player_otetsudai.slot_2_1'    => array('type' => 'key',      'size' =>  4, 'name' => 'おてつだい中カードスロット 2_1',), 
		'player_otetsudai.slot_2_2'    => array('type' => 'key',      'size' =>  4, 'name' => 'おてつだい中カードスロット 2_2',), 
		'player_otetsudai.slot_3_1'    => array('type' => 'key',      'size' =>  4, 'name' => 'おてつだい中カードスロット 3_1',), 
		'player_otetsudai.slot_3_2'    => array('type' => 'key',      'size' =>  4, 'name' => 'おてつだい中カードスロット 3_2',), 
		'player_otetsudai.is_working'  => array('type' => 'key',      'size' =>  1, 'name' => 'おてつだい中か',), 

		'player_achievement.mypage_change_chara'      => array('type' => 'num', 'size' =>  4, 'name' => 'マイペキャラ変更回数',), 
		'player_achievement.mypage_change_background' => array('type' => 'num', 'size' =>  4, 'name' => 'キャラ背景変更回数',), 
		'player_achievement.mypage_change_costume'    => array('type' => 'num', 'size' =>  4, 'name' => 'キャラ衣装変更回数',), 
		'player_achievement.friend_request'           => array('type' => 'num', 'size' =>  4, 'name' => 'フレンド申請回数',), 
		'player_achievement.otetsudai_count'          => array('type' => 'num', 'size' =>  4, 'name' => 'おてつだい実行回数',), 
		'player_achievement.card_tribute_count'       => array('type' => 'num', 'size' =>  4, 'name' => 'カード肥育総数',), 
		'player_achievement.card_tribute_level'       => array('type' => 'num', 'size' =>  4, 'name' => 'カード肥育上昇レベル',), 
		'player_achievement.card_ev'                  => array('type' => 'num', 'size' =>  4, 'name' => 'カード覚醒総数',), 
		'player_achievement.card_bild'                => array('type' => 'num', 'size' =>  4, 'name' => 'カード限突総数',), 
		'player_achievement.card_open'                => array('type' => 'num', 'size' =>  4, 'name' => 'カード枝開放総数',), 
		'player_achievement.card_fullopen'            => array('type' => 'num', 'size' =>  4, 'name' => 'カード全枝開放数',), 
		'player_achievement.quest_clear_main'         => array('type' => 'num', 'size' =>  4, 'name' => '主クエストクリア数',), 
		'player_achievement.quest_clear_story'        => array('type' => 'num', 'size' =>  4, 'name' => '話クエストクリア数',), 
		'player_achievement.quest_clear_free'         => array('type' => 'num', 'size' =>  4, 'name' => '自由クエストクリア数',), 
		'player_achievement.quest_clear_branch'       => array('type' => 'num', 'size' =>  4, 'name' => '主クエストクリア総数',), 
		'player_achievement.quest_total_main'         => array('type' => 'num', 'size' =>  4, 'name' => '話クエストクリア総数',), 
		'player_achievement.quest_total_story'        => array('type' => 'num', 'size' =>  4, 'name' => '自由クエストクリア総数',), 
		'player_achievement.quest_total_free'         => array('type' => 'num', 'size' =>  4, 'name' => '枝クエストクリア総数',), 
		'player_achievement.quest_comp_free'          => array('type' => 'num', 'size' =>  4, 'name' => '自由クエスト何かコンプした数',), 
		'player_achievement.score_bomb_total'         => array('type' => 'num', 'size' =>  4, 'name' => 'ボム累計数',), 
		'player_achievement.score_bomb_high'          => array('type' => 'num', 'size' =>  4, 'name' => 'ボム最大数',), 
		'player_achievement.score_combo_total'        => array('type' => 'num', 'size' =>  4, 'name' => 'コンボ累計数',), 
		'player_achievement.score_combo_high'         => array('type' => 'num', 'size' =>  4, 'name' => 'コンボ最大数',), 
		'player_achievement.collection_story'         => array('type' => 'num', 'size' =>  4, 'name' => '共通コレクション話',), 
		'player_achievement.collection_voice'         => array('type' => 'num', 'size' =>  4, 'name' => '共通コレクション声',), 
		'player_achievement.collection_still'         => array('type' => 'num', 'size' =>  4, 'name' => '共通コレクション絵',), 
		'player_achievement.collection_background'    => array('type' => 'num', 'size' =>  4, 'name' => '共通コレクション背景',), 
		'player_achievement.collection_costume'       => array('type' => 'num', 'size' =>  4, 'name' => '共通コレクション衣装',), 
		'player_achievement.cc_story'                 => array('type' => 'num', 'size' =>  4, 'name' => 'キャラコレクション話',), 
		'player_achievement.cc_voice'                 => array('type' => 'num', 'size' =>  4, 'name' => 'キャラコレクション声',), 
		'player_achievement.cc_still'                 => array('type' => 'num', 'size' =>  4, 'name' => 'キャラコレクション絵',), 
		'player_achievement.cc_background'            => array('type' => 'num', 'size' =>  4, 'name' => 'キャラコレクション背景',), 
		'player_achievement.cc_costume'               => array('type' => 'num', 'size' =>  4, 'name' => 'キャラコレクション衣装',), 
		
	);
	
	// マスタデータ、キーの名称は $select_columns の type=enum の場合はそれに合わせる、それ以外はすきに
	$master = array();
	
	
	// 集計対象
	$stat_columns = array(
		'player_game.last_login' => array(
			'type' => 'datetime',
			'size' => 8, 
			'name' => '最終ログイン日時',
			'stat' => 'date(last_login)', 
		), 
		'player_game.step' => array(
			'type' => 'num', 
			'name' => '進捗度合', 
		), 
		'player_game.level' => array(
			'type' => 'num', 
			'name' => 'プレイヤーレベル', 
		), 
		'player_game.home_character_id' => array(
			'type' => 'enum',
			'name' => 'ホーム画面キャラ',
		), 
		'player_otetsudai.level' => array(
			'type' => 'num', 
			'name' => 'おてつだいレベル', 
		), 
	);

