{{* 文字コード自動判別用文字列 *}}

{{if $f0_result_code}}
	<div>
		{{if $f0_result_code == 1}}
			{{$pstrres.res01}}
		{{elseif $f0_result_code == -1}}
			{{$pstrres.res02}}
		{{else}}
			{{$pstrres.res03}}<br>
			 <pre>{{$f0_error_detail|escape}}</pre>
		{{/if}}
	</div>
{{/if}}

<div align="left" style="white-space: nowrap;">
	<form method="POST" id="f0">
		<input type="hidden" name="f0_submit" value="1">
		<fieldset>
			<legend>{{$pstrres.l001}}</legend>
			<table>
				<tr>
					<td>{{*$pstrres.l101*}}プレイヤーID</td>
					<td>
						<input type="text" name="player_id" value="" size="10">
					</td>
				</tr>
				<tr>
					<td>{{$pstrres.l102}}</td>
					<td>
						<input type="text" name="penalty_days" value="" size=""> 日
					</td>
				</tr>
				<tr>
					<td>{{$pstrres.l103}}</td>
					<td>
						<textarea name="note" cols="30" rows="5"></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2"><button type="submit" onclick="return confirm('{{$pstrres.s101}}');">{{$pstrres.b101}}</button></td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>
<div align="left" style="white-space: nowrap;">
	<form method="POST" id="f1">
		<input type="hidden" name="f1_post" value="1">
		<input type="hidden" id="f1_export" name="export" value="0">
		<input type="hidden" name="f1_submit" value=1>
		<fieldset>
			<legend>{{$strres.l001}}</legend>
			<table id="main2" width="100%">
				<tbody>
					{{foreach name=select_columns key=k item=i from=$select_columns }}
						<tr class="tr{{cycle values="1,2"}}">
							<td>{{$i.name|escape}}</td>
							
							{{if $i.type == 'primary'}}
								<td align="center"><input type="hidden" name="display_cols[]" value="{{$k}}">&nbsp;</td>
							{{else}}
								<td align="center"><input type="checkbox" name="display_cols[]" value="{{$k}}" {{if $display_cols|@is_array}}{{if $k|in_array:$display_cols}}checked{{/if}}{{/if}} cid="{{$smarty.foreach.select_columns.iteration-1}}"></td>
							{{/if}}
							
							{{if $i.type == 'primary' || $i.type == 'key'}}
								<td>
									<input type="text" name="{{$k}}" value="{{$post_data.$k|escape:"html"}}" size="{{$i.size|default:"8"}}">
								</td>
							{{elseif $i.type == 'num'}}
								<td>
									<input type="text" name="{{$k}}[min]" value="{{$post_data.$k.min|escape:"html"}}" size="{{$i.size|default:"8"}}">
									　～　
									<input type="text" name="{{$k}}[max]" value="{{$post_data.$k.max|escape:"html"}}" size="{{$i.size|default:"8"}}">
								</td>
							{{elseif $i.type == 'enum'}}
								<td>
									<select name="{{$k}}[]" size="{{if $i.size|default:"4" < $master.$k|@count}}{{$i.size|default:"4"}}{{else}}{{$master.$k|@count}}{{/if}}" multiple>
										{{foreach key=kk item=ii from=$master.$k}}
											<option value="{{$kk}}" {{if $post_data.$k|@is_array}}{{if $kk|in_array:$post_data.$k}}selected{{/if}}{{/if}}>{{$kk}} : {{$ii}}</option>
										{{/foreach}}
									</select>
								</td>
							{{elseif $i.type == 'string' || $i.type == 'strkey'}}
								<td>
									<input type="text" name="{{$k}}" value="{{$post_data.$k|escape:"html"}}" size="{{$i.size|default:"20"}}">
								</td>
							{{elseif $i.type == 'datetime' || $i.type == 'unixtime'}}
								<td>
									<input type="text" id="f1_{{$k}}_begin" name="{{$k}}[begin]" value="{{if $post_data.$k.begin !== null}}{{$post_data.$k.begin|escape:"html"}}{{/if}}" class="datetime" size="20" maxlength="20" />
									　～　
									<input type="text" id="f1_{{$k}}_end" name="{{$k}}[end]" value="{{if $post_data.$k.end !== null}}{{$post_data.$k.end|escape:"html"}}{{/if}}" class="datetime" size="20" maxlength="20" />
								</td>
							{{else}}
								<td>&nbsp;</td>
							{{/if}}
							
						</tr>
					{{/foreach}}
				</tbody>
			</table>
			<br>
			<div align="center">
				<button type="submit" name="f1_submit_1" id="f1_where_update">{{$strres.b001}}</button>
				　<button type="button" name="reset" onclick="document.getElementById('f3').submit()">{{$strres.b002}}</button>
				　<button type="button" name="f1_submit_2" onclick="f1_csv_export();">{{$strres.b003}}</button>
			</div>
		</fieldset>
	</form>
	<form name="f3" method="POST" id="f3">
		<input type="hidden" name="f1_post" value=1>
		<input type="hidden" name="f2_post" value=1>
	</form>
