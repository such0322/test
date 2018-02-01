

<script type='text/javascript' src="js/vue.js"></script>

<script type='text/javascript'>
<!--

Vue.config.delimiters = ['${', '}'];
Vue.config.unsafeDelimiters = ['{!!', '!!}'];

var vue_objects = {};
var player = {
	quest:{
		map_id:0, 
		quests:[], 
		result:""
	}, 
	event_point:{
		point:0,
		last_point:"",
		recved_reward:""
	}
};

$(function(){

	$("select.vue_trigger").each(function(){
		
		var $this = $(this);
		var label = $this.attr("data-label");
		var vuel = $this.attr("data-vuel");
		
		// Vue のバインド
		vue_objects[label] = new Vue({
			el: "#" + vuel, 
			data:player[label]
		});
		
		// 読み込み
		$this.change(function(){
			var n = $this.attr("name");
			var postdata = {
				player_id : {{$player_id}}, 
				//$this.attr("name") : $(this).children("option:selected").val()
			};
			postdata[n] = parseInt($this.children("option:selected").val());
			if (postdata[n] > 0) {
				$.post("inline.php?menukey={{$menukey}}&include_file=plugin/{{$menukey}}/player_detail.php&v=1&json=" + label, postdata, function(data){
					for (var k in data) {
						var v = data[k];
						if (Array.isArray(v)) {
							for (var i = 0;i < v.length;i++) {
								v[i].trc = "tr" + (i%2+1);
							}
						}
						player[label][k] = v;
					}
				}, 'json');
				$("#" + vuel).show();
			} else {
				$("#" + vuel).hide();
			}
		});
		
		var $form = $this.closest("form");
		$form.submit(function(){
			if (confirm("上記内容で更新します、よろしいですか？")) {
				
				var postdata = $form.serializeArray();
				
				player[label]["result"] = "送信中 ...";
				$(".result").show();
				
				$.post("inline.php?menukey={{$menukey}}&include_file=plugin/{{$menukey}}/player_detail.php&api=1", postdata, function(data){
					
					// 結果表示
					player[label]["result"] = "更新を行いました。";
					$(".result").fadeOut(1000);
				}, 'json');
			}
			return false;
		});
	});

});
//-->
</script>

{{*
<div id="vuetest" style="border:solid 1px black">
	<p v-text="hoge">
		
	</p>
	<ul>
		<li v-for="a in fuga">
			<span>${a.a}</span>
		</li>
	</ul>
</div>
*}}


<div align="left" style="white-space: nowrap;">
	<div style="float:left">
		<form id="f1" method="POST">
			<input type="hidden" name="f1_submit" value="1">
			<input type="hidden" name="import_player_id" id="f1_import_player_id" value="0">
			<fieldset>
				<legend>対象プレイヤー選択</legend>
				{{if $error_message}}
					<div class="error">{{$error_message}}</div>
				{{/if}}
				<table>
					<tr>
						<td>
							<label>player_id<input type="radio" name="idtype" value="player_id" class="f1_radio_idtype" edittarget="f1_player_id" checked="checked"></label>
						</td>
						<td>
							<input type="text" name="player_id" id="f1_player_id" class="app_user_id" value="{{$player_id}}" size="12">
						</td>
					</tr>
					<tr>
						<td>
							<label>account<input type="radio" name="idtype" value="account" class="f1_radio_idtype" edittarget="f1_account"></label>
						</td>
						<td>
							<input type="text" name="account" id="f1_account" value="{{$account}}" size="32" disabled="disabled">
						</td>
					</tr>
					<tr>
						<td>
							<label>move_code<input type="radio" name="idtype" value="move_code" class="f1_radio_idtype" edittarget="f1_move_code"></label>
						</td>
						<td>
							<input type="text" name="move_code" id="f1_move_code" value="{{$move_code}}" size="32" disabled="disabled">
						</td>
					</tr>
					<tr>
						<td>
							<label>player_code<input type="radio" name="idtype" value="player_code" class="f1_radio_idtype" edittarget="f1_player_code"></label>
						</td>
						<td>
							<input type="text" name="player_code" id="f1_player_code" value="{{$player_code}}" size="32" disabled="disabled">
						</td>
					</tr>
				</table>
				<button type="submit" value="">表示</button>
				
			</fieldset>
		</form>
	</div>
	<div style="float:left">
		<form id="f1b" method="POST">
			<input type="hidden" name="f1b_submit" value="1">
			<fieldset>
				<legend>該当アカウント</legend>
				{{if $accounts}}
					<ul>
					{{foreach item=i from=$accounts}}
						<li>{{$i}}</li>
					{{/foreach}}
					</ul>
				{{/if}}
			</fieldset>
		</form>
	</div>
	<div align="left" style="float:left;">
		<form method="POST">
			<input type="hidden" name="f2_submit" value="">
			<fieldset>
				<legend>所持課金通貨</legend>
				<div style="text-align:right">
					<table>
						<tr>
							<td>有料枠</td>
							<td>{{$shoppoint[0]|number_format}}</td>
						</tr>
						<tr>
							<td>無料枠</td>
							<td>{{$shoppoint[1]|number_format}}</td>
						</tr>
					</table>
				</div>
			</fieldset>
		</form>
	</div>
	
	<br style="clear:both;">
</div>

<hr>

