<script language="javascript">
<!--
	function f1_csv_export() {
		document.getElementById("f1").action = "inline.php?include_file=plugin/{{$menukey}}/gacha.php&menukey={{$menukey}}";
		document.getElementById("f1_export").value = "1";
		document.getElementById("f1").submit();
		document.getElementById("f1").action = "";
		document.getElementById("f1_export").value = "0";
	}
//-->
</script>
<div align="left" style="white-space: nowrap;">
	<form method="POST" id="f1">
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
{{*
								<td>
									<input type="text" name="{{$k}}[begin_year]" value="{{$post_data.$k.begin_year|escape:"html"}}" size=4 maxlength=4>-<input type="text" name="{{$k}}[begin_mon]" value="{{$post_data.$k.begin_mon|escape:"html"}}" size=2 maxlength=2>-<input type="text" name="{{$k}}[begin_day]" value="{{$post_data.$k.begin_day|escape:"html"}}" size=2 maxlength=2>
									<input type="text" name="{{$k}}[begin_hour]" value="{{$post_data.$k.begin_hour|escape:"html"}}" size=2 maxlength=2>:<input type="text" name="{{$k}}[begin_min]" value="{{$post_data.$k.begin_min|escape:"html"}}" size=2 maxlength=2>:<input type="text" name="{{$k}}[begin_sec]" value="{{$post_data.$k.begin_sec|escape:"html"}}" size=2 maxlength=2>
									　～　
									<input type="text" name="{{$k}}[end_year]" value="{{$post_data.$k.end_year|escape:"html"}}" size=4 maxlength=4>-<input type="text" name="{{$k}}[end_mon]" value="{{$post_data.$k.end_mon|escape:"html"}}" size=2 maxlength=2>-<input type="text" name="{{$k}}[end_day]" value="{{$post_data.$k.end_day|escape:"html"}}" size=2 maxlength=2>
									<input type="text" name="{{$k}}[end_hour]" value="{{$post_data.$k.end_hour|escape:"html"}}" size=2 maxlength=2>:<input type="text" name="{{$k}}[end_min]" value="{{$post_data.$k.end_min|escape:"html"}}" size=2 maxlength=2>:<input type="text" name="{{$k}}[end_sec]" value="{{$post_data.$k.end_sec|escape:"html"}}" size=2 maxlength=2>
								</td>
*}}
								<td>
									<input type="text" id="f1_{{$k}}_begin" name="{{$k}}[begin]" value="{{if $post_data.$k.begin !== null}}{{$post_data.$k.begin|escape:"html"}}{{else}}{{$default_begin_date|date_format:"%Y-%m-%d"}} 00:00:00{{/if}}" class="datetime" size="20" maxlength="20" />
									　～　
									<input type="text" id="f1_{{$k}}_end" name="{{$k}}[end]" value="{{if $post_data.$k.end !== null}}{{$post_data.$k.end|escape:"html"}}{{else}}{{$default_end_date|date_format:"%Y-%m-%d"}} 00:00:00{{/if}}" class="datetime" size="20" maxlength="20" />
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
				{{$strres.l005}}：<select name="stat_unit">
				{{foreach name=f1_stat_units item=i key=k from=$stat_units}}
					<option value="{{$k}}" {{if $k == $stat_unit}}selected{{/if}}>{{$i.name}}</option>
				{{/foreach}}
				</select>
				　<button type="button" id="f1_where_update">{{$strres.b001}}</button>
				　<button type="button" name="reset" onclick="document.getElementById('f3').submit()">{{$strres.b002}}</button>
				　<button type="button" name="f1_submit_2" onclick="f1_csv_export();">{{$strres.b003}}</button>
			</div>
		</fieldset>
	</form>
	<form name="f3" method="POST" id="f3">
		<input type="hidden" name="f3_submit" value="1">
	</form>
</div>

