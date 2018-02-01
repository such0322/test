
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
						<input type="text" name="date" value="{{$defaults.date}}" id="f1_date" class="date" size="10">
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						クエストID
					</td>
					<td>
						<select name="quest_id">
							<option value="">--------</option>
							{{foreach item=i key=k from=$quest_names}}
								<option value="{{$k}}" {{if $defaults.quest_id == $k}}selected{{/if}} >{{$k}} : {{$i}}</option>
							{{/foreach}}
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
	<script type="text/javascript" src="js/jqplugin/jquery.tablesorter.min.js"></script> 
	<div>
		<form method="post">
			<input type="hidden" name="f2_submit" value="1">
			
			<fieldset>
				<legend>ユーザ数</legend>
				
				<table>
					<tr>
						<td>開始総数</td>
						<td style="text-align:right">{{$quest_stat.begin_total|number_format}}</td>
					</tr>
					<tr>
						<td>開始者数</td>
						<td style="text-align:right">{{$quest_stat.begin_unique|number_format}}</td>
					</tr>
					<tr>
						<td>完了総数</td>
						<td style="text-align:right">{{$quest_stat.commit_total|number_format}}</td>
					</tr>
					<tr>
						<td>完了者数</td>
						<td style="text-align:right">{{$quest_stat.commit_unique|number_format}}</td>
					</tr>
				</table>
				
			</fielset>
			
			
			<fieldset>
				<legend>カード使用状況</legend>
				<script type="text/javascript">$(function(){$(#f2_cardstat).tablesorter();})</script>
				<table id="f2_cardstat" class="main2">
					<thead>
						<tr class="trh">
							<th>カードID</th>
							<th>カード</th>
							
							<th>リーダー使用人数</th>
							<th>リーダー総数</th>
							
							<th>クエスト使用人数</th>
							<th>クエスト総数</th>
							
							<th>助っ人使用人数</th>
							<th>助っ人総数</th>
						</tr>
					</thead>
					<tbody>
						{{foreach item=i from=$quest_card_stat}}
							<tr class="tr{{cycle values="1,2"}}">
								<td>{{$i.card_id}}</td>
								<td>{{$card_names[$i.card_id]}}</td>
								
								<td>{{$i.leader_unique}}</td>
								<td>{{$i.leader_total}}</td>
								
								<td>{{$i.member_unique}}</td>
								<td>{{$i.member_total}}</td>
								
								<td>{{$i.helper_unique}}</td>
								<td>{{$i.helper_total}}</td>
							</tr>
						{{/foreach}}
					</tbody>
				</table>
				
			</fielset>



{{*<!--
			<div>
				<fieldset style="display:inline-box">
					<legend>リーダー使用数</legend>
					<table class="main2">
						<tr class="trh">
							<td>カード</td>
							<td>使用者数</td>
							<td>使用総数</td>
						</tr>
						{{foreach item=i from=$leader}}
							<td>
								{{$i.card_id}} : {{$card_names[$i.card_id]}}
							</td>
							<td style="text-align:right">{{$i.unique|number_format}}</td>
							<td style="text-align:right">{{$i.total|number_format}}</td>
						{{/foreach}}
					</table>
				</fielset>
				<fieldset style="display:inline-box">
					<legend>助っ人使用数</legend>
					<table class="main2">
						<tr class="trh">
							<td>カード</td>
							<td>使用者数</td>
							<td>使用総数</td>
						</tr>
						{{foreach item=i from=$helper}}
							<td>
								{{$i.card_id}} : {{$card_names[$i.card_id]}}
							</td>
							<td style="text-align:right">{{$i.unique|number_format}}</td>
							<td style="text-align:right">{{$i.total|number_format}}</td>
						{{/foreach}}
					</table>
				</fielset>
				<fieldset style="display:inline-box">
					<legend>メンバー使用数</legend>
					<table class="main2">
						<tr class="trh">
							<td>カード</td>
							<td>使用者数</td>
							<td>使用総数</td>
						</tr>
						{{foreach item=i from=$member}}
							<td>
								{{$i.card_id}} : {{$card_names[$i.card_id]}}
							</td>
							<td style="text-align:right">{{$i.unique|number_format}}</td>
							<td style="text-align:right">{{$i.total|number_format}}</td>
						{{/foreach}}
					</table>
				</fielset>
			</div>
-->*}}



		</form>
	<div>

{{/if}}