{{if $player_id }}
	<div align="left" style="float:left;">
		<form method="POST">
			<input type="hidden" name="f2_submit" value="player_game">
			<input type="hidden" name="player_id" value="{{$player_id}}">
			<fieldset>
				<legend>プレイヤー情報</legend>
				<table class="main2">
					<tr class="tr{{cycle values="1,2"}}">
						<td>プレイヤーID</td>
						<td>{{$player_id}}</td>
					</tr>
					<tr class="tr{{cycle values="1,2"}}"><td>名前                    </td><td>{{$player_game.name}}</td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>シナリオ名              </td><td>{{$player_game.scenario_name}}</td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>チュートリアル進捗      </td><td><input type="text" id="player_game_step"                 name="player_game[step]"                 size="4"  value="{{$player_game.step                        }}"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>レベル                  </td><td><input type="text" id="player_game_level"                name="player_game[level]"                size="4"  value="{{$player_game.level                       }}"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>経験値                  </td><td><input type="text" id="player_game_player_exp"           name="player_game[player_exp]"           size="8"  value="{{$player_game.player_exp                  }}"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>所持ゲーム内通貨        </td><td><input type="text" id="player_game_mag"                  name="player_game[mag]"                  size="8"  value="{{$player_game.mag                         }}"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>所持第二ゲーム内通貨    </td><td><input type="text" id="player_game_bit"                  name="player_game[bit]"                  size="8"  value="{{$player_game.bit                         }}"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>ST                      </td><td><input type="text" id="player_game_st"                   name="player_game[st]"                   size="4"  value="{{$player_game.st                          }}"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>ST最大                  </td><td><input type="text" id="player_game_st_max"               name="player_game[st_max]"               size="4"  value="{{$player_game.st_max-$levelupbonus.st     }}"> + {{$levelupbonus.st}}</td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>ST最終更新              </td><td><input type="text" id="player_game_st_updated"           name="player_game[st_updated]"           size="20" value="{{$player_game.st_updated                  }}" class="datetime"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>BP                      </td><td><input type="text" id="player_game_bp"                   name="player_game[bp]"                   size="4"  value="{{$player_game.bp                          }}"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>BP最大                  </td><td><input type="text" id="player_game_bp_max"               name="player_game[bp_max]"               size="4"  value="{{$player_game.bp_max                      }}"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>BP最終更新              </td><td><input type="text" id="player_game_bp_updated"           name="player_game[bp_updated]"           size="20" value="{{$player_game.bp_updated                  }}" class="datetime"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>EP                      </td><td><input type="text" id="player_game_ep"                   name="player_game[ep]"                   size="4"  value="{{$player_game.ep                          }}"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>EP最大                  </td><td><input type="text" id="player_game_ep_max"               name="player_game[ep_max]"               size="4"  value="{{$player_game.ep_max                      }}"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>EP最終更新              </td><td><input type="text" id="player_game_ep_updated"           name="player_game[ep_updated]"           size="20" value="{{$player_game.ep_updated                  }}" class="datetime"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>最終ログイン            </td><td><input type="text" id="player_game_login_date"           name="player_game[login_date]"           size="20" value="{{$player_game.login_date                  }}" class="datetime"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>ログインボーナス受取日時</td><td><input type="text" id="player_game_last_loginbonus_date" name="player_game[last_loginbonus_date]" size="20" value="{{$player_game.last_loginbonus_date        }}" class="datetime"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>全体配布受け取り状況    </td><td><input type="text" id="player_game_recv_dists"           name="player_game[recv_dists]"           size="8"  value="{{$player_game.recv_dists                  }}"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>リーダーカードスロット  </td><td><input type="text" id="player_game_leader_card_slot"     name="player_game[leader_card_slot]"     size="8"  value="{{$player_game.leader_card_slot            }}"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>初心者期間完了日時      </td><td><input type="text" id="player_game_beginner_expire"      name="player_game[beginner_expire]"      size="20" value="{{$player_game.beginner_expire             }}" class="datetime"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>チュートリアル完了日時  </td><td><input type="text" id="player_game_begin_date"           name="player_game[begin_date]"           size="20" value="{{$player_game.begin_date                  }}" class="datetime"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>コメント                </td><td><input type="text" id="player_game_comment"              name="player_game[comment]"              size="30" value="{{$player_game.comment                     }}"></td></tr>
					
					<tr class="tr{{cycle values="1,2"}}"><td>アクティブデッキ        </td><td><input type="text" id="player_game_active_deck_id"       name="player_game[active_deck_id]"       size="2"  value="{{$player_game.active_deck_id              }}"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>リーダーカードスロット  </td><td><input type="text" id="player_game_leader_card_slot"     name="player_game[leader_card_slot]"     size="2"  value="{{$player_game.leader_card_slot            }}"></td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>ホーム画面キャラID      </td><td><input type="text" id="player_game_home_character_id"    name="player_game[home_character_id]"    size="4"  value="{{$player_game.home_character_id           }}"></td></tr>
					
					<tr class="tr{{cycle values="1,2"}}"><td>次の誕生日              </td><td><input type="text" id="player_game_birthday"             name="player_game[birthday]"             size="20" value="{{$player_game.birthday                    }}" class="datetime"></td></tr>
					
					<tr class="tr{{cycle values="1,2"}}"><td>作成日時    </td><td>{{$player_game.created}}</td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>最終更新日時</td><td>{{$player_game.updated}}</td></tr>
					<tr class="tr{{cycle values="1,2"}}"><td>引き継ぎコード        </td><td>{{$move_code}}</td></tr>
					
				</table>
				<hr>
				<div align="center">
					<button type="submit" class="update">更新</button>
				</div>
			</fieldset>
		</form>
	</div>
	
	<div align="left" style="float:left;">
		<form method="POST">
			<input type="hidden" name="f2_submit" value="move_code">
			<input type="hidden" name="player_id" value="{{$player_id}}">
			<fieldset class="toggle_next">
				<legend>引き継ぎ情報</legend>
				<div>
					<table class="main2">
						<tr class="tr{{cycle values="1,2"}}"><td>引き継ぎコード</td><td><input type="text" id="move_code_move_code" name="move_code[move_code]" size="12" value="{{$move_code}}"></td></tr>
						<tr class="tr{{cycle values="1,2"}}"><td>パスワード (更新のみ)</td><td><input type="text" id="move_code_password"   name="move_code[password]"   size="12"  value=""></td></tr>
					</table>
					<hr>
					<div align="center">
						<button type="submit" class="update">更新</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	
	<div align="left" style="float:left;">
		<form method="POST">
			<input type="hidden" name="f2_submit" value="player_birthmon">
			<input type="hidden" name="player_id" value="{{$player_id}}">
			<fieldset class="toggle_next">
				<legend>誕生月</legend>
				<div>
					<table class="main2">
						<tr class="tr{{cycle values="1,2"}}"><td>誕生月</td><td><input type="text" id="player_birthmon_birthmon" name="player_birthmon[birthmon]" size="10" value="{{$player_birthmon.birthmon}}" class="date"></td></tr>
					</table>
					<hr>
					<div align="center">
						<button type="submit" class="update">更新</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	
	<div align="left" style="float:left;">
		<form method="POST">
			<input type="hidden" name="f2_submit" value="player_otetsudai">
			<input type="hidden" name="player_id" value="{{$player_id}}">
			<fieldset class="toggle_next">
				<legend>おてつだい情報</legend>
				<div>
					<table class="main2">
						<tr class="tr{{cycle values="1,2"}}"><td>おてつだいレベル</td><td><input type="text" id="player_otetsudai_level"      name="player_otetsudai[level]"      size="4"  value="{{$player_otetsudai.level     }}"></td></tr>
						<tr class="tr{{cycle values="1,2"}}"><td>おてつだい経験値</td><td><input type="text" id="player_otetsudai_exp"        name="player_otetsudai[exp]"        size="4"  value="{{$player_otetsudai.exp       }}"></td></tr>
						<tr class="tr{{cycle values="1,2"}}"><td>稼働日時        </td><td><input type="text" id="player_otetsudai_registed"   name="player_otetsudai[registed]"   size="20" value="{{$player_otetsudai.registed  }}" class="datetime"></td></tr>
						<tr class="tr{{cycle values="1,2"}}"><td>slot_1_1        </td><td><input type="text" id="player_otetsudai_slot_1_1"   name="player_otetsudai[slot_1_1]"   size="4"  value="{{$player_otetsudai.slot_1_1  }}"></td></tr>
						<tr class="tr{{cycle values="1,2"}}"><td>slot_1_2        </td><td><input type="text" id="player_otetsudai_slot_1_2"   name="player_otetsudai[slot_1_2]"   size="4"  value="{{$player_otetsudai.slot_1_2  }}"></td></tr>
						<tr class="tr{{cycle values="1,2"}}"><td>slot_2_1        </td><td><input type="text" id="player_otetsudai_slot_2_1"   name="player_otetsudai[slot_2_1]"   size="4"  value="{{$player_otetsudai.slot_2_1  }}"></td></tr>
						<tr class="tr{{cycle values="1,2"}}"><td>slot_2_2        </td><td><input type="text" id="player_otetsudai_slot_2_2"   name="player_otetsudai[slot_2_2]"   size="4"  value="{{$player_otetsudai.slot_2_2  }}"></td></tr>
						<tr class="tr{{cycle values="1,2"}}"><td>slot_3_1        </td><td><input type="text" id="player_otetsudai_slot_3_1"   name="player_otetsudai[slot_3_1]"   size="4"  value="{{$player_otetsudai.slot_3_1  }}"></td></tr>
						<tr class="tr{{cycle values="1,2"}}"><td>slot_3_2        </td><td><input type="text" id="player_otetsudai_slot_3_2"   name="player_otetsudai[slot_3_2]"   size="4"  value="{{$player_otetsudai.slot_3_2  }}"></td></tr>
						<tr class="tr{{cycle values="1,2"}}"><td>稼働中か        </td><td><input type="text" id="player_otetsudai_is_working" name="player_otetsudai[is_working]" size="1"  value="{{$player_otetsudai.is_working}}"></td></tr>
					</table>
					<hr>
					<div align="center">
						<button type="submit" class="update">更新</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	
	<div align="left" style="float:left;">
		<form method="POST">
			<input type="hidden" name="f2_submit" value="player_deck">
			<input type="hidden" name="player_id" value="{{$player_id}}">
			<input type="hidden" name="deck_id" value="{{$deck_id}}">
			<fieldset class="toggle_next">
				<legend>デッキ情報</legend>
				<div style="display:none" id="f2_player_deck_div">
					<table class="main2">
						<tr class="trh">
							<th>デッキ番号</th>
							<th>デッキ名前</th>
							<th>カード状況</th>
						</tr>
						{{foreach name=story_master item=ii from=$deck}}
							<tr class="tr{{cycle values="1,2"}}">
								<td align="right">
									{{$ii.deck_id}}
								</td>
								<td align="right">
									{{$ii.name}}
								</td>
								<td align="center">
									<table>
										
										{{foreach item=col from=$deck_display_cols}}
											<tr>
												<td>
													{{$col}}
												</td>
												<td>
												<select name="deck[{{$pos}}]">
													<option value="0">未設定</option>
													{{foreach item=card_id key=card_slot from=$slot2card}}
														{{if $card_id > 0 or $card_slot==$slot}}
															<option value="{{$card_slot}}" {{if $card_slot==$ii[$col]}}selected{{/if}}>{{$card_slot}} (card_id:{{$card_id}}:{{$card_master[$card_id].name}})</option>
														{{/if}}
													{{/foreach}}
												</select>
												</td>
											</tr>
										{{/foreach}}
										
									</table>
								</td>
							</tr>
						{{/foreach}}
					</table>
					<hr>
					<div align="center">
						{{* デッキ更新を実装したら解禁
						<button type="submit" class="update">更新</button>
						*}}
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	
{{*
	<div align="left" style="float:left;">
		<form method="POST">
			<input type="hidden" name="f2_submit" value="friend_point">
			<input type="hidden" name="player_id" value="{{$player_id}}">
			<fieldset class="toggle_next">
				<legend>フレンドポイント</legend>
				<div style="display:none" id="f2_friend_point_div">
					<table class="main2">
						<tr class="tr{{cycle values="1,2"}}"><td>フレンドポイント</td><td><input type="text" id="friend_point" name="friend_point[point]" size="4"  value="{{$friend_point.point}}"></td></tr>
					</table>
					<hr>
					<div align="center">
						<button type="submit" class="update">更新</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
*}}
	
	<div align="left" style="float:left;">
		<fieldset class="toggle_next">
			<legend>プレゼントボックス</legend>
			<div style="display:none" id="f2_player_present_box_div">
				<form method="POST">
					<input type="hidden" name="f2_submit" value="player_present_box">
					<input type="hidden" name="player_id" value="{{$player_id}}">
					<table class="main2">
						<tr class="trh">
							<th>ID</th>
							<th>種別</th>
							<th>番号</th>
							<th>数量</th>
							<th>有効期限</th>
							<th>コメント</th>
							<th></th>
							<th>削除</th>
						</tr>
						{{foreach name=present_box item=i key=k from=$player_present_box}}
							<tr class="tr{{cycle values="1,2"}}">
								<td align="right">
									{{$i.id}}
								</td>
								<td>
									{{$i.type}} : {{$master.stash_type[$i.type]}}
								</td>
								<td align="right">
									{{$i.item}}
								</td>
								<td align="right">
									{{$i.qty}}
								</td>
								<td align="left">
									{{$i.expire}}
								</td>
								<td align="left">
									{{$i.comment}}
								</td>
								
								<td></td>
								<td align="center"><input type="checkbox" name="deletes[]" value="{{$i.id}}"></td>
							</tr>
						{{/foreach}}
					</table>
					
					<div align="center">
						<button type="submit" class="update">削除</button>
					</div>
				</form>
				<hr>
				<form method="POST">
					<input type="hidden" name="f2_submit" value="player_present_box_create">
					<input type="hidden" name="player_id" value="{{$player_id}}">
					<fieldset>
						<legend>追加</legend>
						<div>
							<table>
								<tr>
									<td>カードID</td><td>
										<input type="text" name="presents[card_id]" value="" size="8" class="card_id" id="player_present_box_create_card_id" choser="cardchoser">
									</td>
								</tr>
								<tr>
									<td>アイテムID</td><td>
										<input type="text" name="presents[item_id]" value="" size="8" class="item_id" id="player_present_box_create_item_id" choser="itemchoser">
									</td>
								</tr>
								<tr>
									<td>アイテム個数</td><td>
										<input type="text" name="presents[item_qty]" value="" size="4">
									</td>
								</tr>
								<tr>
									<td>ゲーム内通貨</td><td>
										<input type="text" name="presents[mag]" value="" size="8">
									</td>
								</tr>
								<tr>
									<td>経験値</td><td>
										<input type="text" name="presents[exp]" value="" size="8">
									</td>
								</tr>
								<tr>
									<td>課金通貨</td><td>
										<input type="text" name="presents[mc]" value="" size="8">
									</td>
								</tr>
								<tr>
									<td>フレンドポイント</td><td>
										<input type="text" name="presents[fp]" value="" size="8">
									</td>
								</tr>
								<tr>
									<td>第二ゲーム内通貨</td><td>
										<input type="text" name="presents[bit]" value="" size="8">
									</td>
								</tr>
								<tr>
									<td>有効期限</td><td>
										<input type="text" name="expire" value="2038-01-01 00:00:00" size="20" class="datetime">
									</td>
								</tr>
								<tr>
									<td>コメント</td><td>
										<input type="text" name="comment" value="" size="40">
									</td>
								</tr>
								<tr>
									<td colspan="2" align="center">
										<button type="submit" class="update">送信</button>
									</td>
								</tr>
							</table>
						</div>
					</fieldset>
				</form>
			</div>
		</fieldset>
	</div>
	
	<div align="left" style="float:left;">
		<fieldset class="toggle_next">
			<legend>プレゼントボックス受け取り履歴</legend>
			<div style="display:none" id="f2_player_present_box_history_div">
				<form method="POST">
					<input type="hidden" name="f2_submit" value="player_present_box_history">
					<input type="hidden" name="player_id" value="{{$player_id}}">
					<table class="main2">
						<tr class="trh">
							<th>ID</th>
							<th>種別</th>
							<th>番号</th>
							<th>数量</th>
							<th>有効期限</th>
							<th>コメント</th>
							{{*
							<th></th>
							<th>削除</th>
							*}}
						</tr>
						{{foreach name=present_box item=i key=k from=$player_present_box_history}}
							<tr class="tr{{cycle values="1,2"}}">
								<td align="right">
									{{$i.id}}
								</td>
								<td>
									{{$i.type}} : {{$master.stash_type[$i.type]}}
								</td>
								<td align="right">
									{{$i.item}}
								</td>
								<td align="right">
									{{$i.qty}}
								</td>
								<td align="left">
									{{$i.expire}}
								</td>
								<td align="left">
									{{$i.comment}}
								</td>
								{{*
								<td></td>
								<td align="center"><input type="checkbox" name="deletes[]" value="{{$i.id}}"></td>
								*}}
							</tr>
						{{/foreach}}
					</table>
					
					{{*
					<div align="center">
						<button type="submit" class="update">削除</button>
					</div>
					*}}
					
				</form>
			</div>
		</fieldset>
	</div>
	
	<div align="left" style="float:left;">
		<form method="POST">
			<input type="hidden" name="f2_submit" value="player_item">
			<input type="hidden" name="player_id" value="{{$player_id}}">
			<fieldset class="toggle_next">
				<legend>所持アイテム情報</legend>
				<div style="display:none" id="f2_player_item_div">
					<table class="main2">
						<tr class="trh">
							<th>アイテムID</th>
							<th>所持数</th>
							<th>有効期限</th>
						</tr>
						{{foreach item=rec key=id from=$player_items}}
							<tr class="tr{{cycle values="1,2"}}">
								<td align="left">
									<!-- {{$rec.item_id}}:{{$item_master[$rec.item_id].name}} -->
									<input type="text" name="player_item[{{$rec.id}}][item_id]" value="{{$rec.item_id}}" size="4" id="player_item_{{$rec.id}}_item_id" choser="itemchoser">									<span>
										{{if $rec.item_id > 0}}
											{{if $item_master[$rec.item_id]}}
												({{$item_master[$rec.item_id].name}})
											{{else}}
												<span class="error">UNKNOWN</span>
											{{/if}}
										{{/if}}
									</span>
								</td>
								<td align="right">
									<input type="text" name="player_item[{{$rec.id}}][item_qty]" value="{{$rec.item_qty}}" size="4">
								</td>
								<td align="left">
									<input type="text" name="player_item[{{$rec.id}}][expire]" value="{{$rec.expire}}" size="20" id="player_item_{{$rec.id}}_expire" class="datetime">
								</td>
							</tr>
						{{/foreach}}
					</table>
					<table>
						<tr>
							<td>
								追加アイテムID
							</td>
							<td>
								<input type="text" name="player_item_insert[id]" value="" size="8" id="player_item_insert_id" choser="itemchoser">
							</td>
						</tr>
						<tr>
							<td>
								追加アイテム個数
							</td>
							<td>
								<input type="text" name="player_item_insert[qty]" value="" size="8">
							</td>
						</tr>
						<tr>
							<td>
								追加アイテム有効期限
							</td>
							<td>
								<input type="text" name="player_item_insert[expire]" value="{{$player_item_default_expire}}" size="20" id="player_item_insert_expire" class="date">
							</td>
						</tr>
					</table>
					<hr>
					<div align="center">
						
						<button type="submit" class="update">更新</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	
	<div align="left" style="float:left;">
		<form method="POST">
			<input type="hidden" name="f2_submit" value="friend">
			<input type="hidden" name="player_id" value="{{$player_id}}">
			<fieldset class="toggle_next">
				<legend>フレンド</legend>
				<div style="display:none" id="f2_friend_div">
					<table class="main2">
						<tr class="trh">
							<td align="center">プレイヤーID</td>
							<td align="center">最終助っ人日時</td>
							<td align="center">成立日時</td>
						</tr>
						{{foreach item=ii from=$friend}}
							<tr class="tr{{cycle values="1,2"}}">
								<td align="right">{{$ii.friend_player_id}}</td>
								<td align="left">{{$ii.help_date}}</td>
								<td align="right">{{$ii.created}}</td>
							</td>
						{{foreachelse}}
							<tr class="tr{{cycle values="1,2"}}">
								<td colspan="3" align="center">
									なし
								</td>
							</tr>
						{{/foreach}}
					</table>
				</div>
			</fieldset>
		</form>
	</div>
	
	<div align="left" style="float:left;">
		<form method="POST">
			<input type="hidden" name="f2_submit" value="friend_request">
			<input type="hidden" name="player_id" value="{{$player_id}}">
			<fieldset class="toggle_next">
				<legend>フレンド申請</legend>
				<div style="display:none" id="f2_friend_request_div">
					<table class="main2">
						<tr class="trh">
							<td align="center">プレイヤーID</td>
							<td align="center">申請日時</td>
							<td align="center">削除</td>
						</tr>
						{{foreach item=ii from=$friend_request}}
							<tr class="tr{{cycle values="1,2"}}">
								<td align="right">{{$ii.request_player_id}}</td>
								<td align="left">{{$ii.created}}</td>
								<td align="center">
									<input type="checkbox" name="delete[]" value="{{$ii.request_player_id}}">
								</td>
							</td>
						{{foreachelse}}
							<tr class="tr{{cycle values="1,2"}}">
								<td colspan="3" align="center">
									なし
								</td>
							</tr>
						{{/foreach}}
					</table>
					<div>
						<button type="submit" class="update">更新</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	
	
	
	
	<div align="left" style="float:left;">
		<form method="POST" id="f2_launchcount">
			<input type="hidden" name="f2_submit" value="launchcount">
			<input type="hidden" name="player_id" value="{{$player_id}}">
			<fieldset class="toggle_next">
				<legend id="f2_launchcount_label">起動回数</legend>
				<div style="display:none" id="f2_launchcount_div">
					<table class="main2">
						<tr class="trh">
							<td>day</td>
							<td>count</td>
						</tr>
						{{foreach item=i from=$lauchcount}}
							<tr class="tr{{cycle values="1,2"}}">
								<td>
									{{$i.date}}
								</td>
								<td style="text-align:right">
									{{$i.count}}
								</td>
							</tr>
						{{/foreach}}
					</table>
				</div>
{{* Vue であれこれやろうとしたのの残骸
<script type="text/javascript">
<!--
var player = {};
$(function(){
	player.launchcount =  new Vue({
		el: '#f2_launchcount', 
		data : {}
	});
	$('#f2_launchcount_label').click(function(){
		$.getJSON('inline.php?include_file=plugin/{{$menukey}}/player_detail.php&menukey={{$menukey}}&json=launchcount&player_id={{$player_id}}', function(data){
			player.launchcount = data;
		});
	})
})
//-->
</script>
*}}
			</fieldset>
		</form>
	</div>
	
	
	
	<div align="left" style="float:left;">
		<form method="POST">
			<input type="hidden" name="f2_submit" value="player_mission">
			<input type="hidden" name="player_id" value="{{$player_id}}">
			<fieldset class="toggle_next">
				<legend>ミッション</legend>
				<div style="display:none" id="f2_player_mission_div">
					<table class="main2">
						<tr class="trh">
							<th>ミッションID</th>
							<th>タイトル</th>
							<th>
								状態
							</th>
						</tr>
						{{foreach item=rec key=id from=$mission_master}}
							<tr class="tr{{cycle values="1,2"}}">
								<td align="right">
									{{$id}}
								</td>
								<td align="left">
									{{$rec.title}}
								</td>
								<td align="left">
									<label><input type="radio" name="player_mission[{{$id}}]" value="clear"  {{if $mission.clear[$id]}}checked{{/if}}>完了</label>
									<label><input type="radio" name="player_mission[{{$id}}]" value="notify" {{if $mission.notify[$id]}}checked{{/if}}>達成</label>
									<label><input type="radio" name="player_mission[{{$id}}]" value="none"   {{if ! $mission.clear[$id]}}{{if ! $mission.notify[$id]}}checked{{/if}}{{/if}}>進行中・未開放</label>
								</td>
							</tr>
						{{/foreach}}
					</table>
					<hr>
					<div align="center">
						<button type="submit" class="update">更新</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	
	
	
	<div align="left" style="float:left;">
		<form method="POST">
			<input type="hidden" name="f2_submit" value="player_quest">
			<input type="hidden" name="player_id" value="{{$player_id}}">
			<fieldset>
				<legend>
					クエスト
					<select id="f2_quest_quest_id_selector" class="vue_trigger" name="map_id" data-label="quest" data-vuel="f2_quest">
						<option value="">読み込み対象 map_id を選択</option>
						<option value="">--------</option>
						{{foreach item=i key=k from=$maps}}
							<option value="{{$k}}" {{if $k==$quest_id}}selected{{/if}}>map_id : {{$k}}</option>
						{{/foreach}}
					</select>
				</legend>
				
				<div id="f2_quest" style="display:none">
					
					<p class="result" v-html="result">
						
					</p>
					
					<table class="main2">
						<thead>
							<tr class="trh">
								<td>
									クエストID
								</td>
								<td>
									進行段階
								</td>
								<td>
									クリア済みか
								</td>
								<td>
									クリア日時
								</td>
								<td colspan="4">
									収集状況
								</td>
								<td>
									収集全コンプ済み
								</td>
							</tr>
						</thead>
						
						<tbody>
							<tr v-for="q in quests" v-bind:class="q.trc">
								<td align="right">
									${q.quest_id}
								</td>
								<td align="right">
									<input type="number=" name="quest[${q.quest_id}][s]" v-bind:value="q.step" size="4">
								</td>
								<td style="text-align:center">
									<input type="checkbox" name="quest[${q.quest_id}][c]" value="1" v-bind:checked="q.is_clear">
								</td>
								<td>
									<input type="text=" name="quest[${q.quest_id}][d]" v-bind:value="q.clear_date" size="20">
								</td>
								<td>
									<input type="checkbox" name="quest[${q.quest_id}][a][1]" value="1" v-bind:checked="q.comp.c1">
								</td>
								<td>
									<input type="checkbox" name="quest[${q.quest_id}][a][2]" value="1" v-bind:checked="q.comp.c2">
								</td>
								<td>
									<input type="checkbox" name="quest[${q.quest_id}][a][3]" value="1" v-bind:checked="q.comp.c3">
								</td>
								<td>
									<input type="checkbox" name="quest[${q.quest_id}][a][4]" value="1" v-bind:checked="q.comp.c4">
								</td>
								<td style="text-align:center">
									<input type="checkbox" name="quest[${q.quest_id}][all]" value="1" v-bind:checked="q.allcomp">
								</td>
							</tr>
						</tbody>
						
					</table>
					<hr>
					<div align="center">
						<button type="submit" id="f2_quest_submit">更新</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	
	<div align="left" style="float:left;">
		<form method="POST">
			<input type="hidden" name="f2_submit" value="event_point">
			<input type="hidden" name="player_id" value="{{$player_id}}">
			<fieldset>
				<legend>
					イベントポイント
					<select id="f2_event_point_event_id_selector" class="vue_trigger" name="event_id" data-label="event_point" data-vuel="f2_event_point">
						<option value="">読み込み対象 event_id を選択</option>
						<option value="">--------</option>
						{{foreach item=i key=k from=$event_master}}
							<option value="{{$k}}" {{if $k==$event_id}}selected{{/if}}>event_id : {{$k}}</option>
						{{/foreach}}
					</select>
				</legend>
				
				<div id="f2_event_point" style="display:none">
					
					<p class="result" v-html="result">
						
					</p>
					
					<table>
						<tbody>
							<tr>
								<td>
									所持ポイント
								</td>
								<td>
									<input type="text" name="point" value="" size="8" v-bind:value="point">
								</td>
							</tr>
							<tr>
								<td>
									最終獲得日時
								</td>
								<td>
									<input type="text" name="last_point" value="" size="20" v-bind:value="last_point">
								</td>
							</tr>
							<tr>
								<td>
									達成報酬
								</td>
								<td>
									<textarea name="recved_reward" cols="30" rows="10" v-bind:value="recved_reward" readonly></textarea>
								</td>
							</tr>
						</tbody>
					</table>
					
					<hr>
					
					<div align="center">
						<button type="submit" id="f2_event_point_submit">更新</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	
	<div align="left" style="float:left;">
		<form method="POST">
			<input type="hidden" name="f2_submit" value="card">
			<input type="hidden" name="player_id" value="{{$player_id}}">
			<fieldset class="toggle_next">
				<legend>所持カード情報</legend>
				<div style="display:none" id="f2_player_card_div">
					<table class="main2">
						<tr class="trh">
							<th>スロット</th>
							<th>カードID</th>
							<th>シリアル</th>
							<th>レベル</th>
							<th>現在経験値</th>
							<th>限突</th>
							<th>レア度</th>
							<th>覚醒</th>
							<th>加算hp</th>
							<th>加算atk</th>
							<th>加算heal</th>
							<th>ロック状態</th>
							<th>入手日時</th>
							<th>最終更新日時</th>
							<th></th>
							<th>枝リセット</th>
							<th></th>
							<th>更新対象</th>
						</tr>
						{{foreach name=player_card item=card key=k from=$player_cards}}
							<tr class="tr{{cycle values="1,2"}}">
								<td align="right">{{$card.card_slot}}&nbsp;<input type="hidden" name="card[{{$k}}][card_slot]" value="{{$card.card_slot}}"></td>
								<td align="left" style="white-space:nowrap">
									<input type="text" size="6"  class="card_{{$k}} card_id" name="card[{{$k}}][card_id]"      value="{{$card.card_id}}"      disabled id="player_card_{{$k}}_card_id" choser="cardchoser">
									<span>
										{{if $card.card_id > 0}}
											{{if $card_master[$card.card_id]}}
												({{$card_master[$card.card_id].name}})
											{{else}}
												<span class="error">UNKNOWN</span>
											{{/if}}
										{{/if}}
									</span>
								</td>
								<td align="right"><input type="text" size="12" class="card_{{$k}}" name="card[{{$k}}][card_serial]"  value="{{$card.card_serial}}"  disabled></td>
								<td align="right"><input type="text" size="4"  class="card_{{$k}}" name="card[{{$k}}][level]"        value="{{$card.level}}"        disabled></td>
								<td align="right"><input type="text" size="8"  class="card_{{$k}}" name="card[{{$k}}][card_exp]"     value="{{$card.card_exp}}"     disabled></td>
								<td align="right"><input type="text" size="1"  class="card_{{$k}}" name="card[{{$k}}][bild]"         value="{{$card.bild}}"         disabled></td>
								<td align="right"><input type="text" size="1"  class="card_{{$k}}" name="card[{{$k}}][rarity]"       value="{{$card.rarity}}"       disabled></td>
								<td align="right"><input type="text" size="1"  class="card_{{$k}}" name="card[{{$k}}][is_ev]"        value="{{$card.is_ev}}"        disabled></td>
								<td align="right"><input type="text" size="6"  class="card_{{$k}}" name="card[{{$k}}][bonus_hp]"     value="{{$card.bonus_hp}}"     disabled></td>
								<td align="right"><input type="text" size="6"  class="card_{{$k}}" name="card[{{$k}}][bonus_atk]"    value="{{$card.bonus_atk}}"    disabled></td>
								<td align="right"><input type="text" size="6"  class="card_{{$k}}" name="card[{{$k}}][bonus_heal]"   value="{{$card.bonus_heal}}"   disabled></td>
								<td align="right"><input type="text" size="3"  class="card_{{$k}}" name="card[{{$k}}][is_lock]"      value="{{$card.is_lock}}"      disabled></td>
								<td align="right" style="white-space:nowrap">{{$card.registed}}</td>
								<td align="right" style="white-space:nowrap">{{$card.updated}}</td>
								<td></td>
								<td align="center"><input type="checkbox" name="deletebranch[]" value="{{$card.card_slot}}"></td>
								<td></td>
								<td align="center"><input type="checkbox" class="edittoggle" name="updates[]" value="{{$k}}" toggletarget="card_{{$k}}"></td>
							</tr>
							{{if $smarty.foreach.player_card.iteration is div by 10}}
								<tr class="trh">
									<th>スロット</th>
									<th>カードID</th>
									<th>シリアル</th>
									<th>レベル</th>
									<th>現在経験値</th>
									<th>限突</th>
									<th>レア度</th>
									<th>覚醒</th>
									<th>加算hp</th>
									<th>加算atk</th>
									<th>加算heal</th>
									<th>ロック状態</th>
									<th>入手日時</th>
									<th>最終更新日時</th>
									<th></th>
									<th>枝リセット</th>
									<th></th>
									<th>更新対象</th>
								</tr>
							{{/if}}
						{{/foreach}}
					</table>
					<hr>
					<div align="center">
						<button type="submit" class="update">更新</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	
	<br style="clear:both">
	
	
	
	<div id="cardchoser" class="choser disable-selection">
		<span class="card choseitem" value=""><span>clear</span></span><br>
		{{foreach key=k item=i from=$card_master}}
			<span class="card choseitem" value="{{$k}}" title="{{$i.name}}"><span>{{$k}}</span>:<span class="card_name">{{$i.name}}</span></span>
		{{/foreach}}
	</div>
	<div id="skillchoser" class="choser disable-selection">
		<span class="card choseitem" value=""><span>clear</span></span><br>
		{{foreach key=k item=i from=$skill_master}}
			<span class="skill choseitem" value="{{$i.skill_id}}" title="{{$i.name}}">{{$k}}:{{$i.name}}</span>
		{{/foreach}}
	</div>
	<div id="itemchoser" class="choser disable-selection">
		<span class="item choseitem" value=""><span>clear</span></span><br>
		{{foreach key=k item=i from=$item_master}}
			<span class="item choseitem" value="{{$i.id}}" title="{{$i.name}}">{{$k}}:{{$i.name}}</span>
		{{/foreach}}
	</div>
