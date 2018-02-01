
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
				
				{{*
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
				*}}
				
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
		<input type="hidden" name="f1_submit" value="1">
		<fieldset>
			<legend>検索条件</legend>
			
			<table class="main2">
				<thead>
					<tr class="trh">
						<td class="c">アイテムID</td>
						<td class="c">コレクション種別</td>
						<td class="c">コレクションID</td>
						<td class="c">開放数</td>
					</tr>
				</thead>
				
				<tbody>
					{{foreach item=i from=$collection_itemunlock}}
						<tr class="tr{{cycle values="1,2"}}">
							<td>
								{{$i.item_id}}
							</td>
							<td>
								{{$i.collection_type}}
							</td>
							<td>
								{{$i.collection_id}}
							</td>
							<td class="r">
								{{$i.c|number_format}}
							</td>
						</tr>
					{{/foreach}}
				</tbody>
				
			</tr>
			
		</fieldset>
	</form>
</div>

{{/if}}

