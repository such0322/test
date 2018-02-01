
<div>
	<form method="post" id="f1">
		<input type="hidden" name="f1_submit" value="1">
		<fieldset>
			<legend>検索条件</legend>
			
			<table>
				<tr>
					<td>期間</td>
					<td>
						<div style="display:inline-block">
							<input type="text" name="log_date_begin" value="{{$defaults.log_date_begin}}" size="10" class="date">
						</div>
						<div style="display:inline-block">
							～
						</div>
						<div style="display:inline-block">
							<input type="text" name="log_date_end" value="{{$defaults.log_date_end}}" size="10" class="date">
						</div>
					</td>
				</tr>
				<tr>
					<td>コレクション区分</td>
					<td>
						<select name="collection_group">
							<option value="">問わず</option>
							{{foreach key=k item=i from=$collection_groups}}
								<option value="{{$k}}" {{if $defaults.collection_group==$k}}selected{{/if}}>{{$k}} : {{$i}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:center" colspan="2">
						<button type="submit" name="submit">送信</button>
					</td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>




{{if $f1_submit}}

<style>
td.l {
	text-align:left;
}
td.c {
	text-align:center;
}
td.r {
	text-align:right;
}
</style>

	<div>
		<form method="post" id="f2">
			<fieldset>
				<legend>結果</legend>
				
				<table class="main2">
					
					<tr class="trh">
						<th>
							キャラクター
						</th>
						<th>
							総数
						</th>
						
						{{foreach item=cgn key=cg from=$collection_groups}}
							<th style="text-align:center">{{$cg}} : {{$cgn}}</th>
						{{/foreach}}
						
						<th>
							グラフ
						</th>
					</tr>
					
					{{foreach key=k item=i from=$cc}}
						
						<tr class="tr{{cycle name="cctr" values="1,2"}}">
							<td>
								{{$k}} : {{$character_names[$k]}}
							</td>
							<td class="r">
								{{$i.total}}
							</td>
							
							{{foreach item=cgn key=cg from=$collection_groups}}
								<td style="text-align:right">
									{{$i[$cg]}}
								</td>
							{{/foreach}}
							
							<td>
								{{foreach item=cgn key=cg from=$collection_groups}}<img src="images/g/{{cycle values="a,b,c,d,e"}}.gif" width="{{$ccg[$k][$cg]*300}}" height="8">{{/foreach}}
							</td>
						</tr>
					{{/foreach}}
				</table>
				
			</fieldset>
		</form>
	</div>
{{/if}}

