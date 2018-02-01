
<style>
.trh > td {
	text-align:center;
	white-space:nowrap;
}
.tr1 > .n {
	text-align:right;
}
.tr2 > .n {
	text-align:right;
}
</style>

<form method="post" id="f1">
	<input type="hidden" name="f1_submit" value="1">
	<fieldset>
		<legend>イベント選択</legend>
		<select name="event_id">
			<option value=""></option>
			{{foreach item=i from=$event_ides}}
				<option value="{{$i}}" {{if $i==$event_id}}selected{{/if}}>{{$i}}</option>
			{{/foreach}}
		</select>
		<button type="submit">選択</button>
	</fieldset>
</form>

{{if $evstat}}
	<form method="post" id="f2">
		<fieldset>
			<legend>集計結果</legend>
			<div>
				
				<table class="main2">
					<thead>
						<tr class="trh">
							{{foreach key=col item=label from=$labels}}
								{{if $label}}
									<td>
										{{$label}}
									</td>
								{{/if}}
							{{/foreach}}
						</tr>
					</thead>
					<tbody>
						
						{{foreach item=i from=$evstat}}
							<tr class="tr{{cycle values="1,1,1,1,1,1,1,1,1,1,1,2,2,2,2,2,2,2,2,2,2,2"}}">
								{{foreach key=col item=label from=$labels}}
									{{if $label}}
										<td class="n">
											{{if $col|in_array:$floatcols}}
												{{$i[$col]|string_format:"%.2f"}}
											{{else}}
												{{$i[$col]|number_format}}
											{{/if}}
										</td>
									{{/if}}
								{{/foreach}}
							</tr>
						{{/foreach}}
						
					</tbody>
				</table>
				
			</div>
		</fieldset>
	</form>
{{/if}}
