<?PHP
//print_r($_SESSION);
//var_dump($_SESSION['__mylog']);
	
	require_once('env.php');
	$env = env();
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	
	$con = admin_con();
	
	$player_id = (preg_match('/^[0-9]{1,10}$/', $LOCAL_SESSION['player_id']) ? $LOCAL_SESSION['player_id'] : '');
	$account = (strlen($LOCAL_SESSION['account']) > 0 ? $LOCAL_SESSION['account'] : '');
	$move_code = (strlen($LOCAL_SESSION['move_code']) > 0 ? $LOCAL_SESSION['move_code'] : '');
	
	// マスタデータ、キーの名称は $select_columns の type=enum の場合はそれに合わせる、それ以外はすきに
	$uservars = user_vars_load();
	$master = array(
	);
	
	$master['stash_type'] = array(
		'1'  => 'カード', 
		'2'  => 'アイテム', 
		'3'  => '金', 
		//'4'  => '経験値', 
		'5'  => '課金通貨', 
		'6'  => 'フレンドポイント', 
		//'7'  => '装備品', 
		//'8'  => '素材', 
		//'9'  => '研究ポイント', 
		'10' => '第二ゲーム内通貨', 
	);
	
	$error_message = (isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '');
	
	// マップマスタ
	$maps = \Mag\Res::load('map');
	$map_id = (isset($maps[$LOCAL_SESSION['map_id']]) ? $LOCAL_SESSION['map_id'] : '');
	
	// カードマスタ
	$card_master = \Mag\Res::load('card');
	
	// アイテムマスタ
	$item_master = \Mag\Res::load('item');
	
	// イベントマスタ
	$event_master = \Mag\Master\Event::factory()->load()->getArray();
	
	// ミッションマスタ
	$mission_master = \Mag\Res::load('mission');
	
	////////////////////////////////////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// 参照・編集対象の指定
	if (isset($_GET['f1_submit']) and preg_match('/^[0-9]+$/', $_GET['player_id'])) {
		$player_id = $_GET['player_id'];
		$LOCAL_SESSION['player_id'] = $player_id;
		
		parse_str($_SERVER['QUERY_STRING'], $qs);
		$l = sprintf("index.php?menukey=%s&include_file=%s", $qs['menukey'], $qs['include_file']);
		
		header("Location: $l");
		return;
	}
	if (isset($_POST['f1_submit'])) {
		
		$player_id = $_POST['player_id'];
		
		// アカウント指定からの場合
		if (! $_POST['player_id'] and $_POST['account']) {
			$player_id = \Mag\Storage\Account::factory($_POST['account'])->loadAccount($_POST['account'])->get('player_id');
			if ($player_id) {
				$LOCAL_SESSION['account'] = $_POST['account'];
			}
		} elseif (! $_POST['player_id'] and $_POST['move_code']) {
			// 引き継ぎコード
			$o = \Mag\Storage\MoveCode::factory(0)->loadMoveCode($_POST['move_code']);
			if ($o->isLoaded()) {
				$player_id = $o->get('player_id');
				$LOCAL_SESSION['move_code'] = $_POST['move_code'];
			}
		} elseif (! $_POST['player_id'] and $_POST['player_code']) {
			// プレイヤーコード
			$o = \Mag\Storage\InviteCode::factory(0)->loadInviteCode($_POST['player_code']);
			if ($o->isLoaded()) {
				$player_id = $o->get('player_id');
				$LOCAL_SESSION['player_code'] = $_POST['player_code'];
			}
		}
		
		if ($player_id > 0) {
			$LOCAL_SESSION['player_id'] = $player_id;
		}
		
		parse_str($_SERVER['QUERY_STRING'], $qs);
		$l = sprintf("index.php?menukey=%s&include_file=%s", $qs['menukey'], $qs['include_file']);
		
		header("Location: $l");
		return;
	}
	
	
	// 各種プレイヤー情報の更新
	if (isset($_POST['f2_submit'])) {
		
		$player_id = $_POST['player_id'];
		$ret = array(
			'result' => 0, 
		);
		
		try {
			if ($_POST['f2_submit'] == 'card') {
				
				// カード情報の更新
				$player_card_cols = array(
					'card_serial','level','card_exp','bild','is_ev','bonus_hp','bonus_atk','bonus_heal','is_lock'
				);
				
				if (is_array($_POST['updates']) and sizeof($_POST['updates']) > 0) {
					foreach ($_POST['updates'] as $key) {
						$card = $_POST['card'][$key];
						$player_card = \Mag\Storage\PlayerCard::factory($player_id, 1)->loadSlot($player_id, $card['card_slot']);
						foreach ($player_card_cols as $col) {
							$player_card->set($col, $card[$col]);
						}
						$player_card->save()->unloadCurrent();
					}
					\Mag\DB::commitStack();
				}
				
				if (is_array($_POST['deletebranch']) and sizeof($_POST['deletebranch']) > 0) {
					foreach ($_POST['deletebranch'] as $card_slot) {
						\Mag\Storage\PlayerCardBranch::factory($player_id, 1)->loadSlot($player_id, $card_slot)->delete();
					}
					\Mag\DB::commitStack();
				}
				
			} elseif ($_POST['f2_submit'] == 'player_deck') {
				
				if (is_array($_POST['deck']) and sizeof($_POST['deck']) > 0 and isset($_POST['deck_id'])) {
					$player_deck = \Mag\Storage\PlayerDeck::factory($player_id, $_POST['deck_id'], 1);
					foreach ($_POST['deck'] as $pos => $slot) {
						$player_deck->setCard($pos, $slot);
					}
					$player_deck->save();
					\Mag\DB::commitStack();
				}
				
			} elseif ($_POST['f2_submit'] == 'player_quest') {
				
				// クエスト情報の更新
				if (is_array($_POST['quest']) and sizeof($_POST['quest']) > 0 and isset($maps[$_POST['map_id']])) {
					$player_quest = \Mag\Storage\PlayerQuest::factory($player_id, 1)->loadMap($player_id, $_POST['map_id']);
					foreach ($_POST['quest'] as $quest_id => $q) {
						$player_quest->setStep($quest_id, intval($q['s']));
						$player_quest->setClear($quest_id, ($q['c'] ? 1 : 0), ($q['d']?:''));
						$player_quest->setComp($quest_id, ((isset($q['a']) and is_array($q['a'])) ? array_keys($q['a']) : array()));
						$player_quest->setAllComp($quest_id, intval($q['all']));
					}
					$player_quest->save();
					\Mag\DB::commitStack();
				}
				
			} elseif ($_POST['f2_submit'] == 'player_game') {
				
				// プレイヤー情報の更新
				
				if (is_array($_POST['player_game']) and sizeof($_POST['player_game']) > 0) {
					
					$player_game = \Mag\Storage\PlayerGame::factory($player_id, 1)->load($player_id);
					foreach ($_POST['player_game'] as $key => $val) {
						$player_game->set($key, $val);
					}
\Mag\Log::d('pre save() : ' . __LINE__);
					$player_game->save();
\Mag\Log::d('pre commitStack() : ' . __LINE__);
					\Mag\DB::commitStack();
\Mag\Log::d('after commitStack() : ' . __LINE__);
				}
			} elseif ($_POST['f2_submit'] == 'move_code') {
				
				// 引き継ぎ情報の更新
				
				if (is_array($_POST['move_code']) and sizeof($_POST['move_code']) > 0) {
					
					$move_code = \Mag\Storage\MoveCode::factory(0, 1)->load($player_id);
					$move_code->set('move_code', $_POST['move_code']['move_code']);
					if ($_POST['move_code']['password']) {
						$move_code->setPassword($_POST['move_code']['password']);
					}
					$move_code->save();
					\Mag\DB::commitStack();
				}
			} elseif ($_POST['f2_submit'] == 'player_birthmon') {
				
				// 誕生月
				if (is_array($_POST['player_birthmon']) and sizeof($_POST['player_birthmon']) > 0) {
					$birthmon = $_POST['player_birthmon']['birthmon'];
					if (preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $birthmon)) {
						$player_birthmon = \Mag\Storage\PlayerBirthmon::factory($player_id, 1)->load($player_id);
						if ($player_birthmon->isLoad()) {
							$sql = sprintf("UPDATE player_birthmon SET birthmon = '%s' WHERE player_id = %d", $birthmon, $player_id);
							$res = $player_birthmon->getDB()->exec($sql);
						} else {
							$player_birthmon->create($player_id, $birthmon);
						}
						\Mag\DB::commitStack();
					}
				}
				
			} elseif ($_POST['f2_submit'] == 'player_otetsudai') {
				
				// おてつだい情報の更新
				
				if (is_array($_POST['player_otetsudai']) and sizeof($_POST['player_otetsudai']) > 0) {
					
					$player_otetsudai = \Mag\Storage\PlayerOtetsudai::factory($player_id, 1)->load($player_id);
					foreach ($_POST['player_otetsudai'] as $key => $val) {
						$player_otetsudai->set($key, $val);
					}
					$player_otetsudai->save();
					\Mag\DB::commitStack();
				}
			} elseif ($_POST['f2_submit'] == 'player_gacha_first_bonus') {
				
				// ガチャ優遇情報の更新
				if (is_array($_POST['player_gacha_first_bonus']) and sizeof($_POST['player_gacha_first_bonus']) > 0) {
					foreach ($_POST['player_gacha_first_bonus'] as $first_bonus_group => $rec) {
						$player_gacha_first_bonus = \Mag\Storage\PlayerGachaFirstBonus::factory($player_id, $first_bonus_group, 1);
						$player_gacha_first_bonus->set('lot_count', intval($rec['lot_count']));
						$player_gacha_first_bonus->save();
					}
					\Mag\DB::commitStack();
				}
				
			} elseif ($_POST['f2_submit'] == 'friend_point') {
				// フレンドポイントの更新
				if (is_array($_POST['friend_point']) and sizeof($_POST['friend_point']) > 0) {
					$friend_point = \Mag\Storage\FriendPoint::factory($player_id, 1);
					$friend_point->setPoint($_POST['friend_point']['point']);
					\Mag\DB::commitStack();
				}
			} elseif ($_POST['f2_submit'] == 'player_present_box') {
				
				// プレゼントボックスから削除
				if (is_array($_POST['deletes']) and sizeof($_POST['deletes']) > 0) {
					
					/* このへん削除ログ
					$presents = $player_present_box->get($_POST['deletes']);
					$delete_ides = array();
					foreach ($presents as $rec) {
						if ($rec['type'] == \Mag\Storage\PlayerStash::TYPE_CARD) {
							\Mag\Log\ItemAct::delCard(\Mag\Log\ItemAct::ACTION_DEL_ADMIN, $rec['item'], '');
						} elseif ($rec['type'] == \Mag\Storage\PlayerStash::TYPE_ITEM) {
							\Mag\Log\ItemAct::subItem(\Mag\Log\ItemAct::ACTION_DEL_ADMIN, $rec['item'], $rec['amt']);
						} elseif ($rec['type'] == \Mag\Storage\PlayerStash::TYPE_EQUIP) {
							\Mag\Log\ItemAct::delEquip(\Mag\Log\ItemAct::ACTION_DEL_ADMIN, $rec['item'], '');
						} elseif ($rec['type'] == \Mag\Storage\PlayerStash::TYPE_MAT) {
							\Mag\Log\ItemAct::subMat(\Mag\Log\ItemAct::ACTION_DEL_ADMIN, $rec['item'], $rec['amt']);
						}
						$delete_ides[] = $rec['id'];
					}
					*/
					
					$player_present_box = \Mag\Storage\PlayerPresentBox::factory($player_id, 1)->deleteIdes($_POST['deletes']);
					
					\Mag\DB::commitStack();
				}
				
			} elseif ($_POST['f2_submit'] == 'player_present_box_create') {
				
				// プレゼントボックスへの格納
				\Mag\Page\Work\PresentBox::factory($player_id)->setPlayerId($player_id)->push(array($_POST['presents']), $_POST['comment']);
\Mag\Log::d(__LINE__);
				/*
				$player_present_box = \Mag\Storage\PlayerStash::factory($player_id, 1);
				$player_present_box->create($_POST['presents'], $_POST['comment'], $_POST['expire']);
				$player_present_box->save();
				*/
				\Mag\DB::commitStack();
				
			} elseif ($_POST['f2_submit'] == 'raid_status') {
				
				// レイドボス進捗の更新
				$raid_status = \Mag\Storage\RaidStatus::factory($player_id, $_POST['raidboss_id'], 1);
				$raid_status->set('next_level', $_POST['raid_status']['next_level']);
				$raid_status->save();
				\Mag\DB::commitStack();
			} elseif ($_POST['f2_submit'] == 'player_item') {
				
				// プレイヤーアイテム
				$player_id = $_POST['player_id'];
				$pi = $_POST['player_item'];
				$pii = $_POST['player_item_insert'];
				if ($pi or ($pii and $pii['id'] > 0 and $pii['qty'] > 0)) {
					$player_item = \Mag\Storage\PlayerItem::factory($player_id, 1)->load($player_id);
					if ($pi) {
						foreach ($pi as $id => $a) {
							if ($player_item->seek(array('id' => $id))->valid()) {
								$player_item
									->set('item_id', $a['item_id'])
									->set('item_qty', $a['item_qty'])
									->set('expire', $a['expire'])
									->save()
								;
							}
						}
					}
					if ($pii and $pii['id'] > 0 and $pii['qty'] > 0) {
						$player_item->create($player_id, $pii['id'], $pii['qty'], $pii['expire']);
					}
					\Mag\DB::commitStack();
				}
				
			} elseif ($_POST['f2_submit'] == 'friend') {
				
				
				// TOOD: 最終助っ人日時の更新
				
				
				// TOOD: フレンド関係の削除
				
				
			} elseif ($_POST['f2_submit'] == 'friend_request') {
				
				// 
				if ($_POST['delete'] and is_array($_POST['delete'])) {
					$friend_request = \Mag\Storage\FriendRequest::factory($player_id, 1);
					foreach ($_POST['delete'] as $request_player_id) {
						$friend_request->delete($player_id, $request_player_id);
					}
					\Mag\DB::commitStack();
				}
				
			} elseif ($_POST['f2_submit'] == 'event_point') {
				
				$event_id = $_POST['event_id'];
				$player_id = $_POST['player_id'];
				$point = $_POST['point'];
				$last_point = $_POST['last_point'];
				
				// イベントポイント
				$event_point = \Mag\Storage\EventPoint::factory($event_id, 1)->loadEvent($event_id, $player_id);
				if ($event_point->isLoaded()) {
					$event_point->set('point', $point)->set('last_point', $last_point)->save();
				} else {
					$event_point->create($event_id, $player_id, $point);
				}
				\Mag\DB::commitStack();
			} elseif ($_POST['f2_submit'] == 'player_mission') {
				$player_id = $_POST['player_id'];
				
				do {
					if (! is_array($_POST['player_mission'])) {
						break;
					}
					
					// 期待する変更後のものでまとめ
					$missions = array(
						'clear' => array(), 
						'notify' => array(), 
						'none' => array(), 
					);
					foreach ($_POST['player_mission'] as $mission_id => $state) {
						if (isset($missions[$state])) {
							$missions[$state][] = $mission_id;
						} else {
							break 2;
						}
					}
					
					// 現状読み込み
					$player_mission =  \Mag\Storage\PlayerMission::factory($player_id, 1)->load($player_id);
					$player_mission_notify =  \Mag\Storage\PlayerMissionNotify::factory($player_id, 1)->load($player_id);
					
					// 現状と違うものを抽出
					$updates = array(
						'create' => array(
							'clear' => array(), 
							'notify' => array(), 
						), 
						'delete' => array(
							'clear' => array(), 
							'notify' => array(), 
						), 
					);
					foreach ($missions['clear'] as $mission_id) {
						if (! $player_mission->select(array('mission_id'=>$mission_id))) {
							$updates['create']['clear'][] = $mission_id;
						}
						if ($player_mission_notify->select(array('mission_id'=>$mission_id))) {
							$updates['delete']['notify'][] = $mission_id;
						}
					}
					foreach ($missions['notify'] as $mission_id) {
						if (! $player_mission_notify->select(array('mission_id'=>$mission_id))) {
							$updates['create']['notify'][] = $mission_id;
						}
						if ($player_mission->select(array('mission_id'=>$mission_id))) {
							$updates['delete']['clear'][] = $mission_id;
						}
					}
					foreach ($missions['none'] as $mission_id) {
						if ($player_mission_notify->select(array('mission_id'=>$mission_id))) {
							$updates['delete']['notify'][] = $mission_id;
						}
						if ($player_mission->select(array('mission_id'=>$mission_id))) {
							$updates['delete']['clear'][] = $mission_id;
						}
					}
					
//\Mag\Log::d(__LINE__);
					// それらの適用
					if ($updates['create']['clear']) {
//\Mag\Log::d(array(__LINE__, $player_id, $updates['create']['clear']));
						$player_mission->creates($player_id, $updates['create']['clear']);
//\Mag\Log::d(__LINE__);
					}
					if ($updates['create']['notify']) {
//\Mag\Log::d(array(__LINE__, $player_id, $updates['create']['notify']));
						$player_mission_notify->creates($player_id, $updates['create']['notify']);
//\Mag\Log::d(__LINE__);
					}
					if ($updates['delete']['clear']) {
//\Mag\Log::d(array(__LINE__, $player_id, $updates['delete']['clear']));
						$player_mission->deleteMissionIdes($updates['delete']['clear']);
//\Mag\Log::d(__LINE__);
					}
					if ($updates['delete']['notify']) {
//\Mag\Log::d(array(__LINE__, $player_id, $updates['delete']['notify']));
						$player_mission_notify->deleteMissionIdes($updates['delete']['notify']);
//\Mag\Log::d(__LINE__);
					}
					\Mag\DB::commitStack();
					
				} while (0);
				
			}
		} catch (Exception $e) {
\Mag\Log::d($e);
mylog(sprintf('%s:%d > %s', basename(__FILE__), __LINE__, $e->getMessage()));
			$ret['result'] = -1;
		}
		
		if (isset($_GET['api'])) {
			header('Content-Type:application/json');
			echo json_encode($ret);
			exit;
		}
		
	}
	
	
	// Vue系データ取得
	if (isset($_GET['json']) and isset($_POST['player_id'])) {
		$json = array();
		
		if ($_GET['json'] == 'quest') {
			$map_id = $_POST['map_id'];
			
			$comp_nos = array("c1" => false, "c2" => false, "c3" => false, "c4" => false);
			
			$quest_master = \Mag\Master\Quest::factory()->load(array('map_id' => $map_id));
			$player_quest = \Mag\Storage\PlayerQuest::factory($player_id)->loadMap($player_id, $map_id);
			
			$json['map_id'] = $map_id;
			$json['quests'] = array();
			while ($quest_master->valid()) {
				$quest_id = $quest_master->key();
				
				$j = array(
					'quest_id' => $quest_id, 
					'is_clear' => false, 
					'step' => 0, 
					'clear_date' => '', 
					'allcomp' => false, 
					'comp' => $comp_nos, 
				);
				
				if ($player_quest->isLoad()) {
					$j['is_clear']   = ($player_quest->isClear($quest_id) ? true : false);
					$j['step']       = $player_quest->getStep($quest_id);
					$j['clear_date'] = $player_quest->getClearDate($quest_id);
					$j['allcomp']    = ($player_quest->isAllComp($quest_id) ? true : false);
					
					foreach (array_keys($comp_nos) as $n) {
						$j['comp']["{$n}"] = ($player_quest->isComp($quest_id, intval(substr($n,1))) ? true : false);
					}
				}
				
				$json['quests'][] = $j;
				
				$quest_master->next();
			}
			
		} elseif ($_GET['json'] == 'event_point') {
			
			$event_id = $_POST['event_id'];
			
			$json = array(
				'point' => 0, 
				'last_point' => '', 
				'recved_reward' => '', 
				'recv_result' => 0, 
			);
			
			$event_point_storage = \Mag\Storage\EventPoint::factory($event_id)->loadEvent($event_id, $player_id);
			if ($event_point_storage->isLoad()) {
				$json['point'] = $event_point_storage->get('point');
				$json['last_point'] = $event_point_storage->get('last_point');
			}
			
			// ついでに報酬受け取り状況の確認
			$player_event_reward = \Mag\Storage\PlayerEventReward::factory($player_id)->loadEvent($player_id, $event_id);
			if ($player_event_reward->isLoad()) {
				$json['recved_reward'] = implode(', ', $player_event_reward->get('state'));
				$json['recv_result'] = $player_event_reward->get('recv_result');
			}
			
		} else {
			
		}
		
		header('Content-Type:application/json');
		echo json_encode($json);
		exit;
	}
	
	
	if ($_POST) {
		return;
	}
	else {
		unset($_SESSION['error_message']);
		unset($_SESSION['__mylog']);
	}
	
	$psmarty->assign('error_message', $error_message);
	$psmarty->assign('master', $master);
	
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、API 的な動作を行うあたり
	if ($_GET['json']) {
		
		$json = array();
		if (isset($_GET['player_id'])) {
			$player_id = intval($_GET['player_id']);
		}
		
		if ($_GET['json'] == 'launchcount') {
			
			// 最近の起動回数の計上
			$days = 30;
			$tpl = "SELECT date(log_date) as d, count(*) as cnt FROM lt_launch WHERE user_id = %d AND log_date > subdate(now(), INTERVAL %d DAY) GROUP BY d ORDER BY d";
			$sql = sprintf($tpl, $player_id, $days);
			$arr = db_select($con, $sql);
			
			// 戻り値は隙間があるのでそれを埋める
			$lauchcount = array();
			$ts = time();
			$dates = array();
			for ($i = 0;$i < $days;$i++) {
				$dates[date('Y-m-d', $ts - (60*60*24*$i))] = 0;
			}
			foreach ($arr as $rec) {
				$dates[$rec['d']] = $rec['cnt'];
			}
			foreach ($dates as $d => $c) {
				$lauchcount[] = array(
					'date'  => $d, 
					'count' => intval($c), 
				);
			}
			
			$json['lauchcount'] = $lauchcount;
		}
		
		header('Content-Type:application/json');
		echo json_encode($json);
		exit;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、検索・抽出条件フォーム
	
	// これを jquery 1.11 で動かすなら
	$smarty->assign('jquery_version', '1.11');
	
	$psmarty->assign('maps', $maps);
	$psmarty->assign('card_master', $card_master);
	$psmarty->assign('item_master', $item_master);
	$psmarty->assign('event_master', $event_master);
	$psmarty->assign('mission_master', $mission_master);
	
	$psmarty->assign('player_id', $player_id);
	$psmarty->assign('map_id', $map_id);
	$psmarty->assign('deck_id', $deck_id);
	
	// 検索条件が指定されていない場合はここで終わらせる
	if (! $player_id) {
		return ;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、結果
	
	// アカウント
	$accounts = \Mag\Storage\Account::getAccounts($player_id);
	$psmarty->assign('accounts', $accounts);
	
	// 課金通貨
	$shoppoint = \Mag\Storage\Shoppoint::factory($player_id)->setPlayerId($player_id);
	$psmarty->assign('shoppoint', $shoppoint->getShoppoint());
	
	// プレイヤー情報
	$player_game = \Mag\Storage\PlayerGame::factory($player_id)->load($player_id);
	$psmarty->assign('player_game', $player_game->getRec());
	
	// 誕生月
	$player_birthmon = \Mag\Storage\PlayerBirthmon::factory($player_id)->load($player_id);
	$psmarty->assign('player_birthmon', $player_birthmon->getRec());
	
	// おてつだい情報
	$player_otetsudai = \Mag\Storage\PlayerOtetsudai::factory($player_id)->load($player_id);
	$psmarty->assign('player_otetsudai', ($player_otetsudai->getRec()?:array('birthmon'=>'')));
	
/*
	// ガチャ初回優遇情報
	$gacha_first_bonus_master = \Mag\Res::load('gacha_first_bonus');
	if ($gacha_first_bonus_master) {
		$player_gacha_first_bonuses = array();
		foreach ($gacha_first_bonus_master as $rec) {
			if ($rec['first_bonus_group']) {
				$player_gacha_first_bonus = \Mag\Storage\PlayerGachaFirstBonus::factory($player_id, $rec['first_bonus_group'], 0);
				$player_gacha_first_bonuses[$rec['first_bonus_group']] = $player_gacha_first_bonus->getRec();
			}
		}
	}
	$psmarty->assign('player_gacha_first_bonuses', $player_gacha_first_bonuses);
*/
	
	// フレンドポイント
	$friend_point = \Mag\Storage\FriendPoint::factory($player_id);
	$psmarty->assign('friend_point', $friend_point->getRec());
	
	// 所持カード情報
	$player_card = \Mag\Storage\PlayerCard::factory($player_id)->load($player_id);
	$psmarty->assign('player_cards', $player_card->getArray());
	$slot2card = array();
	foreach ($player_card->getArray() as $card) {
		$slot2card[$card['card_slot']] = $card['card_id'];
	}
	$psmarty->assign('slot2card', $slot2card);
	
	// デッキ装着情報
	$player_deck = \Mag\Storage\PlayerDeck::factory($player_id)->load($player_id);
	$deck_display_cols = array("card_slot_01","card_slot_02","card_slot_03","card_slot_04","card_slot_05");
	$psmarty->assign('deck', $player_deck->getArray());
	$psmarty->assign('deck_display_cols', $deck_display_cols);
	
	// アイテム
	$player_item = \Mag\Storage\PlayerItem::factory($player_id)->load($player_id);
	$psmarty->assign('player_items', $player_item->getArray());
	
	// プレゼントボックス
	$player_present_box = \Mag\Storage\PlayerPresentBox::factory($player_id)->load($player_id);
	$psmarty->assign('player_present_box', $player_present_box->getArray());
	$player_present_box_history = \Mag\Storage\PlayerPresentBoxHistory::factory($player_id)->load($player_id);
	$psmarty->assign('player_present_box_history', $player_present_box_history->getArray());
	
/*
	// クエスト
	if ($map_id > 0) {
		$quest_master = \Mag\Res::load('quest', array('map_id' => $map_id));
		$psmarty->assign('quest_master', $quest_master);
		
		$quests = array();
		$player_quest = \Mag\Storage\PlayerQuest::factory($player_id, $map_id);
		if ($player_quest->isLoaded()) {
			foreach ($quest_master as $quest_id => $q) {
				$quests[$quest_id] = array(
					's' => $player_quest->getStep($quest_id), 
					'c' => $player_quest->isClear($quest_id), 
					'd' => $player_quest->getClearDate($quest_id), 
				);
			}
		}
		$psmarty->assign('quests', $quests);
	} else {
		$psmarty->assign('quest_master', array());
		$psmarty->assign('quests', array());
	}
	
	// レイド
	if ($raidboss_id > 0) {
		$raid_status = \Mag\Storage\RaidStatus::factory($player_id, $raidboss_id);
		$psmarty->assign('raid_status', $raid_status->getRec());
	} else {
		$psmarty->assign('raid_status', array());
	}
	
	// イベント
	if ($event_id > 0) {
		$event_point = \Mag\Storage\EventPoint::factory($event_id, $player_id);
		$psmarty->assign('event_point', $event_point->get('point'));
		
		$player_event_point = \Mag\Storage\PlayerEventPoint::factory($player_id, $event_id);
		$psmarty->assign('player_event_point', $player_event_point->getRec());
		
		$event_reward_master = \Mag\Res::load('event_reward', array('event_id' => $event_id));
		$psmarty->assign('event_reward_master', $event_reward_master);
		
		$player_event_reward = \Mag\Storage\PlayerEventReward::factory($player_id, $event_id);
		if ($player_event_reward->getRecvs()) {
			$psmarty->assign('event_rewards', array_combine($player_event_reward->getRecvs(), array_fill(0, sizeof($player_event_reward->getRecvs()), 1)));
		} else {
			$psmarty->assign('event_rewards', array());
		}
		$psmarty->assign('is_result_recv', $player_event_reward->get('is_result_recv'));
		
	} else {
		$psmarty->assign('event_point', 0);
		$psmarty->assign('event_rewards', array());
		$psmarty->assign('event_reward_master', array());
	}
	
	// ガチャ購入済み情報
	$player_buylist_gacha = \Mag\Storage\PlayerBuylistGacha::factory($player_id);
	$psmarty->assign('player_buylist_gacha', $player_buylist_gacha->getItems());
	
	// 購入済み情報
	$player_buylist = \Mag\Storage\PlayerBuylist::factory($player_id);
	$psmarty->assign('player_buylist', $player_buylist->getItems());
*/
	
	// フレンド
	$friend = \Mag\Storage\Friend::factory($player_id)->load($player_id);
	$psmarty->assign('friend', $friend->getArray());
	
	// フレンド申請
	$friend_request = \Mag\Storage\FriendRequest::factory($player_id)->load($player_id);
	$psmarty->assign('friend_request', $friend_request->getArray());
	
/*
	// イベント実績
	$player_event_achievement = \Mag\Storage\PlayerEventAchievement::factory($player_id);
	$psmarty->assign('player_event_achievement', $player_event_achievement->getRecords());
	
	// 配給品
	$supply_item_masters = \Mag\Master\SupplyItem::factory();
	$psmarty->assign('supply_item_masters', $supply_item_masters);
	$supply_item = array();
	foreach ($supply_item_masters as $id => $supply_item_master) {
		$player_supply_item = \Mag\Storage\PlayerSupplyItem::factory($player_id, $id);
		$supply_item[$id] = $player_supply_item->getRecvDate();
	}
	$psmarty->assign('supply_item', $supply_item);
*/
	
	// 招待コード
	$move_code = \Mag\Storage\MoveCode::factory(0)->load($player_id);
	$psmarty->assign('move_code', $move_code->get('move_code'));
	
	// ミッション状況
	$mission = array(
		'clear' => \Mag\Storage\PlayerMission::factory($player_id)->load($player_id)->distinct('mission_id'), 
		'notify' => \Mag\Storage\PlayerMissionNotify::factory($player_id)->load($player_id)->distinct('mission_id'), 
	);
	$psmarty->assign('mission', $mission);
	
	
	
	
	
	// 最近の起動回数の計上
	$days = 15;
	$tpl = "SELECT date(log_date) as d, count(*) as cnt FROM lt_launch WHERE user_id = %d AND log_date > subdate(now(), INTERVAL %d DAY) GROUP BY d ORDER BY d";
	$sql = sprintf($tpl, $player_id, $days);
	$arr = db_select($con, $sql);
	
	// 戻り値は隙間があるのでそれを埋める
	$lauchcount = array();
	$ts = time();
	$dates = array();
	for ($i = 0;$i < $days;$i++) {
		$dates[date('Y-m-d', $ts - (60*60*24*$i))] = 0;
	}
	if ($arr) {
		foreach ($arr as $rec) {
			$dates[$rec['d']] = $rec['cnt'];
		}
	}
	foreach ($dates as $d => $c) {
		$lauchcount[] = array(
			'date'  => $d, 
			'count' => intval($c), 
		);
	}
	$psmarty->assign('lauchcount', $lauchcount);


