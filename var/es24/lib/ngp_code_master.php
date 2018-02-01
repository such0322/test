<?PHP
	
	/** 番号と名前の対照表 */
	function ngp_code_master() {
		$user_vars = user_vars_load();
		$strres = string_resource();
		
		$ret = array(
			// メンテフラグのリスト
			'mainte_flag_list' => array(
				0 => $strres['CodeMaster']['mainte_flag_list.0'], 
				1 => $strres['CodeMaster']['mainte_flag_list.1'], 
			), 
			
			'l_chat_log' => array(
				'pftype' => $user_vars['pftype'], 
				'server_id' => $user_vars['server_id'], 
				'chat_type' => $user_vars['chat_type'], 
			), 
			'l_realmoney' => array(
				'pftype' => $user_vars['pftype'], 
				'trade_type' => array(
					'1' => $strres['CodeMaster']['l_realmoney.trade_tpe.1'], 
					'2' => $strres['CodeMaster']['l_realmoney.trade_tpe.2'], 
					'3' => $strres['CodeMaster']['l_realmoney.trade_tpe.3'], 
				), 
				'goods_id' => $user_vars['goods_id'], 
			), 
			'l_freemoney' => array(
				'pftype' => $user_vars['pftype'], 
				'trade_type' => array(
					'1' => $strres['CodeMaster']['l_realmoney.trade_tpe.1'], 
					'2' => $strres['CodeMaster']['l_realmoney.trade_tpe.2'], 
					'3' => $strres['CodeMaster']['l_realmoney.trade_tpe.3'], 
				), 
				'add_type' => $user_vars['add_type'], 
			), 
			'l_kakin' => array(
				'pftype' => $user_vars['pftype'], 
				'item_id' => $user_vars['item_id'], 
			), 
			'l_shop' => array(
				'pftype' => $user_vars['pftype'], 
				'item_id' => $user_vars['item_id'], 
			), 
			'l_gacha' => array(
				'pftype' => $user_vars['pftype'], 
				'gacha_type' => $user_vars['gacha_type'], 
				'gacha_id' => $user_vars['gacha_id'], 
				'drop_id' => $user_vars['drop_id'], 
				'is_free' => array(
					'0'  => $strres['CodeMaster']['l_gacha.is_free.0'], 
					'1'  => $strres['CodeMaster']['l_gacha.is_free.1'], 
				), 
				'is_comp' => array(
					'0'  => $strres['CodeMaster']['l_gacha.is_comp.0'], 
					'1'  => $strres['CodeMaster']['l_gacha.is_comp.1'], 
				), 
			), 
			'l_login_daily' => array(
				'pftype' => $user_vars['pftype'], 
				'server_id' => $user_vars['server_id'], 
			), 
			'l_login_monthly' => array(
				'pftype' => $user_vars['pftype'], 
				'server_id' => $user_vars['server_id'], 
			), 
			'l_item_move' => array(
				'pftype' => $user_vars['pftype'], 
				'item_id' => $user_vars['item_id'], 
				'action_type' => $user_vars['action_type'], 
				'type' => array(
					'0'  => $strres['CodeMaster']['l_item_move.type.0'], 
					'1'  => $strres['CodeMaster']['l_item_move.type.1'], 
					'2'  => $strres['CodeMaster']['l_item_move.type.2'], 
					'3'  => $strres['CodeMaster']['l_item_move.type.3'], 
				), 
			), 
		);
		
		return $ret;
	}
	
	/** 検索とかしたい項目のまとめ */
	function ngp_tables() {
		
		$strres = string_resource();
		$user_vars = user_vars_load();
		
		$ret =  array(
			'l_add_item' => array(
				'l_add_item_id' => array(
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
				'chara_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'キャラクタID', 
				), 
				'item_id' => array(
					'type' => 'enum',
					'size' => 12, 
					'name' => 'アイテムID', 
				), 
				'item_num' => array(
					'type' => 'num',
					'size' => 8, 
					'name' => 'アイテム数', 
				), 
				'result_code' => array(
					'type' => 'string',
					'size' => 20, 
					'name' => '結果コード', 
				), 
				'item_serial' => array(
					'type' => 'string',
					'size' => 20, 
					'name' => 'アイテムシリアル', 
				), 
				'additemext' => array(
					'type' => 'string',
					'size' => 20, 
					'name' => '追加パラメータ', 
				), 
				'note' => array(
					'type' => 'text',
					'name' => '備考', 
				), 
			), 
			'l_chat_log' => array(
				'l_chat_log_id' => array(
					'type' => 'primary',
					'size' => 8, 
					'name' => '履歴ID',
				), 
				'log_date' => array(
					'type' => 'datetime',
					'size' => 8, 
					'name' => '日時',
				), 
				'pftype' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => 'プラットフォーム区分',
				), 
				'log_date' => array(
					'type' => 'datetime',
					'size' => 8, 
					'name' => '日時',
				), 
				'uid' => array(
					'type' => 'strkey',
					'size' => 30, 
					'name' => 'プラットフォーム側ユーザID', 
				), 
				'user_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'ユーザID', 
				), 
				'chara_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'キャラクタID', 
				), 
				'message' => array(
					'type' => 'string',
					'name' => '発言内容', 
					'size' => 40, 
				), 
				'server_id' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => 'サーバ', 
				), 
				'area_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'エリア', 
				), 
				'x' => array(
					'type' => 'num',
					'size' => 8, 
					'name' => 'x', 
				), 
				'y' => array(
					'type' => 'num',
					'size' => 8, 
					'name' => 'y', 
				), 
				'z' => array(
					'type' => 'num',
					'size' => 8, 
					'name' => 'z', 
				), 
				'chat_type' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => 'チャット種別',
				), 
				'chat_target' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => '送信先', 
				), 
			), 
			'l_realmoney' => array(
				'l_realmoney_id' => array(
					'type' => 'primary',
					'size' => 8, 
					'name' => '履歴番号',
				), 
				'log_date' => array(
					'type' => 'datetime',
					'size' => 8, 
					'name' => '日時',
				), 
				'pftype' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => 'プラットフォーム区分',
				), 
				'pftype' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => 'プラットフォーム区分',
				), 
				'uid' => array(
					'type' => 'strkey',
					'size' => 30, 
					'name' => 'プラットフォーム側ユーザID', 
				), 
				'user_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'ユーザID', 
				), 
				'chara_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'キャラクタID', 
				), 
				'trade_val' => array(
					'type' => 'num',
					'size' => 12, 
					'name' => '取引額', 
				), 
				'trade_type' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => '取引種別', 
				), 
				'goods_id' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => '商品ID', 
				), 
			), 
			'l_realmoney_trade' => array(
				'l_realmoney_trade_id' => array(
					'type' => 'primary',
					'size' => 8, 
					'name' => '履歴番号',
				), 
				'log_date' => array(
					'type' => 'datetime',
					'size' => 8, 
					'name' => '日時',
				), 
				'pftype' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => 'プラットフォーム区分',
				), 
				'pftype' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => 'プラットフォーム区分',
				), 
				'uid' => array(
					'type' => 'strkey',
					'size' => 30, 
					'name' => 'プラットフォーム側ユーザID', 
				), 
				'user_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'ユーザID', 
				), 
				'chara_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'キャラクタID', 
				), 
				'trade_val' => array(
					'type' => 'num',
					'size' => 12, 
					'name' => '取引額', 
				), 
				'trade_type' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => '取引種別', 
				), 
				'goods_id' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => '商品ID', 
				), 
			), 
			'l_realmoney_payment' => array(
				'l_realmoney_payment_id' => array(
					'type' => 'primary',
					'size' => 8, 
					'name' => '履歴番号',
				), 
				'log_date' => array(
					'type' => 'datetime',
					'size' => 8, 
					'name' => '日時',
				), 
				'pftype' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => 'プラットフォーム区分',
				), 
				'uid' => array(
					'type' => 'strkey',
					'size' => 30, 
					'name' => 'プラットフォーム側ユーザID', 
				), 
				'user_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'ユーザID', 
				), 
				'chara_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'キャラクタID', 
				), 
				'trade_val' => array(
					'type' => 'num',
					'size' => 12, 
					'name' => '取引額', 
					'is_taxset' => 1, 
				), 
				//'tax' => array(
				//	'type' => 'num',
				//	'size' => 12, 
				//	'name' => '税金該当分', 
				//), 
				//'trade_type' => array(
				//	'type' => 'enum',
				//	'size' => 8, 
				//	'name' => '取引種別', 
				//), 
				'goods_id' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => '商品ID', 
				), 
				'add_point' => array(
					'type' => 'num',
					'size' => 12, 
					'name' => '加算課金通貨', 
				), 
				//'trans_id' => array(
				//	'type' => 'strkey',
				//	'size' => 20, 
				//	'name' => '決済ID', 
				//), 
				//'log_id' => array(
				//	'type' => 'strkey',
				//	'size' => 20, 
				//	'name' => '決済ログID', 
				//), 
			), 
			'l_freemoney' => array(
				'l_freemoney_id' => array(
					'type' => 'primary',
					'size' => 8, 
					'name' => '履歴番号',
				), 
				'log_date' => array(
					'type' => 'datetime',
					'size' => 8, 
					'name' => '日時',
				), 
				'pftype' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => 'プラットフォーム区分',
				), 
				'pftype' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => 'プラットフォーム区分',
				), 
				'uid' => array(
					'type' => 'strkey',
					'size' => 30, 
					'name' => 'プラットフォーム側ユーザID', 
				), 
				'user_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'ユーザID', 
				), 
				'chara_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'キャラクタID', 
				), 
				'trade_val' => array(
					'type' => 'num',
					'size' => 12, 
					'name' => '取引額', 
				), 
				'trade_type' => array(
					'type' => 'enum',
					'size' => 4, 
					'name' => '取引種別', 
				), 
				'add_type' => array(
					'type' => 'enum',
					'size' => 4, 
					'name' => '付与種別', 
				), 
			), 
			'l_kakin' => array(
				'l_kakin_id' => array(
					'type' => 'primary',
					'size' => 8, 
					'name' => '履歴番号',
				), 
				'log_date' => array(
					'type' => 'datetime',
					'size' => 8, 
					'name' => '日時',
				), 
				'pftype' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => 'プラットフォーム区分',
				), 
				'uid' => array(
					'type' => 'strkey',
					'size' => 30, 
					'name' => 'プラットフォーム側ユーザID', 
				), 
				'user_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'ユーザID', 
				), 
				'chara_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'キャラクタID', 
				), 
				'item_id' => array(
					'type' => 'enum',
					'size' => 12, 
					'name' => 'アイテムID', 
				), 
				'item_num' => array(
					'type' => 'num',
					'size' => 8, 
					'name' => 'アイテム数', 
				), 
				'subtotal' => array(
					'type' => 'num',
					'size' => 8, 
					'name' => '決済額', 
				), 
				'payment_id' => array(
					'type' => 'string',
					'size' => 8, 
					'name' => '決済ID', 
				), 
				'is_charged' => array(
					'type' => 'subquery', 
					'name' => '決済履歴有り', 
					'query' => " user_id IN (SELECT user_id FROM l_realmoney_payment)", 
				), 
				'is_notcharged' => array(
					'type' => 'subquery', 
					'name' => '決済履歴無し', 
					'query' => " user_id NOT IN (SELECT user_id FROM l_realmoney_payment)", 
				), 
			), 
			'l_shop' => array(
				'l_shop_id' => array(
					'type' => 'primary',
					'size' => 8, 
					'name' => '履歴番号',
				), 
				'log_date' => array(
					'type' => 'datetime',
					'size' => 8, 
					'name' => '日時',
				), 
				'pftype' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => 'プラットフォーム区分',
				), 
				'uid' => array(
					'type' => 'strkey',
					'size' => 30, 
					'name' => 'プラットフォーム側ユーザID', 
				), 
				'user_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'ユーザID', 
				), 
				'chara_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'キャラクタID', 
				), 
				'item_id' => array(
					'type' => 'enum',
					'size' => 12, 
					'name' => 'アイテムID', 
				), 
				'item_num' => array(
					'type' => 'num',
					'size' => 8, 
					'name' => 'アイテム数', 
				), 
				'subtotal' => array(
					'type' => 'num',
					'size' => 8, 
					'name' => '決済額', 
				), 
			), 
			'l_gacha' => array(
				'l_gacha_id' => array(
					'type' => 'primary',
					'size' => 8, 
					'name' => '履歴番号',
				), 
				'log_date' => array(
					'type' => 'datetime',
					'size' => 8, 
					'name' => '日時',
				), 
				'pftype' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => 'プラットフォーム区分',
				), 
				'uid' => array(
					'type' => 'strkey',
					'size' => 30, 
					'name' => 'プラットフォーム側ユーザID', 
				), 
				'user_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'ユーザID', 
				), 
				'chara_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'キャラクタID', 
				), 
				'gacha_type' => array(
					'type' => 'enum',
					'size' => 4, 
					'name' => 'ガチャ種別', 
				), 
				'gacha_id' => array(
					'type' => 'enum',
					'size' => 4, 
					'name' => 'ガチャID', 
				), 
				'drop_id' => array(
					'type' => 'enum',
					'size' => 12, 
					'name' => '出力品ID', 
				), 
				
				'shop_point' => array(
					'type' => 'num',
					'size' => 8, 
					'name' => '消費課金ポイント', 
				), 
				'ingame_point' => array(
					'type' => 'num',
					'size' => 8, 
					'name' => '消費ゲーム内通貨', 
				), 
				'ticket_id' => array(
					'type' => 'num',
					'size' => 8, 
					'name' => '消費チケットID', 
				), 
				'ticket_num' => array(
					'type' => 'num',
					'size' => 8, 
					'name' => '消費チケット枚数', 
				), 
				'is_free' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => '無料ガチャフラグ', 
				), 
				'is_comp' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => 'コンプリートフラグ', 
				), 
			), 
			'l_item_move' => array(
				'l_item_move_id' => array(
					'type' => 'primary',
					'size' => 8, 
					'name' => '履歴ID',
				), 
				'log_date' => array(
					'type' => 'datetime',
					'size' => 8, 
					'name' => '日時',
				), 
				'pftype' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => 'プラットフォーム区分',
				), 
				'uid' => array(
					'type' => 'strkey',
					'size' => 30, 
					'name' => 'プラットフォーム側ユーザID', 
				), 
				'user_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'ユーザID', 
				), 
				'chara_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => 'キャラクタID', 
				), 
				'type' => array(
					'type' => 'enum',
					'name' => '変動種別', 
					'size' => 10, 
				), 
				'item_num' => array(
					'type' => 'num',
					'size' => 8, 
					'name' => 'アイテム数', 
				), 
				'item_serial' => array(
					'type' => 'strkey',
					'size' => 20, 
					'name' => 'アイテムシリアル', 
				), 
				'item_id' => array(
					'type' => 'enum',
					'size' => 12, 
					'name' => 'アイテムID', 
				), 
				'action_type' => array(
					'type' => 'enum',
					'size' => 8, 
					'name' => '行動種別',
				), 
				'trade_price'  => array(
					'type' => 'num',
					'size' => 10, 
					'name' => '取引額', 
				), 
				'target_user_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => '相手ユーザID', 
				), 
				'target_chara_id' => array(
					'type' => 'key',
					'size' => 8, 
					'name' => '相手キャラクタID', 
				), 
			), 
		);
		
		foreach ($ret AS $k => $v) {
			foreach ($v AS $col => $conf) {
				if (isset($strres['TableMaster'][$col])) {
					$ret[$k][$col]['name'] = $strres['TableMaster'][$col];
				}
			}
		}
		
		return $ret;
	}
	
