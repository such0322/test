<script language="javascript">
	function f1_csv_export() {
		document.getElementById("f1").action = "inline.php?include_file=plugin/{{$menukey}}/player_stat.php&menukey={{$menukey}}";
		document.getElementById("f1_export").value = "1";
		document.getElementById("f1").submit();
		document.getElementById("f1").action = "";
		document.getElementById("f1_export").value = "0";
	}
</script>
<div align="left" style="white-space: nowrap;">
	<form method="POST" id="f1">
		<input type="hidden" id="f1_export" name="export" value="0">
		<input type="hidden" name="f1_submit" value=1>
		<fieldset>
			<legend>{{$strres.l001}}</legend>
			<table id="main2" width="100%">
				<thead>
					<tr class="trh">
						<th>項目</th>
						<th>集計対象か</th>
						<th>検索条件</th>
					</tr>
				</thead>
				<tbody>
					{{foreach name=select_columns key=k item=i from=$select_columns }}
						<tr class="tr{{cycle values="1,2"}}">
							<td align="right">{{$i.name|escape}}</td>
							
							{{if $stat_columns[$k]}}
								<td align="center"><input type="checkbox" name="stat_cols[]" value="{{$k}}" {{if $stat_cols|@is_array}}{{if $k|in_array:$stat_cols}}checked{{/if}}{{/if}} cid="{{$smarty.foreach.select_columns.iteration-1}}"></td>
							{{else}}
								<td>&nbsp;</td>
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
									<input type="text" id="f1_{{$k}}_begin" name="{{$k}}[begin]" value="{{if $post_data.$k.begin !== null}}{{$post_data.$k.begin|escape:"html"}}{{else}}{{/if}}" class="datetime" size="20" maxlength="20" />
									　～　
									<input type="text" id="f1_{{$k}}_end" name="{{$k}}[end]" value="{{if $post_data.$k.end !== null}}{{$post_data.$k.end|escape:"html"}}{{else}}{{/if}}" class="datetime" size="20" maxlength="20" />
									{{if $is_change_tz}}
										<input type="checkbox" name="is_jst_show" class="jst_show_check" id="f1_{{$k}}_jst" target="{{$k}}"><label for="f1_{{$k}}_jst">{{$timezone_label}}{{$strres.deshow}}</label>
									{{/if}}
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
				　<button type="submit" id="f1_where_update">{{$strres.b001}}</button>
				　<button type="button" name="reset" onclick="document.getElementById('f3').submit()">{{$strres.b002}}</button>
				　<button type="button" name="f1_submit_2" onclick="f1_csv_export();">{{$strres.b003}}</button>
			</div>
		</fieldset>
	</form>
	<form name="f3" method="POST" id="f3">
		<input type="hidden" name="f1_submit" value="1">
		<input type="hidden" name="f2_submit" value="1">
		<input type="hidden" name="f3_submit" value="1">
	</form>
</div>

<script type="text/javascript">
<!--
$(document).ready(function(){
	
	// 検索条件の変更
	$("#f1_where_update").click(function(){
		
		var f1_postdata = [
			{"name":"f1_submit","value":"1"}
			, {"menukey":"f1_submit","value":"{{$menukey}}"}
			, {"name":"include_file","value":"plugin/{{$menukey}}/player_stat.php"}
		];
		$("#f1 input:text").each(function(){
			f1_postdata.push({"name" : $(this).attr("name"), "value" : $(this).val()});
		});
		$("#f1 input:checked").each(function(){
			f1_postdata.push({"name" : $(this).attr("name"), "value" : $(this).val()});
		});
		$("#f1 select").each(function(){
			var v = $(this).val() || [];
			for (i = 0;i < v.length;i++) {
				f1_postdata.push({"name": $(this).attr("name"), "value":v[i]});
			}
		});
		
		$.get("inline.php?menukey={{$menukey}}&include_file=plugin/{{$menukey}}/player_stat.php", f1_postdata, function(data){
			// 検索条件を変えたら表を再読み込み
			$('#f2_history').flexReload();
		}, "text")
		
		return false;
	});
	
	// 表示項目の変更
	$("input[name='stat_cols[]']").change(function(ev){
		$("#f2_history").flexToggleCol($(this).attr("cid"), ($(this).attr("checked") == "checked" ? true : false));
		//$("#f1_where_update").click();
	});
	
	$('#f2_history').flexigrid(
		{
			url: 'inline.php?menukey={{$menukey}}&include_file=plugin/{{$menukey}}/player_stat.php&json=1&type=stat',
			title: 'キャラ検索', 
			nomsg: 'not found', 
			colModel :
			[
				{{foreach name=f2_history_colmodel key=k item=i from=$select_columns }}
					{display:'{{$i.name}}', name:'{{$k}}', sortable:true, align:{{if $i.type=='key' || $i.type=='num'}}'right'{{else}}'left'{{/if}}, width:{{if $i.type=='key' || $i.type=='num'}}60{{elseif $i.type=='datetime'}}120{{else}}200{{/if}}, hide:true  }, 
				{{/foreach}}
				{display:'', name:'blank', sortable:false, align:'right', width:40}, 
				{display:'合計', name:'s', sortable:true,  align:'right', width:60}
			],
			
			sortname: '{{$order}}',
			sortorder: '{{$desc}}',
			autoload: false, 
			
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
			,preProcess: function(data){
				//$.each(data.rows, function(i, val){
				//	val.cell[2] = '<a href="?menukey={{$menukey}}&include_file={{$menukey}}/chara_detail.php&f1_submit=1&chara_id=' + val.cell[2] + '">' + val.cell[2] + '</a>';
				//});
				return data;
			}
		}
	);
});

//-->
</script>
<div align="left" style="white-space: nowrap;">
	<form>
		<fieldset>
			<legend>集計結果</legend>
			<table id="f2_history">
				<thead>
				</thead>
				<tfoot>
				</tfoot>
				<tbody>
				</tbody>
			</table>
		</fieldset>
	</form>
</div>
