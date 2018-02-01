
<div>
	<form method="post" id="f1">
		<input type="hidden" name="f1_submit" value="1">
		<fieldset>
			<legend>検索条件</legend>
			
			<table>
				<tr>
					<td>おてつだい区分</td>
					<td>
						<select name="type">
							<option value="">問わず</option>
							{{foreach key=k item=i from=$type_list}}
								<option value="{{$k}}" {{if $defaults.type==$k}}selected{{/if}}>{{$i}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
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
					<td>集計単位</td>
					<td>
						<label><input type="radio" name="stat_group" value="card_id"      {{if $defaults.stat_group=="card_id"     }}checked{{/if}}>card_id 単位</label><br>
						<label><input type="radio" name="stat_group" value="character_id" {{if $defaults.stat_group=="character_id"}}checked{{/if}}>character 単位</label><br>
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


	{{if $sets}}
<style>
#f3t table {
	background-color:white;
}
#f3t td.label {
	width:180px;
	white-space:nowrap;
	background-color:#eee;
}
#f3t td.total {
	width:100px;
	background-color:#eee;
}
#f3t td.combi {
	width:40px;
	background-color:#eee;
}
#f3t td.highlight{
	background-color:#ddf;
}
</style>
<script type="text/javascript">
<!--
$(function(){
	$("#f3t td.highlight-trigger").mouseenter(function(){
		var t = $(this).attr("data-highlight");
		$("#f3t td." + t).addClass("highlight");
	}).mouseleave(function(){
		var t = $(this).attr("data-highlight");
		$("#f3t td." + t).removeClass("highlight");
	});
});
//-->
</script>
	
		<div>
			<form method="post" id="f3">
				<fieldset>
					<legend>結果</legend>
					
					<table id="f3t">
						<thead>
							<tr>
								<td class="c label"> id </td>
								<td class="c total"> total </td>
								
								{{foreach item=label key=id from=$labels_rev}}
									<td class="c combi c{{$id}}">{{$id}}</td>
								{{/foreach}}
							</tr>
						</thead>
						
						<tbody>
							{{foreach item=i key=k from=$sets}}
								<tr>
									<td class="l label highlight-trigger c{{$k}}" data-highlight="c{{$k}}">
										{{$k}} : {{$labels[$k]}}
									</td>
									<td class="r total c{{$k}}">
										{{$total[$k]|number_format}}
									</td>
									
									{{foreach item=label key=id from=$labels_rev}}
											{{if $i[$id]|is_null}}
												<td>
												
												</td>
											{{else}}
												<td class="r combi c{{$id}} c{{$k}}">
														{{$i[$id]}}
												</td>
											{{/if}}
									{{/foreach}}
								</tr>
							{{/foreach}}
						</tbody>
					</table>
					
				</fieldset>
			</form>
		</div>
	{{else}}
		<div>
			<form method="post" id="f2">
				<fieldset>
					<legend>結果</legend>
					
					<table class="main2">
						
						<tr class="trh">
							<th>
								id
							</th>
							<th>
								相方を問わない総件数
							</th>
							
							{{foreach item=i from=$ranks}}
								<td style="text-align:center">{{$i}}位</td>
							{{/foreach}}
							
						</tr>
						
						{{foreach key=k item=i from=$total}}
							
							<tr class="tr{{cycle values="1,1,2,2"}}">
								<td rowspan="2">
									{{$k}} : {{$labels[$k]}}
								</td>
								<td rowspan="2" class="r">
									{{$i}}
								</td>
								
								{{foreach item=rank from=$ranks}}
									<td>
										{{if $favcomb[$k][$rank].t > 0}}
											{{$favcomb[$k][$rank].id}} : {{$favcomb[$k][$rank].label}}
										{{/if}}
									</td>
								{{/foreach}}
							</tr>
							<tr class="tr{{cycle values="1,1,2,2"}}">
								{{foreach item=rank from=$ranks}}
									<td class="r">
										{{if $favcomb[$k][$rank].t > 0}}
											{{$favcomb[$k][$rank].t|number_format}}
										{{/if}}
									</td>
								{{/foreach}}
							</tr>
							
						{{/foreach}}
					</table>
					
				</fieldset>
			</form>
		</div>
	{{/if}}
	
{{/if}}
