<?PHP
	$loglist = array(
		'launch' => array(
			'name' => '起動', 
			'prefix' => 'launch', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'begin' => array(
			'name' => '開始', 
			'prefix' => 'begin', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'continue' => array(
			'name' => '継続', 
			'prefix' => 'continue', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'regist' => array(
			'name' => '登録', 
			'prefix' => 'regist', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => '広告コード', 
					'type' => 'VARCHAR(255)', 
				), 
			), 
		), 
		'unregist' => array(
			'name' => '解約', 
			'prefix' => 'unregist', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'login' => array(
			'name' => 'ログイン', 
			'prefix' => 'login', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => 'サーバID', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'chara_create' => array(
			'name' => 'キャラクター作成', 
			'prefix' => 'chara_create', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => 'セーブスロットID', 
					'type' => 'INTEGER', 
				), 
				'7' => array(
					'name' => 'キャラクター名', 
					'type' => 'VARCHAR(64)', 
				), 
			), 
		), 
		'chara_delete' => array(
			'name' => 'キャラクター削除', 
			'prefix' => 'chara_delete', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => 'セーブスロットID', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'kakin' => array(
			'name' => '課金アイテム購入', 
			'prefix' => 'kakin', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => 'アイテムID', 
					'type' => 'VARCHAR(64)', 
				), 
				'7' => array(
					'name' => '購入個数', 
					'type' => 'INTEGER', 
				), 
				'8' => array(
					'name' => '決済額', 
					'type' => 'INTEGER', 
				), 
				'9' => array(
					'name' => '決済番号', 
					'type' => 'VARCHAR(255)', 
				), 
			), 
		), 
		'shop' => array(
			'name' => '非課金アイテム購入', 
			'prefix' => 'shop', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => 'アイテムID', 
					'type' => 'VARCHAR(64)', 
				), 
				'7' => array(
					'name' => '購入個数', 
					'type' => 'INTEGER', 
				), 
				'8' => array(
					'name' => '決済額', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'gacha' => array(
			'name' => 'ガチャ', 
			'prefix' => 'gacha', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => 'ガチャ種類', 
					'type' => 'INTEGER', 
				), 
				'7' => array(
					'name' => 'ガチャID', 
					'type' => 'INTEGER', 
				), 
				'8' => array(
					'name' => '出力品ID', 
					'type' => 'VARCHAR(64)', 
				), 
				'9' => array(
					'name' => '消費課金ポイント', 
					'type' => 'INTEGER', 
				), 
				'10' => array(
					'name' => '消費ゲーム内通貨', 
					'type' => 'INTEGER', 
				), 
				'11' => array(
					'name' => '使用チケットID', 
					'type' => 'INTEGER', 
				), 
				'12' => array(
					'name' => '使用チケット枚数', 
					'type' => 'INTEGER', 
				), 
				'13' => array(
					'name' => '無料フラグ', 
					'type' => 'INTEGER', 
				), 
				'14' => array(
					'name' => 'コンプリートフラグ', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'item_act' => array(
			'name' => 'アイテム変動', 
			'prefix' => 'item_act', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => '変動種別', 
					'type' => 'INTEGER', 
				), 
				'7' => array(
					'name' => 'アイテムID', 
					'type' => 'VARCHAR(64)', 
				), 
				'8' => array(
					'name' => 'アイテム数', 
					'type' => 'INTEGER', 
				), 
				'9' => array(
					'name' => 'アイテムシリアルコード', 
					'type' => 'VARCHAR(32)', 
				), 
				'10' => array(
					'name' => '行動内容', 
					'type' => 'INTEGER', 
				), 
				'11' => array(
					'name' => '取引価格', 
					'type' => 'INTEGER', 
				), 
				'12' => array(
					'name' => '相手ユーザID', 
					'type' => 'INTEGER', 
				), 
				'13' => array(
					'name' => '相手キャラクターID', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'step' => array(
			'name' => '進行状況', 
			'prefix' => 'step', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => 'ステップ', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'event_step' => array(
			'name' => 'イベント進行状況', 
			'prefix' => 'event_step', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => 'ステップ', 
					'type' => 'INTEGER', 
				), 
				'7' => array(
					'name' => 'イベントID', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'friend' => array(
			'name' => 'フレンド', 
			'prefix' => 'friend', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => 'フレンド申請状態', 
					'type' => 'INTEGER', 
				), 
				'7' => array(
					'name' => '相手ユーザID', 
					'type' => 'INTEGER', 
				), 
				'8' => array(
					'name' => '相手キャラクターID', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'chat' => array(
			'name' => 'チャット', 
			'prefix' => 'chat', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => '発言内容', 
					'type' => 'TEXT', 
				), 
				'7' => array(
					'name' => 'サーバ', 
					'type' => 'INTEGER', 
				), 
				'8' => array(
					'name' => 'エリア', 
					'type' => 'INTEGER', 
				), 
				'9' => array(
					'name' => 'x', 
					'type' => 'INTEGER', 
				), 
				'10' => array(
					'name' => 'y', 
					'type' => 'INTEGER', 
				), 
				'11' => array(
					'name' => 'z', 
					'type' => 'INTEGER', 
				), 
				'12' => array(
					'name' => '発言種別', 
					'type' => 'INTEGER', 
				), 
				'13' => array(
					'name' => '発言対象', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'realmoney' => array(
			'name' => '(旧)リアルマネー', 
			'prefix' => 'realmoney', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => 'リアルマネー取引額', 
					'type' => 'INTEGER', 
				), 
				'7' => array(
					'name' => '取引タイプ', 
					'type' => 'INTEGER', 
				), 
				'8' => array(
					'name' => '商品番号', 
					'type' => 'VARCHAR(64)', 
				), 
			), 
		), 
		'realmoney_payment' => array(
			'name' => '決済', 
			'prefix' => 'realmoney_payment', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => '決済額', 
					'type' => 'INTEGER', 
				), 
				'7' => array(
					'name' => '取引タイプ', 
					'type' => 'INTEGER', 
				), 
				'8' => array(
					'name' => '商品番号', 
					'type' => 'VARCHAR(64)', 
				), 
				'9' => array(
					'name' => '備考', 
					'type' => 'VARCHAR(64)', 
				), 
				'10' => array(
					'name' => '加算課金通貨', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'realmoney_trade' => array(
			'name' => '課金通貨変動', 
			'prefix' => 'realmoney_trade', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => 'リアルマネー取引額', 
					'type' => 'INTEGER', 
				), 
				'7' => array(
					'name' => '取引タイプ', 
					'type' => 'INTEGER', 
				), 
				'8' => array(
					'name' => '商品番号', 
					'type' => 'VARCHAR(64)', 
				), 
				'9' => array(
					'name' => '備考', 
					'type' => 'VARCHAR(64)', 
				), 
			), 
		), 
		'invite' => array(
			'name' => '友達招待成立', 
			'prefix' => 'invite', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => '招待されたUID', 
					'type' => 'VARCHAR(64)', 
				), 
				'7' => array(
					'name' => '招待されたユーザID', 
					'type' => 'INTEGER', 
				), 
				'8' => array(
					'name' => '招待されたキャラクターID', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'invite_item_post' => array(
			'name' => '招待アイテムポスト', 
			'prefix' => 'invite_item_post', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'invite_item_receive' => array(
			'name' => '招待アイテム受け取り', 
			'prefix' => 'invite_item_receive', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'invite_ticket_give' => array(
			'name' => '招待チケット付与', 
			'prefix' => 'invite_ticket_give', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => 'チケットID', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'invite_ticket_pay' => array(
			'name' => '招待チケット消費', 
			'prefix' => 'invite_ticket_pay', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => 'チケットID', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
		'freemoney' => array(
			'name' => '無料付与マネー', 
			'prefix' => 'freemoney', 
			'cols' => array(
				'1' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
				), 
				'2' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
				), 
				'3' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
				), 
				'4' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
				), 
				'5' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
				), 
				'6' => array(
					'name' => '取引額', 
					'type' => 'INTEGER', 
				), 
				'7' => array(
					'name' => '取引タイプ', 
					'type' => 'INTEGER', 
				), 
				'8' => array(
					'name' => '付与タイプ', 
					'type' => 'INTEGER', 
				), 
			), 
		), 
	);
?>
