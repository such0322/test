{{* 文字コード自動判別用文字列 *}}
<script language="javascript">
	function f1_csv_export() {
		document.getElementById("f1").action = "inline.php?include_file=plugin/{{$menukey}}/item_move.php&menukey={{$menukey}}";
		document.getElementById("f1_export").value = "1";
		document.getElementById("f1").submit();
		document.getElementById("f1").action = "";
		document.getElementById("f1_export").value = "0";
	}
</script>
<div align="left" style="white-space: nowrap;">
	<form method="POST" id="f1">
		<input type="hidden" name="f1_post" value="1">
		<input type="hidden" id="f1_export" name="export" value="0">
		<fieldset>
			<legend>{{$strres.l001}}</legend>
			<table id="main2" width="100%">
				<tbody>
					{{foreach name=select_columns key=k item=i from=$select_columns }}
						<tr class="tr{{cycle values="1,2"}}">
							<td>{{$i.name}}</td>
							
							{{if $i.type == 'primary'}}
								<td align="center"><input type="hidden" name="display_cols[]" value="{{$k}}">&nbsp;</td>
							{{else}}
								<td align="center"><input type="checkbox" name="display_cols[]" value="{{$k}}" {{if $display_cols|@is_array}}{{if $k|in_array:$display_cols}}checked{{/if}}{{/if}}></td>
							{{/if}}
							
							{{if $i.type == 'primary' || $i.type == 'key' || $i.type == 'strkey'}}
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
											<option value="{{$kk}}" {{if $post_data.$k|@is_array}}{{if $kk|in_array:$post_data.$k:1}}selected{{/if}}{{/if}}>{{$kk}} : {{$ii}}</option>
										{{/foreach}}
									</select>
								</td>
							{{elseif $i.type == 'string'}}
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
									<input type="text" id="f1_{{$k}}_begin" name="{{$k}}[begin]" value="{{if $post_data.$k.begin}}{{$post_data.$k.begin|escape:"html"}}{{else}}{{$default_begin_date|date_format:"%Y-%m-%d"}} 00:00:00{{/if}}" class="datetime" size="20" maxlength="20" />
									　～　
									<input type="text" id="f1_{{$k}}_end" name="{{$k}}[end]" value="{{if $post_data.$k.end}}{{$post_data.$k.end|escape:"html"}}{{else}}{{$default_end_date|date_format:"%Y-%m-%d"}} 00:00:00{{/if}}" class="datetime" size="20" maxlength="20" />
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
				<button type="submit">{{$strres.b001}}</button>
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
	
	
<script language="javascript">
function toggle_select(tr, id_name) {
	id_name = id_name || 'tr_selected';
	
	if (tr.className == 'trselect') {
		tr.className = tr.getAttribute('org_class');
		tr.setAttribute('id', '');
	}
	else {
		var obj = document.getElementById(id_name);
		if (obj) {
			obj.className = obj.getAttribute('org_class');
			obj.setAttribute('id', '');
		}
		tr.setAttribute('org_class', tr.className);
		tr.setAttribute('id', id_name);
		tr.setAttribute('className', 'trselect');
	}
}
function f2_submit(offset, limit, order, desc) {
	document.getElementById("f2_offset").value = offset;
	document.getElementById("f2_limit").value = limit;
	document.getElementById("f2_order").value = order;
	document.getElementById("f2_desc").value = desc;
	
	document.getElementById("f2").submit();
}
function f2_toggle_div(show) {
	
	document.getElementById("f2_tab_history").style.display = "none";
	document.getElementById("f2_tab_ranking").style.display = "none";
	document.getElementById("f2_tab_stat").style.display = "none";
	document.getElementById(show).style.display = "block";
}
function show_graph() {
	var elms = document.getElementsByTagName("span");
	for (i = 0;i < elms.length;i++) {
		if (elms[i].name == "graph") {
			elms[i].innerHTML = '<img src="images/frame_base_r3_c5.gif" width="' + elms[i].getAttribute("graph_length") + '" height="8" alt="">';
		}
	}
}
function hide_graph() {
	var elms = document.getElementsByTagName("span");
	for (i = 0;i < elms.length;i++) {
		if (elms[i].name == "graph") {
			elms[i].innerHTML = '';
		}
	}
}
</script>
<br>
<div align="left" style="white-space: nowrap;">
	<form name="f2" method="POST" id="f2">
		<fieldset>
			<legend>{{$strres.l001}}</legend>
			<input type="hidden" name="f2_post" value=1>
			<input type="hidden" name="offset" id="f2_offset" value="{{$paging.offset}}">
			<input type="hidden" name="limit" id="f2_limit" value="{{$paging.limit}}">
			<input type="hidden" name="order" id="f2_order" value="{{$paging.order}}">
			<input type="hidden" name="desc" id="f2_desc" value="{{$paging.desc}}">
			<div width="100%" align="right">
				
{{*
				{{foreach name=f2_paging key=k item=i from=$paging.pages}}
					{{if $k == $paging.offset}}
						<big>{{$i}}</big>
					{{else}}
						<a href="javascript:f2_submit({{$k}}, {{$paging.limit}}, '{{$paging.order}}', '{{$paging.desc}}');">{{$i}}</a>
					{{/if}}
				{{/foreach}}
				
				　({{$paging.begin}} - {{$paging.end}} /{{$paging.max}})　
*}}
				{{if $paging.prev >= 0}}
					<a href="javascript:f2_submit({{$paging.prev}}, {{$paging.limit}}, '{{$paging.order}}', '{{$paging.desc}}');">prev</a>
				{{/if}}
				　
				{{if $paging.next >= 0}}
					<a href="javascript:f2_submit({{$paging.next}}, {{$paging.limit}}, '{{$paging.order}}', '{{$paging.desc}}');">next</a>
				{{/if}}
				　
				<select name="limit" onchange="f2_submit(0, this.value, '{{$paging.order}}', '{{$paging.desc}}');">
					{{foreach item=i from=$paging.limit_list}}
						<option value="{{$i}}" {{if $i == $paging.limit}}selected{{/if}}>{{$i}}</option>
					{{/foreach}}
				</select>
			</div>
			<table id="main2" name="history_table" width="100%">
				<thead>
					<tr id="trh">
						{{foreach key=k item=i from=$f2_trh}}
							<td align="center">
								<div style="white-space: nowrap;">
									{{$i.name}}
									<a href="javascript:f2_submit({{$paging.offset}}, {{$paging.limit}}, '{{$k}}', '')"    >{{if $paging.order == $k && $paging.desc == ''    }}▲{{else}}△{{/if}}</a>
									<a href="javascript:f2_submit({{$paging.offset}}, {{$paging.limit}}, '{{$k}}', 'DESC')">{{if $paging.order == $k && $paging.desc == 'DESC'}}▼{{else}}▽{{/if}}</a>
								</div>
							</td>
						{{/foreach}}
					</tr>
				</thead>
				<tbody>
					{{foreach name=f2_tr item=i from=$f2_tr}}
						<tr class="tr{{cycle values="1,2"}}">
							{{foreach item=ii from=$i}}
								<td>
									{{$ii}}
								</td>
							{{/foreach}}
						</tr>
					{{/foreach}}
				</tbody>
			</table>
		</fieldset>
	</form>
</div>
	
	
{{/if}}
