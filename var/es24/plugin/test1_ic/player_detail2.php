<?PHP
//print_r($_SESSION);
//var_dump($_SESSION['__mylog']);
	
	require_once('env.php');
	$env = env();
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	
	$player_id = (preg_match('/^[0-9]{1,10}$/', $LOCAL_SESSION['player_id']) ? $LOCAL_SESSION['player_id'] : '');
	$account = (strlen($LOCAL_SESSION['account']) > 0 ? $LOCAL_SESSION['account'] : '');
	
	// マスタデータ、キーの名称は $select_columns の type=enum の場合はそれに合わせる、それ以外はすきに
	$uservars = user_vars_load();
	$master = array(
	);
	
	$master['stash_type'] = array(
		'1' => 'カード', 
		'2' => 'アイテム', 
		'3' => '金', 
		'4' => '経験値', 
		'5' => '課金通貨', 
		'6' => 'フレンドポイント', 
		'7' => '装備品', 
		'8' => '素材', 
		'9' => '研究ポイント', 
	);
	
	$error_message = (isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '');
	
	// マップマスタ
	$maps = \Mag\Res::load('map');
	$map_id = (isset($maps[$LOCAL_SESSION['map_id']]) ? $LOCAL_SESSION['map_id'] : '');
	
	// レイドボスマスタ
	$raid_bosses = \Mag\Res::load('raid_boss');
	$raidboss_id = (isset($raid_bosses[$LOCAL_SESSION['raidboss_id']]) ? $LOCAL_SESSION['raidboss_id'] : '');
	
	// カードマスタ
	$card_master = \Mag\Res::load('card');
	
	// スキルマスタ
	$skill_master = \Mag\Res::load('skill_list');
	
	// アイテムマスタ
	$item_master = \Mag\Res::load('item');
	
	// 素材マスタ
	$equip_master = \Mag\Res::load('equip');
	
	// 素材マスタ
	$mat_master = \Mag\Res::load('material');
	
	// イベントマスタ
	$event_master = \Mag\Res::load('event');
	$event_id = (isset($event_master[$LOCAL_SESSION['event_id']]) ? $LOCAL_SESSION['event_id'] : '');
	
	// ガチャマスタ
	$gacha_master = \Mag\Res::load('gacha');
	$gacha_ides = array();
	foreach ($gacha_master as $m) {
		$gacha_ides[$m['gacha_id']] = $m['gacha_id'];
	}
	
	// rank_group
	$rank_groups = array();
	foreach ($event_master as $m) {
		$rank_groups[$m['rank_group']] = $m['rank_group'];
	}
	
	// デッキ番号 (複数デッキ対応までは固定)
	$deck_id = 1;
	
	
	
	
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
			$player_id = \Mag\Storage\Account::loadPlayerId($_POST['account']);
			if ($player_id) {
				$LOCAL_SESSION['account'] = $_POST['account'];
			}
		}
		
		if ($player_id > 0) {
			$LOCAL_SESSION['player_id'] = $player_id;
			
			if (isset($_POST['map_id']) and isset($maps[$_POST['map_id']])) {
				$map_id = $LOCAL_SESSION['map_id'] = $_POST['map_id'];
			} else {
				$map_id = $LOCAL_SESSION['map_id'] = 0;
			}
			
			if (isset($_POST['raidboss_id'])) {
				$raidboss_id = $LOCAL_SESSION['raidboss_id'] = $_POST['raidboss_id'];
			} else {
				$raidboss_id = $LOCAL_SESSION['raidboss_id'] = 0;
			}
			
			if (isset($_POST['event_id'])) {
				$event_id = $LOCAL_SESSION['event_id'] = $_POST['event_id'];
			} else {
				$event_id = $LOCAL_SESSION['event_id'] = 0;
			}
		}
		
		parse_str($_SERVER['QUERY_STRING'], $qs);
		$l = sprintf("index.php?menukey=%s&include_file=%s", $qs['menukey'], $qs['include_file']);
		
		header("Location: $l");
		return;
	}
	
	// 各種プレイヤー情報の更新
	if (isset($_POST['f2_submit'])) {
		
		try {
			$player_id = intval($_POST['player_id']);
			
			if ($_POST['f2_submit'] == 'card') {
				
				// カード情報の更新
				if (is_array($_POST['updates']) and sizeof($_POST['updates']) > 0) {
					$player_card = \Mag\Storage\PlayerCard::factory($player_id, 1);
					foreach ($_POST['updates'] as $key) {
						$player_card->updateCard($_POST['card'][$key]['card_slot'], $_POST['card'][$key]);
					}
					$player_card->save();
					\Mag\DB::commitStack();
				}
			} elseif ($_POST['f2_submit'] == 'player_commu') {
				
				// こみゅ情報の更新
				
				if (is_array($_POST['updates']) and sizeof($_POST['updates']) > 0) {
					$player_commu = \Mag\Storage\PlayerCommu::factory($player_id, 0, 1);
					$player_commu->load($player_id);
					
					foreach ($_POST['updates'] as $commu_id) {
						$commu = $_POST['commu'][$commu_id];
						if ($commu_id == 'new' and ! $player_commu->getCommu($commu['commu_id'])) {
							$player_commu->create($player_id, $commu['commu_id'], $commu['ls_level_max']);
						}
						$player_commu->setCommu($commu_id, 'ls', $commu['ls']);
						$player_commu->setCommu($commu_id, 'ls_level', $commu['ls_level']);
						$player_commu->setCommu($commu_id, 'ls_level_max', $commu['ls_level_max']);
					}
					$player_commu->save();
					
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
//mylog($_POST);
				if (is_array($_POST['quests']) and sizeof($_POST['quests']) > 0 and isset($maps[$_POST['map_id']])) {
					$player_quest = \Mag\Storage\PlayerQuest::factory($player_id, $_POST['map_id'], 1);
					foreach ($_POST['quests'] as $q) {
						$quest_id = $q['quest_id'];
						$d = '';
						if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $q['d'])) {
							$d = $q['d'];
						}
						$player_quest->setStep($quest_id, intval($q['s']));
						$player_quest->setClear($quest_id, ((! $q['c'] or $q['c'] == 'false') ? 0 : 1), $d);
//mylog(array('q' => $q, 's' => intval($q['s']), 'c' => ((! $q['c'] or $q['c'] == 'false') ? 0 : 1), 'd' => $d));
					}
					if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $_POST['unlock_limit'])) {
						$player_quest->set('unlock_limit', $_POST['unlock_limit']);
					}
					$player_quest->save();
					\Mag\DB::commitStack();
				}
				
			} elseif ($_POST['f2_submit'] == 'player_story') {
				
				// ストーリー情報の更新
				if (is_array($_POST['story']) and sizeof($_POST['story']) > 0 and isset($maps[$_POST['map_id']])) {
					$player_story = \Mag\Storage\PlayerStory::factory($player_id, $_POST['map_id'], 1);
					foreach ($_POST['story'] as $story_id => $q) {
						$player_story->start($story_id, ($q['s'] ? 1 : 0));
						$player_story->end($story_id, ($q['e'] ? 1 : 0));
					}
					$player_story->save();
					\Mag\DB::commitStack();
				}
				
				\Mag\DB::commitStack();
			} elseif ($_POST['f2_submit'] == 'raid_status') {
				
				// レイドボス進捗の更新
				$raid_status = \Mag\Storage\RaidStatus::factory($player_id, $_POST['raidboss_id'], 1);
				$raid_status->set('next_level', $_POST['raid_status']['next_level']);
				$raid_status->save();
				\Mag\DB::commitStack();
			} elseif ($_POST['f2_submit'] == 'player_event_point') {
				
				// プレイヤーイベントポイント
				$event_id = intval($_POST['event_id']);
				if ($event_id > 0) {
					$player_event_point = \Mag\Storage\PlayerEventPoint::factory($player_id, $event_id, 1);
					$player_event_point->set('total_point', $_POST['total_point']);
					$player_event_point->set('left_point',  $_POST['left_point'] );
					$player_event_point->set('reward_point', $_POST['reward_point']);
					$player_event_point->set('reward_left',  $_POST['reward_left'] );
					$player_event_point->save();
					\Mag\DB::commitStack();
				}
			} elseif ($_POST['f2_submit'] == 'player_battle_rank') {
				
				// プレイヤー対戦ランク
				$rank_group = intval($_POST['rank_group']);
				if ($rank_group > 0) {
					$player_battle_rank = \Mag\Storage\PlayerBattleRank::factory($player_id, $rank_group, 1);
					$player_battle_rank->set('rank',         $_POST['rank']         );
					$player_battle_rank->set('win',          $_POST['win']          );
					$player_battle_rank->set('lose',         $_POST['lose']         );
					$player_battle_rank->set('draw',         $_POST['draw']         );
					$player_battle_rank->set('leave',        $_POST['leave']        );
					$player_battle_rank->set('rensyou',      $_POST['rensyou']      );
					$player_battle_rank->set('renpai',       $_POST['renpai']       );
					$player_battle_rank->set('rollback_win', $_POST['rollback_win'] );
					$player_battle_rank->save();
					\Mag\DB::commitStack();
				}
			} elseif ($_POST['f2_submit'] == 'event_point') {
				
				// イベントポイント
				$event_id = intval($_POST['event_id']);
				if ($event_id > 0) {
					$event_point = \Mag\Storage\EventPoint::factory($event_id, $player_id, 1);
					$event_point->set('point', $_POST['point']);
					$event_point->set('pure_point', $_POST['pure_point']);
					$event_point->set('last_point', $_POST['last_point']);
					$event_point->save();
					\Mag\DB::commitStack();
				}
//				$event_point = \Mag\Storage\EventPoint::factory($event_id, $player_id, 1);
//				$event_point->set('point', $_POST['event_point']['point']);
//				$event_point->save();
//				\Mag\DB::commitStack();
				
			} elseif ($_POST['f2_submit'] == 'player_event_reward') {
				
				// イベント報酬
				$event_id = $_POST['event_id'];
				$event_reward_master = \Mag\Res::load('event_reward', array('event_id' => $event_id));
				$player_event_reward = \Mag\Storage\PlayerEventReward::factory($player_id, $event_id);
				
				$player_event_reward->setResultRecv($player_id, $event_id, ($_POST['is_result_recv'] > 0 ? 1 : 0));
				$state = array();
				foreach ($event_reward_master as $event_reward_id => $m) {
					if ($player_event_reward->isRecv($event_reward_id)) {
						$player_event_reward->recv($event_reward_id, 0);
					}
				}
				if (is_array($_POST['state']) and sizeof($_POST['state']) > 0 ) {
					foreach ($_POST['state'] as $event_reward_id) {
						if (isset($event_reward_master[$event_reward_id]) ) {
							$player_event_reward->recv($event_reward_id, 1);
						}
					}
				}
				
				$player_event_reward->save();
				\Mag\DB::commitStack();
				
			} elseif ($_POST['f2_submit'] == 'player_boost_personal') {
				
				// 個別イベントポイント倍率変動イベント
				$player_boost_personal = \Mag\Storage\PlayerBoostPersonal::factory($player_id, $_POST['event_id'], 1);
				
				$player_boost_personal->create($player_id, $_POST['event_id'], $_POST['expire'], $_POST['point_boost']);
				\Mag\DB::commitStack();
				
			} elseif ($_POST['f2_submit'] == 'player_gacha_step') {
				
				// ガチャ進捗
				$player_gacha_step = \Mag\Storage\PlayerGachaStep::factory($player_id, $_POST['gacha_id'], 1);
				
				$player_gacha_step->set('step', $_POST['step']);
				$player_gacha_step->set('total_step', $_POST['total_step']);
				
				$player_gacha_step->save();
				\Mag\DB::commitStack();
				
			}
		} catch (Exception $e) {
mylog(sprintf('%s:%d > %s', basename(__FILE__), __LINE__, $e->getMessage()));
			;
		}
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、JSON による要求
	if (isset($_GET['json'])) {
		header('Content-Type: application/json');
		$output = array();
		$player_id = $_POST['player_id'];
		
		if ($_GET['json'] == 'player_quset') {
			$map_id = $_POST['map_id'];
			$quests = array();
			$player_quest = \Mag\Storage\PlayerQuest::factory($player_id, $map_id);
			$output['unlock_limit'] = $player_quest->getUnlockLimit();
			
			$output['quests'] = array();
			$quest_master = \Mag\Res::load('quest', array('map_id' => $map_id));
			
			foreach ($quest_master as $quest_id => $q) {
				if ($player_quest->isLoaded()) {
					$output['quests'][] = array(
						'quest_id' => $quest_id, 
						's' => $player_quest->getStep($quest_id), 
						'c' => ($player_quest->isClear($quest_id) ? true : false), 
						'd' => ($player_quest->getClearDate($quest_id)?:''), 
					);
				} else {
					$output['quests'][] = array(
						'quest_id' => $quest_id, 
						's' => 0, 
						'c' => false, 
						'd' => '', 
					);
				}
			}
		} elseif ($_GET['json'] == 'player_event_point') {
			$event_id = $_POST['event_id'];
			$player_event_point = \Mag\Storage\PlayerEventPoint::factory($player_id, $event_id);
			$cols = array(
				'rank',         
				'total_point',  
				'left_point',   
				'reward_point',  
				'reward_left',   
			);
			foreach ($cols as $col) {
				$output[$col] = intval($player_event_point->get($col));
			}
		} elseif ($_GET['json'] == 'player_battle_rank') {
			$rank_group = $_POST['rank_group'];
			$player_battle_rank = \Mag\Storage\PlayerBattleRank::factory($player_id, $rank_group);
			$cols = array(
				'rank',         
				'total_win',    
				'win',          
				'lose',         
				'draw',         
				'leave',        
				'rensyou',      
				'renpai',       
				'rollback_win', 
			);
			foreach ($cols as $col) {
				$output[$col] = intval($player_battle_rank->get($col));
			}
		} elseif ($_GET['json'] == 'event_point') {
			$event_id = $_POST['event_id'];
			$player_event_point = \Mag\Storage\EventPoint::factory($event_id, $player_id);
			$cols = array(
				'point', 
				'pure_point', 
				'last_point', 
			);
			foreach ($cols as $col) {
				$output[$col] = $player_event_point->get($col);
			}
		} elseif ($_GET['json'] == 'player_boost_personal') {
			$event_id = $_POST['event_id'];
			$player_boost_personal = \Mag\Storage\PlayerBoostPersonal::factory($player_id, $event_id);
			$cols = array(
				'expire', 
				'point_boost', 
			);
			foreach ($cols as $col) {
				$output[$col] = $player_boost_personal->get($col);
			}
		} elseif ($_GET['json'] == 'player_gacha_step') {
			$gacha_id = $_POST['gacha_id'];
			$player_gacha_step = \Mag\Storage\PlayerGachaStep::factory($player_id, $gacha_id);
			$cols = array(
				'gacha_id', 
				'step', 
				'total_step', 
			);
			foreach ($cols as $col) {
				$output[$col] = $player_gacha_step->get($col);
			}
		} elseif ($_GET['json'] == 'player_event_reward') {
			$event_id = $_POST['event_id'];
			$player_event_reward = \Mag\Storage\PlayerEventReward::factory($player_id, $event_id);
			$output = array(
				'is_result_recv' => ($player_event_reward->get('is_result_recv') ? true : false), 
				'state' => array(
					// array('event_reward_id' => event_reward_id, 'is_recv' => is_recv), ...
				), 
			);
			$event_reward_master = \Mag\Res::load('event_reward', array('event_id' => $event_id));
			if (sizeof(event_reward_master) > 0) {
				$trc = '1';
				foreach ($event_reward_master as $event_reward_id => $m) {
					$output['state'][$event_reward_id] = array(
						'event_reward_id' => $event_reward_id,
						'is_recv' => ($player_event_reward->isRecv($event_reward_id) ? true : false), 
						'trc' => 'tr' . $trc, 
					);
					$trc = ($trc == 1 ? 2 : 1);
				}
			}
		}
		
		echo json_encode($output);
		exit;
	}
	
	if ($_POST) {
		return;
	}
	else {
		unset($_SESSION['error_message']);
		unset($_SESSION['__mylog']);
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成
	
	$psmarty->assign('error_message', $error_message);
	$psmarty->assign('master', $master);
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、検索・抽出条件フォーム
	
	$psmarty->assign('maps', $maps);
	$psmarty->assign('skill_master', $skill_master);
	$psmarty->assign('card_master', $card_master);
	$psmarty->assign('item_master', $item_master);
	$psmarty->assign('equip_master', $equip_master);
	$psmarty->assign('mat_master', $mat_master);
	$psmarty->assign('event_master', $event_master);
	$psmarty->assign('gacha_master', $gacha_master);
	$psmarty->assign('gacha_ides', $gacha_ides);
	$psmarty->assign('rank_groups', $rank_groups);
	
	$psmarty->assign('player_id', $player_id);
	
	// 検索条件が指定されていない場合はここで終わらせる
	if (! $player_id) {
		return ;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、各種初期値とか
	
	// アカウント
	$accounts = \Mag\Storage\Account::getAccounts($player_id);
	$psmarty->assign('accounts', $accounts);
	