<style>
<!--
.choser {
	width:500px;
	height:200px;
	border: 1px solid gray;
	border-radius: 2px;
	background-color:#fdfefe;
	margine:3px;
	padding:5px;
	overflow-y:scroll;
	display:none;
	line-height:1.5;
}
span.choseitem {
	border: 1px solid green;
	border-radius: 2px;
	cursor: pointer;
	margine:2px;
	padding:2px;
	white-space:nowrap;
}
.nobr {white-space:nowrap;}

-->
</style>
<script>
<!--
var timer_handle = null;
$(function(){
	
	
	$("button.update").click(function(){
		return confirm('更新します、よろしいですか？');
	});
	
	
	$("input[choser]").each(function(){
		var l = $(this).attr('id') + "_chooserlabel";
		$(this).attr("for", l);
		if ($(this).next().is("span:not([id])")) {
			$(this).next().attr('id', l).addClass("nobr");
		} else {
			$(this).after('<span class="nobr" id="' + l + '"></span>');
		}
	}).focus(function(){
		// 選択箱を出す input.text にフォーカスが入ったら
		
		// 閉じる処理の
		$(".choser:visible").hide();
		if (timer_handle) {window.clearTimeout(timer_handle);timer_handle = null;}
		
		// 一覧を開いてどこをターゲットするかメモ
		$("#" + $(this).attr("choser")).show().position({
			my: "left top", 
			at: "left bottom", 
			of: $(this)
		}).attr("target", $(this).attr("id"));
		
	}).blur(function(){
		// フォーカスが外れたら
		//$("#" + $(this).attr("choser")).attr("target", "").hide();
		//timer_handle = window.setTimeout(function(){$(".choser").hide();timer_handle = null;}, 100);
	}).change(function(){
		// 値が変更されたら
		//$("#" + $(this).attr("choser")).attr("target", "").hide();
		
		timer_handle = window.setTimeout(function(){$(".choser").hide();timer_handle = null;}, 100);
	});
	
	// よその要素にフォーカスが移ったら選択リストを閉じる
	$(":not([choser])").focus(function(){
		timer_handle = window.setTimeout(function(){$(".choser").hide();timer_handle = null;}, 100);
	});
	
	$(".choser .choseitem").click(function(){
		var p = $(this).parents(".choser").get(0);
		var t = $(p).attr("target")
		if (t) {
			$("#" + t).val($(this).attr("value"));
			$("#" + t).change();
			$("#" + t).blur();
			//$("#" + $("#" + t).attr("choser")).attr("target", "").hide();
			
			if ($("#" + t).attr("for")) {
				$("#" + $("#" + t).attr("for")).text("(" + $(this).attr("title") + ")");
			}
			
			timer_handle = window.setTimeout(function(){$(".choser").hide();timer_handle = null;}, 100);
		}
	});
	
	$("input.card_id").change(function(){
		if ($(this).val() > 0) {
			$(this).next().text("(" + $("span.card[value='" + $(this).val() + "'] > span.card_name").text() + ")");
		} else {
			$(this).next().text("");
		}
	});
	
	$("input:checkbox.edittoggle").change(function(){
		$("." +$(this).attr("toggletarget")).attr("disabled", ! $(this).is(":checked"));
		//$("." +$(this).attr("toggletarget")).attr("readonly", ! $(this).is(":checked"));
	});
	
	
	$('fieldset.toggle_next > legend:first-child').each(function(){
		var v = localStorage.getItem("visible_" + $(this).next().attr("id"));
		if (v == "show") {
			 $(this).next().show();
		} else if (v == "hide") {
			 $(this).next().hide();
		}
		if ($(this).next().is(":visible")) {
			$(this).html($(this).html() + '<span class="toggle_arrow" style="display:none">▽</span><span class="toggle_arrow">△</span>');
		} else {
			$(this).html($(this).html() + '<span class="toggle_arrow">▽</span><span class="toggle_arrow" style="display:none">△</span>');
		}
	});
	$('fieldset.toggle_next > legend:first-child').click(function(e){
		$(this).next().toggle(500, function(){
			localStorage.setItem("visible_" + $(this).attr("id"), ($(this).is(":visible")?"show":"hide"));
		});
		$(this).children(".toggle_arrow").toggle();
	}).css('cursor', 'pointer');
});
//-->
</script>
{{/if}}

<script>
<!--
$(function(){
	
	// player_id と account どっちでプレイヤーを選ぶか
	$(".f1_radio_idtype").change(function(){
		$(".f1_radio_idtype:not(:checked)").each(function(){
			$("#" + $(this).attr("edittarget")).attr("disabled", true);
		})
		$("#" + $(".f1_radio_idtype:checked").attr("edittarget")).attr("disabled", false);
	});
});
//-->
</script>
