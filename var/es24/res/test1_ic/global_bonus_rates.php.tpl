
{{if $result}}
	<p>
		{{if $result == 'f2ok'}}
			<span>global_bonus_rates を更新しました</span>
		{{elseif $result == 'error'}}
			<span class="error">問題が発生しました</span>
		{{/if}}
	</p>
{{/if}}

<div align="left" style="clear:both;">
	<form name="f1" id="f1" method="POST">
		<input type="hidden" name="f1_submit" value="1">
		<input type="hidden" name="edit_version" id="f1_edit_version" value="">
	</form>
	
	
	<div align="left" style="clear:both;">
		
		<form name="f2" method="POST">
			<input type="hidden" name="f2_submit" value="1">
			<input type="hidden" name="edit_version" value="{{$edit_version}}">
			<fieldset>
				<legend>
					報酬倍率
				</legend>
				
				<div style="float:left;margin:1px 5px;">
					
					<div>
							読み込み元
						
						<select name="edit_version" id="f2_edit_version">
							<option value="">バージョン指定なし</option>
							{{foreach from=$versions item=i key=k}}
								<option value="{{$k}}" {{if $edit_version==$k}}selected{{/if}}>{{$k}} : {{$i}}</option>
							{{/foreach}}
						</select>
					</div>
					
					<table>
						<tr>
							<td>
								更新対象
							</td>
							<td>
								{{foreach from=$versions item=i key=k}}
									<label><input type="checkbox" name="update_vers[]" value="{{$k}}" {{if $edit_version==$k}}checked{{/if}}>{{$k}} : {{$i}}</label><br>
								{{/foreach}}
							</td>
						</tr>
					</table>
					
					<table class="main2">
						
						<tr class="trh">
							<td colspan="2" align="center">期間</td>
							<td colspan="4" align="center">クエスト報酬</td>
							<td colspan="6" align="center">おてつだい報酬</td>
							<td colspan="2" align="center"></td>
						</tr>
						<tr class="trh">
							<td align="center">開始</td>
							<td align="center">終了</td>
							
							<td align="center">金</td>
							<td align="center">カード経験値</td>
							<td align="center">プレイヤー経験値</td>
							<td align="center">アイテム数</td>
							
							<td align="center">金</td>
							<td align="center">かけら</td>
							<td align="center">おてつだい経験値</td>
							<td align="center">プレイヤー経験値</td>
							<td align="center">フレンドポイント</td>
							<td align="center">アイテム数</td>
							
							<td align="center"></td>
							<td align="center">削除</td>
						</tr>
						
						{{foreach item=i key=k from=$global_bonus_rates}}
							<tr id="global_ratese_rec_{{$k}}" class="tr{{cycle values="1,2"}}">
								
								<td><input type="text" name="update[{{$k}}][start_date]" size="20" value="{{$i.start_date}}" id="global_ratese_rec_{{$k}}_start_date"  class="datetime"></td>
								<td><input type="text" name="update[{{$k}}][end_date]"   size="20" value="{{$i.end_date}}"   id="global_ratese_rec_{{$k}}_end_date"    class="datetime"></td>
								
								<td><input type="text" name="update[{{$k}}][quest_mag]" size="5" value="{{$i.quest_mag}}" class=""></td>
								<td><input type="text" name="update[{{$k}}][quest_card_exp]" size="5" value="{{$i.quest_card_exp}}" class=""></td>
								<td><input type="text" name="update[{{$k}}][quest_player_exp]" size="5" value="{{$i.quest_player_exp}}" class=""></td>
								<td><input type="text" name="update[{{$k}}][quest_item]" size="5" value="{{$i.quest_item}}" class=""></td>
								
								<td><input type="text" name="update[{{$k}}][otetsudai_mag]" size="5" value="{{$i.otetsudai_mag}}" class=""></td>
								<td><input type="text" name="update[{{$k}}][otetsudai_bit]" size="5" value="{{$i.otetsudai_bit}}" class=""></td>
								<td><input type="text" name="update[{{$k}}][otetsudai_otetsudai_exp]" size="5" value="{{$i.otetsudai_otetsudai_exp}}" class=""></td>
								<td><input type="text" name="update[{{$k}}][otetsudai_player_exp]" size="5" value="{{$i.otetsudai_player_exp}}" class=""></td>
								<td><input type="text" name="update[{{$k}}][otetsudai_friend_point]" size="5" value="{{$i.otetsudai_friend_point}}" class=""></td>
								<td><input type="text" name="update[{{$k}}][otetsudai_item]" size="5" value="{{$i.otetsudai_item}}" class=""></td>
								
								<td></td>
								<td><input type="checkbox" name="delete[]" value="{{$k}}"</td>
							</tr>
						{{/foreach}}
						<tr id="global_ratese_rec_new" class="tr{{cycle values="1,2"}}">
							
							<td><input type="text" name="insert[start_date]" size="20" id="global_ratese_rec_new_start_date" class="datetime"></td>
							<td><input type="text" name="insert[end_date]"   size="20" id="global_ratese_rec_new_end_date"   class="datetime"></td>
							
							<td><input type="text" name="insert[quest_mag]"        value="100" size="5" class=""></td>
							<td><input type="text" name="insert[quest_card_exp]"   value="100" size="5" class=""></td>
							<td><input type="text" name="insert[quest_player_exp]" value="100" size="5" class=""></td>
							<td><input type="text" name="insert[quest_item]"       value="100" size="5" class=""></td>
							
							<td><input type="text" name="insert[otetsudai_mag]"           value="100" size="5" class=""></td>
							<td><input type="text" name="insert[otetsudai_bit]"           value="100" size="5" class=""></td>
							<td><input type="text" name="insert[otetsudai_otetsudai_exp]" value="100" size="5" class=""></td>
							<td><input type="text" name="insert[otetsudai_player_exp]"    value="100" size="5" class=""></td>
							<td><input type="text" name="insert[otetsudai_friend_point]"  value="100" size="5" class=""></td>
							<td><input type="text" name="insert[otetsudai_item]"          value="100" size="5" class=""></td>
							
							<td></td>
							<td></td>
						</tr>
						
					</table>
					
					<button type="submit" name="submit" onclick="return confirm('変更します、よろしいですか？');">更新</button>
					
				</div>
				
			</fieldset>
		</form>
	</div>
</div>

<script>
<!--
$(function(){
	// バージョン変更の再読み込み
	$('#f2_edit_version').change(function(){
		$('#f1_edit_version').val($(this).val());
		$('#f1').submit();
	});
});
//-->
</script>
