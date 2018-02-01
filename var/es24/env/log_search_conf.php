<?PHP
	$loglist = array(
		'launch' => array(
			'name' => '起動', 
			'prefix' => 'launch', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID、なければ 0', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => 'キャラクターの指定があればそのID、なければ 0', 
				), 
			), 
		), 
		'begin' => array(
			'name' => '開始', 
			'prefix' => 'begin', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID、なければ 0', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => 'キャラクターの指定があればそのID、なければ 0', 
				), 
			), 
		), 
		'continue' => array(
			'name' => '継続', 
			'prefix' => 'continue', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID、なければ 0', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => 'キャラクターの指定があればそのID、なければ 0', 
				), 
			), 
		), 
		'regist' => array(
			'name' => '登録', 
			'prefix' => 'regist', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => 'キャラクターの指定があればそのID、なければ 0', 
				), 
			), 
		), 
		'login' => array(
			'name' => 'ログイン', 
			'prefix' => 'login', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '操作中のキャラクターID、キャラクターの指定がない場合はユーザID', 
				), 
				'server_id' => array(
					'name' => 'サーバID', 
					'type' => 'INTEGER', 
					'info' => 'ログインしたサーバのID', 
				), 
			), 
		), 
		'kakin' => array(
			'name' => '課金アイテム購入', 
			'prefix' => 'kakin', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '操作中のキャラクターID、選択していなければ0', 
				), 
				'item_id' => array(
					'name' => 'アイテムID', 
					'type' => 'VARCHAR(64)', 
					'info' => '購入したアイテムのID', 
				), 
				'item_qty' => array(
					'name' => '購入個数', 
					'type' => 'INTEGER', 
					'info' => '購入数', 
				), 
				'total_price' => array(
					'name' => '決済額', 
					'type' => 'INTEGER', 
					'info' => '実際に決済した金額', 
				), 
				'payment_id' => array(
					'name' => '決済番号', 
					'type' => 'VARCHAR(255)', 
					'info' => '外部の決済処理を利用した場合にそちらで発生する決済ID', 
				), 
			), 
		), 
		'shop' => array(
			'name' => '非課金アイテム購入', 
			'prefix' => 'shop', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '操作中のキャラクターID、選択していなければ0', 
				), 
				'item_id' => array(
					'name' => 'アイテムID', 
					'type' => 'VARCHAR(64)', 
					'info' => '購入したアイテムのID', 
				), 
				'item_qty' => array(
					'name' => '購入個数', 
					'type' => 'INTEGER', 
					'info' => '購入数', 
				), 
				'total_price' => array(
					'name' => '決済額', 
					'type' => 'INTEGER', 
					'info' => '実際に決済した金額', 
				), 
			), 
		), 
		'gacha' => array(
			'name' => 'ガチャ', 
			'prefix' => 'gacha', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '操作中のキャラクターID、選択していなければ0', 
				), 
				'gacha_type' => array(
					'name' => 'ガチャ種類', 
					'type' => 'INTEGER', 
					'info' => 'ガチャIDのグループ、なければ 0 ', 
				), 
				'gacha_id' => array(
					'name' => 'ガチャID', 
					'type' => 'INTEGER', 
					'info' => '回したガチャのID', 
				), 
				'gacha_drop_id' => array(
					'name' => '出力品ID', 
					'type' => 'VARCHAR(64)', 
					'info' => 'ガチャから出てきたもののID', 
				), 
				'total_price' => array(
					'name' => '消費課金ポイント', 
					'type' => 'INTEGER', 
					'info' => '消費した課金ポイントの額、なければ0', 
				), 
				'subtotal' => array(
					'name' => '消費ゲーム内通貨', 
					'type' => 'INTEGER', 
					'info' => '消費したゲーム内通貨、なければ0', 
				), 
				'ticket_id' => array(
					'name' => '使用チケットID', 
					'type' => 'INTEGER', 
					'info' => '使用したチケットのID、なければ0', 
				), 
				'ticket_amt' => array(
					'name' => '使用チケット枚数', 
					'type' => 'INTEGER', 
					'info' => '使用したチケット枚数、なければ0', 
				), 
				'is_free' => array(
					'name' => '無料フラグ', 
					'type' => 'INTEGER', 
					'info' => '初回やデイリーなどコインやチケットを消費せずに回した場合は 1 が入る', 
				), 
				'is_comp' => array(
					'name' => 'コンプリートフラグ', 
					'type' => 'INTEGER', 
					'info' => 'コンプリートを達成したら 1 が入る', 
				), 
				'card_id' => array(
					'name' => '出力されたカードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'bild' => array(
					'name' => '限突段階', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'item_id' => array(
					'name' => '変換先アイテムID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'item_act' => array(
			'name' => 'アイテム変動', 
			'prefix' => 'item_act', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '操作中のキャラクターID、選択していなければ0', 
				), 
				'move_type' => array(
					'name' => '変動種別', 
					'type' => 'INTEGER', 
					'info' => '0:変動なし、1:アイテムの追加、2:アイテムの移動 (ユーザ→相手ユーザ)、3:アイテムの消失、4:アイテムの移動 (相手ユーザ→ユーザ)', 
				), 
				'item_id' => array(
					'name' => 'アイテムID', 
					'type' => 'VARCHAR(64)', 
					'info' => '変動のあったアイテムのID', 
				), 
				'item_amt' => array(
					'name' => 'アイテム数', 
					'type' => 'INTEGER', 
					'info' => 'アイテム個数', 
				), 
				'item_ser' => array(
					'name' => 'アイテムシリアルコード', 
					'type' => 'VARCHAR(32)', 
					'info' => 'アイテムの発生ごとにユニークに振られるアイテムのコード、ない場合は空欄', 
				), 
				'action_type' => array(
					'name' => '行動内容', 
					'type' => 'INTEGER', 
					'info' => '行動内容コード', 
				), 
				'trade_price' => array(
					'name' => '取引価格', 
					'type' => 'INTEGER', 
					'info' => '取引時に設定されたゲーム内通貨価格、変動の無い場合は 0', 
				), 
				'target_user_id' => array(
					'name' => '相手ユーザID', 
					'type' => 'INTEGER', 
					'info' => '行動を起こした相手のキャラクターのID、相手がなければ 0', 
				), 
				'target_chara_id' => array(
					'name' => '相手キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '行動を起こした相手のユーザのID、相手がなければ 0', 
				), 
			), 
		), 
		'step' => array(
			'name' => '進行状況', 
			'prefix' => 'step', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '操作中のキャラクターID、選択していなければ0', 
				), 
				'step' => array(
					'name' => 'ステップ', 
					'type' => 'INTEGER', 
					'info' => '到達した進行ステップ数', 
				), 
			), 
		), 
		'event_step' => array(
			'name' => 'イベント進行状況', 
			'prefix' => 'event_step', 
			'cols' => array(
				'log_date' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'UID', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'ユーザID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID', 
				), 
				'chara_id' => array(
					'name' => 'ステップ', 
					'type' => 'INTEGER', 
					'info' => '操作中のキャラクターID、選択していなければ0', 
				), 
				'event_step' => array(
					'name' => 'イベントID', 
					'type' => 'INTEGER', 
					'info' => '到達した進行ステップ数', 
				), 
				'event_id' => array(
					'name' => '', 
					'type' => 'INTEGER', 
					'info' => 'イベントごとに発番される開催番号', 
				), 
			), 
		), 
		'friend' => array(
			'name' => 'フレンド', 
			'prefix' => 'friend', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '操作中のキャラクターID、選択していなければ0', 
				), 
				'friend_operate_state' => array(
					'name' => 'フレンド申請状態', 
					'type' => 'INTEGER', 
					'info' => '1：申請、2：申請キャンセル、3：承諾、4：申請却下、5：解除', 
				), 
				'target_user_id' => array(
					'name' => '相手ユーザID', 
					'type' => 'INTEGER', 
					'info' => '行動を起こした相手のキャラクターのID', 
				), 
				'target_chara_id' => array(
					'name' => '相手キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '行動を起こした相手のユーザのID、選択していなければ0', 
				), 
			), 
		), 
		'chat' => array(
			'name' => 'チャット', 
			'prefix' => 'chat', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '操作中のキャラクターID、選択していなければ0', 
				), 
				'message' => array(
					'name' => '発言内容', 
					'type' => 'TEXT', 
					'info' => 'チャットで発言した内容', 
				), 
				'server_id' => array(
					'name' => 'サーバ', 
					'type' => 'INTEGER', 
					'info' => '発言を行ったサーバ', 
				), 
				'area_id' => array(
					'name' => 'エリア', 
					'type' => 'INTEGER', 
					'info' => '発言を行ったエリア', 
				), 
				'x' => array(
					'name' => 'x', 
					'type' => 'INTEGER', 
					'info' => '発言を行ったx座標', 
				), 
				'y' => array(
					'name' => 'y', 
					'type' => 'INTEGER', 
					'info' => '発言を行ったy座標', 
				), 
				'z' => array(
					'name' => 'z', 
					'type' => 'INTEGER', 
					'info' => '発言を行ったz座標', 
				), 
				'chat_type' => array(
					'name' => '発言種別', 
					'type' => 'INTEGER', 
					'info' => '発言の種別', 
				), 
				'chat_target' => array(
					'name' => '発言対象', 
					'type' => 'INTEGER', 
					'info' => '発言の対象', 
				), 
			), 
		), 
		'realmoney_payment' => array(
			'name' => '決済ログ', 
			'prefix' => 'realmoney_payment', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '操作中のキャラクターID、選択していなければ0', 
				), 
				'check_price' => array(
					'name' => '決済額', 
					'type' => 'INTEGER', 
					'info' => '税金等含めて実際に決済を行った金額', 
				), 
				'trade_type' => array(
					'name' => '取引タイプ', 
					'type' => 'INTEGER', 
					'info' => '1: 購入の固定値', 
				), 
				'goods_id' => array(
					'name' => '商品番号', 
					'type' => 'VARCHAR(64)', 
					'info' => '商品番号。', 
				), 
				'biko' => array(
					'name' => '備考', 
					'type' => 'VARCHAR(64)', 
					'info' => '購入経路等参照用の備考', 
				), 
				'shoppoint' => array(
					'name' => '加算課金通貨', 
					'type' => 'INTEGER', 
					'info' => '購入時に加算された課金通貨', 
				), 
			), 
		), 
		'realmoney_trade' => array(
			'name' => '有料課金通貨変動', 
			'prefix' => 'realmoney_trade', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '操作中のキャラクターID、選択していなければ0', 
				), 
				'shoppoint' => array(
					'name' => 'リアルマネー取引額', 
					'type' => 'INTEGER', 
					'info' => 'リアルマネーの購入、使用、期限切れ各々の額。増額は正の値、減額は負の値になる。', 
				), 
				'trade_type' => array(
					'name' => '取引タイプ', 
					'type' => 'INTEGER', 
					'info' => '1: 購入 2: 使用 3: 期限切れ', 
				), 
				'goods_id' => array(
					'name' => '商品番号', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'biko' => array(
					'name' => '備考', 
					'type' => 'VARCHAR(64)', 
					'info' => '購入経路等参照用の備考', 
				), 
			), 
		), 
		'freemoney' => array(
			'name' => '無料課金通貨変動', 
			'prefix' => 'freemoney', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '操作中のキャラクターID、選択していなければ0', 
				), 
				'shoppoint' => array(
					'name' => '取引額', 
					'type' => 'INTEGER', 
					'info' => '無料付与マネーの購入、使用、期限切れ各々の額。増額は正の値、減額は負の値になる。', 
				), 
				'trade_type' => array(
					'name' => '取引タイプ', 
					'type' => 'INTEGER', 
					'info' => '1: 購入 2: 使用 3: 期限切れ', 
				), 
				'add_type' => array(
					'name' => '付与タイプ', 
					'type' => 'INTEGER', 
					'info' => '付与されたタイプ。各ゲームコンテンツでタイプが異なるので、どういうものかは各ゲームコンテンツ担当者に伺う。', 
				), 
				'goods_id' => array(
					'name' => '商品番号', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'biko' => array(
					'name' => '備考', 
					'type' => 'VARCHAR(64)', 
					'info' => '購入経路等参照用の備考', 
				), 
			), 
		), 
		'access' => array(
			'name' => 'アクセス', 
			'prefix' => 'access', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'ope' => array(
					'name' => '要求操作', 
					'type' => 'TEXT', 
					'info' => '', 
				), 
				'result' => array(
					'name' => '結果コード', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'ip' => array(
					'name' => 'アクセス元IPv4アドレス', 
					'type' => 'TEXT', 
					'info' => '', 
				), 
				'pre_sess' => array(
					'name' => '処理前のセッションキーの一部', 
					'type' => 'TEXT', 
					'info' => '', 
				), 
				'aff_sess' => array(
					'name' => '処理後のセッションキーの一部', 
					'type' => 'TEXT', 
					'info' => '', 
				), 
				'cost' => array(
					'name' => 'ope の処理に要した時間', 
					'type' => 'FLOAT', 
					'info' => '', 
				), 
			), 
		), 
		'loginbonus' => array(
			'name' => 'ログインボーナス', 
			'prefix' => 'loginbonus', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'loginbonus_id' => array(
					'name' => 'ログインボーナスID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'total_login_days' => array(
					'name' => '累計ログイン日数', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'continue_days' => array(
					'name' => '継続ログイン日数', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'loginbonus_prize_id' => array(
					'name' => 'ログインボーナス報酬ID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'is_blank_day' => array(
					'name' => '前回ログインから日が空いた場合は 1', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'loginbonus_days' => array(
					'name' => 'このログインボーナスの継続日数', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'login_dist' => array(
			'name' => '全体配布アイテム', 
			'prefix' => 'login_dist', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'login_dist' => array(
					'name' => '配布アイテムID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'login_ua' => array(
			'name' => 'ログイン時端末', 
			'prefix' => 'login_ua', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'ua' => array(
					'name' => '端末を示す文字列', 
					'type' => 'VARCHAR(255)', 
					'info' => '', 
				), 
				'os' => array(
					'name' => '使用している端末のOSバージョン', 
					'type' => 'VARCHAR(255)', 
					'info' => '', 
				), 
			), 
		), 
		'quest_begin' => array(
			'name' => 'クエスト開始', 
			'prefix' => 'quest_begin', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'level' => array(
					'name' => 'クエスト開始時プレイヤーレベル', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'map_id' => array(
					'name' => 'マップID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'quest_id' => array(
					'name' => 'クエストID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'transaction_id' => array(
					'name' => 'トランザクションID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'helper_player_id' => array(
					'name' => '助っ人プレイヤーID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'if_friend_helper' => array(
					'name' => 'フレンドか', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'helper_card_id' => array(
					'name' => '助っ人カードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'leader_card_id' => array(
					'name' => 'リーダーカードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'slot1_card_id' => array(
					'name' => 'スロット１番に刺さっているカードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'slot2_card_id' => array(
					'name' => 'スロット２番に刺さっているカードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'slot3_card_id' => array(
					'name' => 'スロット３番に刺さっているカードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'slot4_card_id' => array(
					'name' => 'スロット４番に刺さっているカードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'slot5_card_id' => array(
					'name' => 'スロット５番に刺さっているカードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'slot6_card_id' => array(
					'name' => 'スロット６番に刺さっているカードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'quest_keepalive' => array(
			'name' => 'クエスト処理有無確認', 
			'prefix' => 'quest_keepalive', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'transaction_id' => array(
					'name' => 'トランザクションID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'is_alive' => array(
					'name' => '有効なら 1', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'saved_transaction_id' => array(
					'name' => '記録上に残っていたトランザクションID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'map_id' => array(
					'name' => '記録上に残っていたマップID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'quest_id' => array(
					'name' => '記録上に残っていたクエストID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'quest_commit' => array(
			'name' => 'クエスト完了', 
			'prefix' => 'quest_commit', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'map_id' => array(
					'name' => 'マップID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'quest_id' => array(
					'name' => 'クエストID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'transaction_id' => array(
					'name' => 'トランザクションID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'bomb' => array(
					'name' => 'ボム使用回数', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'combo' => array(
					'name' => '累計コンボ数', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'max_combo' => array(
					'name' => '最大コンボ数', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'player_exp' => array(
					'name' => '報酬プレイヤー経験値', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_exp' => array(
					'name' => '報酬カード経験値', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'mag' => array(
					'name' => '報酬金', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'quest_cheat' => array(
			'name' => 'クエストチート', 
			'prefix' => 'quest_cheat', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'map_id' => array(
					'name' => 'マップID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'quest_id' => array(
					'name' => 'クエストID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'transaction_id' => array(
					'name' => 'トランザクションID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
			), 
		), 
		'quest_retry' => array(
			'name' => 'クエスト再挑戦', 
			'prefix' => 'quest_retry', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'map_id' => array(
					'name' => 'マップID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'quest_id' => array(
					'name' => 'クエストID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'new_transaction_id' => array(
					'name' => '再生成後のトランザクションID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'transaction_id' => array(
					'name' => '処理前のトランザクションID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'item_id' => array(
					'name' => '消費したアイテムID、消費して無ければ 0', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'is_mc' => array(
					'name' => '消費した課金通貨、消費して無ければ 0', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'quest_retire' => array(
			'name' => 'クエストリタイア', 
			'prefix' => 'quest_retire', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'map_id' => array(
					'name' => 'マップID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'quest_id' => array(
					'name' => 'クエストID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'transaction_id' => array(
					'name' => 'トランザクションID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
			), 
		), 
		'friend_point' => array(
			'name' => 'フレンドポイント', 
			'prefix' => 'friend_point', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'fp' => array(
					'name' => '変動値', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'card_tribute' => array(
			'name' => 'カードレベル強制上げ', 
			'prefix' => 'card_tribute', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_id' => array(
					'name' => '成長させたカードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_slot' => array(
					'name' => '成長させたカードスロット', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_serial' => array(
					'name' => '成長させたカードシリアル', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'up_level' => array(
					'name' => '上昇レベル', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'after_level' => array(
					'name' => '処理後のレベル', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'mag' => array(
					'name' => '消費mag', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'after_mag' => array(
					'name' => '消費後の所持mag', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'presentbox_push' => array(
			'name' => 'プレゼントボックス搬入', 
			'prefix' => 'presentbox_push', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'present_id' => array(
					'name' => 'プレゼントボックス中身ID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'type' => array(
					'name' => '種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'item' => array(
					'name' => '格納物ID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'amt' => array(
					'name' => '格納量', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'presentbox_recv' => array(
			'name' => 'プレゼントボックス受取', 
			'prefix' => 'presentbox_recv', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'present_id' => array(
					'name' => 'プレゼントボックス中身ID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'type' => array(
					'name' => '種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'item' => array(
					'name' => '格納物ID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'amt' => array(
					'name' => '格納量', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'presentbox_lost' => array(
			'name' => 'プレゼントボックス消失', 
			'prefix' => 'presentbox_lost', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'present_id' => array(
					'name' => 'スタッシュ中身ID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'type' => array(
					'name' => '種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'item' => array(
					'name' => '格納物ID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'amt' => array(
					'name' => '格納量', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'event_addpoint' => array(
			'name' => 'イベントポイント', 
			'prefix' => 'event_addpoint', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'event_id' => array(
					'name' => '処理したイベントのID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'raidboss_id' => array(
					'name' => 'レイドボスID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'raid_serial' => array(
					'name' => 'レイドシリアル', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'event_point' => array(
					'name' => '取得ポイント', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'after_event_point' => array(
					'name' => '取得後の所持ポイント', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'combo_bonus' => array(
					'name' => '獲得ポイントのうちコンボボーナス分', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'boost_bonus' => array(
					'name' => '獲得ポイントのうちカードによる増幅分', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'event_recvreward' => array(
			'name' => 'イベント報酬', 
			'prefix' => 'event_recvreward', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'event_id' => array(
					'name' => 'イベントID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'event_reward_id' => array(
					'name' => '報酬ID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'event_point' => array(
					'name' => '受け取り時のポイント', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'killcount' => array(
					'name' => '受け取り時の討伐数', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'event_recvreward_ranking' => array(
			'name' => 'イベント結果報酬', 
			'prefix' => 'event_recvreward_ranking', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'event_id' => array(
					'name' => 'イベントID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'raidboss_id' => array(
					'name' => 'レイドボスID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'event_reward_id' => array(
					'name' => '報酬ID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'rank' => array(
					'name' => '順位', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'account_disable' => array(
			'name' => 'アカウント封鎖', 
			'prefix' => 'account_disable', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'new_account' => array(
					'name' => '書き換え後のUID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
			), 
		), 
		'gacha_step' => array(
			'name' => 'ガチャステップ', 
			'prefix' => 'gacha_step', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '操作中のキャラクターID、選択していなければ0', 
				), 
				'gacha_goods_id' => array(
					'name' => 'ガチャ商品ID', 
					'type' => 'INTEGER', 
					'info' => '回したガチャの商品ID', 
				), 
				'hazure_group' => array(
					'name' => 'はずれグループ', 
					'type' => 'INTEGER', 
					'info' => '処理したはずれグループ番号', 
				), 
				'after_step' => array(
					'name' => '処理後 step', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'pre_step' => array(
					'name' => '処理前 step', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'is_goal' => array(
					'name' => '規定ゴールに達したか', 
					'type' => 'INTEGER', 
					'info' => 'ゴールに達してランク保障が発動した場合は 1', 
				), 
				'is_reset' => array(
					'name' => 'リセット条件に合致したか', 
					'type' => 'INTEGER', 
					'info' => '特定ランク品が抽選されたことによるリセットが発生した場合は 1', 
				), 
			), 
		), 
		'levelup' => array(
			'name' => 'レベルアップ', 
			'prefix' => 'levelup', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'oldlevel' => array(
					'name' => '上昇前のレベル', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'newlevel' => array(
					'name' => '現在のレベル', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'oldexp' => array(
					'name' => '処理前の経験値', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'current_exp' => array(
					'name' => '現在の経験値', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'card_bild' => array(
			'name' => 'カード限突', 
			'prefix' => 'card_bild', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_slot' => array(
					'name' => '対象カードスロット', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_id' => array(
					'name' => '対象カードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_serial' => array(
					'name' => '対象カードシリアル', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'bild' => array(
					'name' => '処理後の限突段階', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'merge_card_id' => array(
					'name' => '混ぜたカードID、アイテム消費で行った場合は 0', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'item_id' => array(
					'name' => '消費したアイテム、カードによる場合は 0', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'item_qty' => array(
					'name' => '消費したアイテム個数、カードによる場合は 0', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'card_open' => array(
			'name' => 'カード才能開花', 
			'prefix' => 'card_open', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_slot' => array(
					'name' => '対象カードスロット', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_id' => array(
					'name' => '対象カードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_serial' => array(
					'name' => '対象カードシリアル', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_branch_id' => array(
					'name' => '開放した枝ID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'is_payment' => array(
					'name' => '課金通貨の消費で開放した場合は 1', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'character_avatar' => array(
			'name' => 'キャラ衣装変更', 
			'prefix' => 'character_avatar', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'character_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'costume_id' => array(
					'name' => '衣装ID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'background_id' => array(
					'name' => '背景ID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'mission_clear' => array(
			'name' => 'ミッション要件の達成', 
			'prefix' => 'mission_clear', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'mission_id' => array(
					'name' => '達成したミッションID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'mission_check' => array(
			'name' => 'ミッション要件の完了', 
			'prefix' => 'mission_check', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'mission_id' => array(
					'name' => '完了したミッションID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'mission_comp' => array(
			'name' => 'ミッションコンプの達成', 
			'prefix' => 'mission_comp', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'mission_comp_id' => array(
					'name' => '達成したミッションコンプのID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'otetsudai_start' => array(
			'name' => 'お手伝い開始', 
			'prefix' => 'otetsudai_start', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'slot_1_1' => array(
					'name' => '1_1 に刺したカードスロット', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'slot_1_2' => array(
					'name' => '1_2 に刺したカードスロット', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'slot_2_1' => array(
					'name' => '2_1 に刺したカードスロット', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'slot_2_2' => array(
					'name' => '2_2 に刺したカードスロット', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'slot_3_1' => array(
					'name' => '3_1 に刺したカードスロット', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'slot_3_2' => array(
					'name' => '3_2 に刺したカードスロット', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_1_1' => array(
					'name' => '1_1 に刺したカードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_1_2' => array(
					'name' => '1_2 に刺したカードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_2_1' => array(
					'name' => '2_1 に刺したカードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_2_2' => array(
					'name' => '2_2 に刺したカードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_3_1' => array(
					'name' => '3_1 に刺したカードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_3_2' => array(
					'name' => '3_2 に刺したカードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'otetsudai_pair_id' => array(
					'name' => '開放したコレクションのID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'otetsudai_end' => array(
			'name' => 'お手伝い完了', 
			'prefix' => 'otetsudai_end', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'otetsudai_exp' => array(
					'name' => '発生したお手伝い経験値', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'mag' => array(
					'name' => '取得したゲーム内通貨', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'player_exp' => array(
					'name' => '取得したプレイヤー経験値', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'fp' => array(
					'name' => '取得したフレンドポイント', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'bit' => array(
					'name' => '取得した第二ゲーム内通貨', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'otetsudai_pair_id' => array(
					'name' => '開放したコレクションのID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'otetsudai_end_item' => array(
			'name' => 'お手伝い完了報酬', 
			'prefix' => 'otetsudai_end_item', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'item_id' => array(
					'name' => '取得したアイテムID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'item_qty' => array(
					'name' => '取得したアイテム個数', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'item_supply' => array(
			'name' => '配給品受け取り', 
			'prefix' => 'item_supply', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'item_id' => array(
					'name' => '受け取ったアイテムID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'supply' => array(
					'name' => '受け取った数', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'lost' => array(
					'name' => '受け取り損ねた数', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'collection_unlock' => array(
			'name' => 'コレクション入手', 
			'prefix' => 'collection_unlock', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'character_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'collection_type' => array(
					'name' => 'コレクション種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'collection_id' => array(
					'name' => 'コレクションID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'collection_itemunlock' => array(
			'name' => 'アイテムでコレクション開放', 
			'prefix' => 'collection_itemunlock', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'item_id' => array(
					'name' => '消費アイテムID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'collection_type' => array(
					'name' => 'コレクション種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'collection_id' => array(
					'name' => 'コレクションID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'account_move' => array(
			'name' => 'アカウント移行', 
			'prefix' => 'account_move', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'new_move_code' => array(
					'name' => '新規に発行した引き継ぎコード', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'old_move_code' => array(
					'name' => '引き継ぐ際に使った引き継ぎコード', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'remove_rm' => array(
					'name' => '消失した課金通貨の有料枠', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'remove_fm' => array(
					'name' => '消失した課金通貨の無料枠', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'login_failed' => array(
			'name' => 'ログイン失敗', 
			'prefix' => 'login_failed', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => '空', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => '空', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => '空', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'naze' => array(
					'name' => '失敗理由', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'try_account' => array(
					'name' => '入力されたアカウント', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'try_move_code' => array(
					'name' => '入力された引き継ぎコード', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'try_password' => array(
					'name' => '入力されたパスワード', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_agent' => array(
					'name' => '入力されたユーザエージェント', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'os' => array(
					'name' => '入力されたOS', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
			), 
		), 
		'cynapse_item' => array(
			'name' => 'CYNAPSE用item', 
			'prefix' => 'cynapse_item', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'item_id' => array(
					'name' => 'アイテムID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'unit_price' => array(
					'name' => '単価', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'qty' => array(
					'name' => '数量', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'payment_type' => array(
					'name' => '決済種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'rm' => array(
					'name' => '消費課金通貨の有料枠', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'fm' => array(
					'name' => '消費課金通貨の無料枠', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'cynapse_gacha' => array(
			'name' => 'CYNAPSE用gacha', 
			'prefix' => 'cynapse_gacha', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'gacha_id' => array(
					'name' => 'ガチャID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'payment_type' => array(
					'name' => '支払種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'purchased_money' => array(
					'name' => '消費した課金通貨のうち有料枠', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'non_purchased_money' => array(
					'name' => '消費した課金通貨のうち無料枠', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'step_count' => array(
					'name' => 'ガチャのステップ', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'results_id' => array(
					'name' => '抽選された出力品ID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'rarity_id' => array(
					'name' => '抽選結果のランク', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'item_count' => array(
					'name' => '出力数', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'paymentpoint_sub' => array(
			'name' => '課金通貨消費詳細', 
			'prefix' => 'paymentpoint_sub', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'buy_point' => array(
					'name' => '消費ポイントの分母', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'goods_id' => array(
					'name' => '減らしたポイントを買った時の商品', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'sub_point' => array(
					'name' => '減ったポイント', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'regist_date' => array(
					'name' => 'このポイントを買った日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
			), 
		), 
		'pawn' => array(
			'name' => 'アイテム変換', 
			'prefix' => 'pawn', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'item_id' => array(
					'name' => '変換したアイテムID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'item_qty' => array(
					'name' => '変換したアイテム数量', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'bit' => array(
					'name' => '変換された第二ゲーム内通貨', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'mag' => array(
					'name' => '変換されたゲーム内通貨', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'mc' => array(
					'name' => '変換された課金通貨', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'player_exp' => array(
					'name' => '変換されたプレイヤー経験値', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'add_card' => array(
			'name' => 'カード入手', 
			'prefix' => 'add_card', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => '', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'card_id' => array(
					'name' => 'カードID', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'bild' => array(
					'name' => '限突段階', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
				'item_id' => array(
					'name' => '変換後アイテム', 
					'type' => 'INTEGER', 
					'info' => '', 
				), 
			), 
		), 
		'gacha_omake' => array(
			'name' => 'ガチャオマケ', 
			'prefix' => 'gacha_omake', 
			'cols' => array(
				'log_date' => array(
					'name' => '発生日時', 
					'type' => 'DATETIME', 
					'info' => 'ログを吐いた日時', 
				), 
				'pf_type' => array(
					'name' => 'プラットフォーム種別', 
					'type' => 'INTEGER', 
					'info' => 'プラットフォームを示す種別番号', 
				), 
				'uid' => array(
					'name' => 'UID', 
					'type' => 'VARCHAR(64)', 
					'info' => '提供しているプラットフォーム上でのユーザID、存在しない場合は設定しない', 
				), 
				'user_id' => array(
					'name' => 'ユーザID', 
					'type' => 'INTEGER', 
					'info' => 'ゲーム側で管理しているユーザのID', 
				), 
				'chara_id' => array(
					'name' => 'キャラクターID', 
					'type' => 'INTEGER', 
					'info' => '操作中のキャラクターID、選択していなければ0', 
				), 
				'gacha_type' => array(
					'name' => 'ガチャ種類', 
					'type' => 'INTEGER', 
					'info' => 'ガチャIDのグループ、なければ 0 ', 
				), 
				'gacha_id' => array(
					'name' => 'ガチャID', 
					'type' => 'INTEGER', 
					'info' => '回したガチャのID', 
				), 
				'gacha_drop_id' => array(
					'name' => '未使用', 
					'type' => 'VARCHAR(64)', 
					'info' => '未使用', 
				), 
				'total_price' => array(
					'name' => '消費課金ポイント', 
					'type' => 'INTEGER', 
					'info' => '消費した課金ポイントの額、なければ0', 
				), 
				'subtotal' => array(
					'name' => '消費ゲーム内通貨', 
					'type' => 'INTEGER', 
					'info' => '消費したゲーム内通貨、なければ0', 
				), 
				'ticket_id' => array(
					'name' => '使用チケットID', 
					'type' => 'INTEGER', 
					'info' => '使用したチケットのID、なければ0', 
				), 
				'ticket_amt' => array(
					'name' => '使用チケット枚数', 
					'type' => 'INTEGER', 
					'info' => '使用したチケット枚数、なければ0', 
				), 
				'omake_mag' => array(
					'name' => 'オマケゲーム内通貨', 
					'type' => 'INTEGER', 
					'info' => '購入時に抽選とは関係なく付与されるゲーム内通貨', 
				), 
				'omake_bit' => array(
					'name' => 'オマケ第二ゲーム内通貨', 
					'type' => 'INTEGER', 
					'info' => '購入時に抽選とは関係なく付与される第二ゲーム内通貨', 
				), 
				'omake_item_id' => array(
					'name' => 'オマケアイテムID', 
					'type' => 'INTEGER', 
					'info' => '購入時に抽選とは関係なく付与されるアイテム', 
				), 
				'omake_item_qty' => array(
					'name' => 'オマケアイテム数量', 
					'type' => 'INTEGER', 
					'info' => '購入時に抽選とは関係なく付与されるアイテムの数量', 
				), 
				'omake_rand_item_id' => array(
					'name' => 'オマケ抽選アイテムID', 
					'type' => 'INTEGER', 
					'info' => '購入時ランダムに抽選されて付与されるアイテム', 
				), 
				'omake_rand_item_qty' => array(
					'name' => 'オマケ抽選アイテム数量', 
					'type' => 'INTEGER', 
					'info' => '購入時ランダムに抽選されて付与されるアイテムの数量', 
				), 
			), 
		), 
	);
?>
