<?PHP
	$tables = array(
		'l_penalty' => array(
			'l_penalty_id' => array(
				'type' => 'primary',
				'size' => 8, 
				'name' => '履歴ID',
			), 
			'log_date' => array(
				'type' => 'datetime',
				'size' => 8, 
				'name' => '日時',
			), 
			'operator' => array(
				'type' => 'strkey',
				'name' => '操作者', 
				'size' => 10, 
			), 
			'player_id' => array(
				'type' => 'strkey',
				'name' => 'プレイヤーID', 
				'size' => 10, 
			), 
			'penalty_days' => array(
				'type' => 'num',
				'name' => 'ペナルティ実施日数', 
				'size' => 10, 
			), 
			'note' => array(
				'type' => 'text',
				'size' => 8, 
				'name' => '備考欄', 
			), 
		), 
		/*
		'l_tablename' => array(
			'id' => array(
				'type' => 'primary',
				'size' => 8, 
				'name' => '履歴番号',
			), 
			'log_date' => array(
				'type' => 'datetime',
				'size' => 8, 
				'name' => '日時',
			), 
			'uid' => array(
				'type' => 'key',
				'size' => 8, 
				'name' => 'プラットフォーム側ユーザID', 
			), 
			'item_id' => array(
				'type' => 'enum',
				'size' => 12, 
				'name' => 'アイテムID', 
			), 
		), 
		*/
	);