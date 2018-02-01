
<div>
	<form method="post">
		<input type="hidden" name="f1_submit" value="1">
		<fieldset>
			<legend>条件指定</legend>
			<table>
				<tr>
					<td style="text-align:right">
						日付
					</td>
					<td>
						<table>
							<tr>
								<td><input type="text" name="date_begin" value="{{$defaults.date_begin}}" id="f1_date_begin" class="date" size="10"></td>
								<td>　～　</td>
								<td><input type="text" name="date_end" value="{{$defaults.date_end}}" id="f1_date_end" class="date" size="10"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						集計対象
					</td>
					<td>
						<select name="col">
							{{foreach item=i from=$cols}}
								<option value="{{$i}}" {{if $i==$defaults.col}}selected{{/if}}>{{$i}}</option>
							{{/foreach}}
							<option value="leader">リーダー</option>
							<option value="member">メンバー</option>
							<option value="helper">助っ人</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center">
						<button type="submit" name="submit">送信</button>
					<td>
				</tr>
			</table>
		</fielset>
	</form>
</div>

{{if $f1_submit}}
	<div>
		<form method="post">
			<input type="hidden" name="f2_submit" value="1">
			
			<fieldset>
				<legend>カード使用状況</legend>
				<table id="f2_cardstat" class="main2">
					<thead>
						<tr class="trh">
							<th>カードID</th>
							
							{{foreach key=quest_id item=quest_name from=$active_quests}}
								<th>{{$quest_id}}:{{$quest_name}}</th>
							{{/foreach}}
						</tr>
					</thead>
					<tbody>
						{{foreach key=card_id item=i from=$quest_card_stat}}
							<tr class="tr{{cycle values="1,2"}}">
								<td>{{$card_id}} : {{$card_names[$card_id]}}</td>
								
								{{foreach key=quest_id item=quest_name from=$active_quests}}
									<td style="text-align:right">
										{{if $i[$quest_id]}}
											{{$i[$quest_id]|number_format}}
										{{else}}
											
										{{/if}}
									</td>
								{{/foreach}}
							</tr>
						{{/foreach}}
					</tbody>
				</table>
				
			</fielset>
		</form>
	<div>

{{/if}}