<script type="text/javascript">
<!--
$(document).ready(function(){
	$("#tabs").tabs({
		select: function(event, ui) {
			// flexigrid の都合で div/div/table に書き換わる
			$("#" + $(ui.panel).children().children(".bDiv").children("table").attr("id")).flexReload();
			//$("#" + $(ui.panel).attr('target')).flexReload();  ↑が使えなかったら素直にタブの所で target に宛先指定する
		}
	});
	
	// 検索条件の変更
	$("#f1_where_update").click(function(){
		
		// $("#f1").serialize() でも簡単に取れる
		
		var f1_postdata = [
			{"name":"f1_submit","value":"1"}
			, {"menukey":"f1_submit","value":"{{$menukey}}"}
			, {"name":"include_file","value":"plugin/{{$menukey}}/gacha.php"}
		];
		$("#f1 input:text").each(function(){
			f1_postdata.push({"name" : $(this).attr("name"), "value" : $(this).val()});
		});
		$("#f1 input:checked").each(function(){
			f1_postdata.push({"name" : $(this).attr("name"), "value" : $(this).val()});
		});
		$("#f1 select").each(function(){
			var v = $(this).val() || [];
			if ($(this).attr("multiple")) {
				var v = $(this).val() || [];
				for (i = 0;i < v.length;i++) {
					f1_postdata.push({"name": $(this).attr("name"), "value":v[i]});
				}
			}
			else {
				f1_postdata.push({"name": $(this).attr("name"), "value":$(this).val()});
			}
		});
		
		$.get("inline.php?menukey={{$menukey}}&include_file=plugin/{{$menukey}}/gacha.php", f1_postdata, function(data){
			// 検索条件を変えたら表を再読み込み
			if ($('#tabs-1').is(':visible')) {$('#f2_history').flexReload();}
			if ($('#tabs-2').is(':visible')) {$('#f2_ranking').flexReload();}
			if ($('#tabs-3').is(':visible')) {$('#f2_stat').flexReload();}
			
		});
	});
	
	// 表示項目の変更
	$("input[name='display_cols[]']").change(function(ev){
		$("#f2_history").flexToggleCol($(this).attr("cid"), ($(this).attr("checked") == "checked" ? true : false));
	});
	
	$('#f2_history').flexigrid(
		{
			url: 'inline.php?menukey={{$menukey}}&include_file=plugin/{{$menukey}}/gacha.php&json=1&type=history',
			title: '{{$pstrres.gh01}}', 
			nomsg: '{{$pstrres.gh02}}', 
			colModel :
			[
				{{foreach name=f2_history_colmodel key=k item=i from=$select_columns }}
					{{if $i.type == 'primary'}}
						{display:'{{$i.name}}', name:'{{$k}}', sortable:true, align:'right', width:60}
					{{elseif $post_data.display_cols|@is_array && $k|in_array:$post_data.display_cols}}
						{{if $smarty.foreach.f2_history_colmodel.iteration > 0}}, {{/if}}{display:'{{$i.name}}', name:'{{$k}}', sortable:true, align:{{if $i.type=='key' || $i.type=='num'}}'right'{{else}}'left'{{/if}}, width:120 }
					{{else}}
						{{if $smarty.foreach.f2_history_colmodel.iteration > 0}}, {{/if}}{display:'{{$i.name}}', name:'{{$k}}', sortable:true, align:{{if $i.type=='key' || $i.type=='num'}}'right'{{else}}'left'{{/if}}, width:120, hide:true }
					{{/if}}
				{{/foreach}}
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
			//,preProcess: _preProcess
		}
	);
	
	$('#f2_ranking').flexigrid(
		{
			url: 'inline.php?menukey={{$menukey}}&include_file=plugin/{{$menukey}}/gacha.php&json=1&type=ranking',
			title: '{{$pstrres.gr01}}', 
			nomsg: '{{$pstrres.gr02}}', 
			colModel :
			[
				  {display: '{{$pstrres.gr03}}', name : 'name',  width : 140, sortable : false, align: 'center'}
				, {display: '{{$pstrres.gr04}}', name : 'count', width : 180, sortable : false, align: 'left'}
				, {display: '{{$pstrres.gr05}}', name : 'sum',   width : 120, sortable : false, align: 'right'}
			],
			autoload: false, 
			
			showTableToggleBtn: true, 
			procmsg: '{{$strres.g001}}',
			pagestat: '{{$strres.g002}}',
			method: 'POST',
			dataType: 'json',
			usepager: true,
			singleSelect: true, 
			useRp: false, 
			width: 'auto',
			height: 'auto'
		}
	);
	
	$('#f2_stat').flexigrid(
		{
			url: 'inline.php?menukey={{$menukey}}&include_file=plugin/{{$menukey}}/gacha.php&json=1&type=stat',
			title: '{{$pstrres.gs01}}', 
			nomsg: '{{$pstrres.gs02}}', 
			colModel :
			[
				  {display: '{{$pstrres.gs03}}', name : 'log_type', width : 200, sortable : false, align: 'left'}
				, {display: '{{$pstrres.gs04}}', name : 'sales',    width : 120, sortable : false, align: 'right'}
//				, {display: '{{$pstrres.gs05}}', name : 'sales',    width : 300, sortable : false, align: 'left'}
			],
			autoload: false, 
			
			showTableToggleBtn: true, 
			procmsg: '{{$strres.g001}}',
			pagestat: '{{$strres.g002}}',
			method: 'POST',
			dataType: 'json',
			usepager: true,
			singleSelect: true, 
			useRp: false, 
			width: 'auto',
			height: 'auto'
			, preProcess: function (data) {
				$.each( data.rows, function(i, val) {
					val.cell[2] = '<img src="images/g/a.gif" width="' + val.cell[2] + '" height="12" />';
				});
				return data;
			}
		}
	);
});

function f2_toggle_div(show) {
	
	document.getElementById("f2_tab_history").style.display = "none";
	document.getElementById("f2_tab_ranking").style.display = "none";
	document.getElementById("f2_tab_stat").style.display = "none";
	document.getElementById(show).style.display = "block";
}
-->
</script>

<div align="left" style="white-space: nowrap;">
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1">{{$strres.l002}}</a></li>
			<li><a href="#tabs-2">{{$strres.l003}}</a></li>
			<li><a href="#tabs-3">{{$strres.l004}}</a></li>
		</ul>
		<div id="tabs-1">
			<table id="f2_history">
				<thead>
				</thead>
				<tfoot>
				</tfoot>
				<tbody>
				</tbody>
			</table>
		</div>
		<div id="tabs-2">
			<table id="f2_ranking">
				<thead>
				</thead>
				<tfoot>
				</tfoot>
				<tbody>
				</tbody>
			</table>
		</div>
		<div id="tabs-3">
			<table id="f2_stat">
				<thead>
				</thead>
				<tfoot>
				</tfoot>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>