</div>

{{if $is_f1_search}}
	
<script type="text/javascript">
<!--
$(document).ready(function(){
	
	// 検索条件の変更
	$("#f1_where_update").click(function(){
		
		f1_postdata = [
			{"name":"f1_submit","value":"1"}
			, {"name":"menukey","value":"{{$menukey}}"}
			, {"name":"include_file","value":"plugin/{{$menukey}}/penalty.php"}
		];
		$("#f1 input").each(function(){
			f1_postdata.push({"name" : $(this).attr("name"), "value" : $(this).val()});
		});
		$("#f1 select").each(function(){
			var v = $(this).val() || [];
			for (i = 0;i < v.length;i++) {
				f1_postdata.push({"name": $(this).attr("name"), "value":v[i]});
			}
		});
		
		$.get("inline.php", f1_postdata, function(data){
			// 検索条件を変えたら表を再読み込み
			$('#f2_history').flexReload();
		}, "text")
		
		return false;
	});
	
	// 表示項目の変更
	$("input[name='display_cols[]']").change(function(ev){
		$("#f2_history").flexToggleCol($(this).attr("cid"), ($(this).attr("checked") == "checked" ? true : false));
	});
	
	$('#f2_history').flexigrid(
		{
			url: 'inline.php?menukey={{$menukey}}&include_file=plugin/{{$menukey}}/penalty.php&json=1&type=history',
			title: '{{$pstrres.gf01}}', 
			nomsg: '{{$pstrres.gf02}}', 
			colModel :
			[
				{{foreach name=f2_history_colmodel key=k item=i from=$select_columns }}
					{{if $i.type == 'primary'}}
						{display:'{{$i.name}}', name:'{{$k}}', sortable:true, align:'right', width:60}
					{{else}}
						{{if $smarty.foreach.f2_history_colmodel.iteration > 0}}, {{/if}}{display:'{{$i.name}}', name:'{{$k}}', sortable:true, align:{{if $i.type=='key' || $i.type=='num'}}'right'{{else}}'left'{{/if}}, width:120{{if $post_data.display_cols|@is_array && $k|in_array:$post_data.display_cols}}{{else}}, hide:true{{/if}} }
					{{/if}}
				{{/foreach}}
			],
			
			sortname: 'log_date',
			sortorder: 'desc',
			autoload: true, 
			
			// 以下は基本固定で大丈夫なもの
			showTableToggleBtn: true, 
			procmsg: '{{$strres.g001}}',
			pagestat: '{{$strres.g002}}',
			method: 'POST',
			dataType: 'json',
			usepager: true,
			singleSelect: true, 
			rp: 50,
			useRp: true, 
			rpOptions: [10, 50, 100, 200, 500], 
			width: 'auto',
			height: 'auto'
			//,preProcess: _preProcess
		}
	);
});

-->
</script>
<div align="left" style="white-space: nowrap;">
	<div>
		<table id="f2_history">
			<thead>
			</thead>
			<tfoot>
			</tfoot>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
	
{{/if}}
