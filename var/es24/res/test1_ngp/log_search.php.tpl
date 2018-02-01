<script language="javascript">
<!--
	function f1_csv_export() {
		document.getElementById("f1").action = "inline.php?include_file=plugin/{{$menukey}}/log_search.php&menukey={{$menukey}}";
		document.getElementById("f1_export").value = "1";
		document.getElementById("f1").submit();
		document.getElementById("f1").action = "";
		document.getElementById("f1_export").value = "0";
	}
//-->
</script>
<div align="left" style="white-space: nowrap;">
	<form method="POST" id="f0">
		<input type="hidden" name="f0_submit" value="1">
		<fieldset>
			<legend>{{$pstrres.f0001}}</legend>
			<select name="current_log">
				<option value="">--------</option>
				{{foreach key=k item=i from=$loglist}}
					<option value="{{$k}}" {{if $k == $current_log}}selected{{/if}}>{{$i.name|escape}}</option>
				{{/foreach}}
			</select>
			<button type="submit" name="submit">{{$pstrres.f0002}}</button>
		</fieldset>
	</form>
</div>

{{if $select_columns}}
<div align="left" style="white-space: nowrap;">
	<form method="POST" id="f1">
		<input type="hidden" name="f1_post" value="1">
		<input type="hidden" id="f1_export" name="export" value="0">
		<input type="hidden" name="f1_submit" value=1>
		<fieldset>
			<legend>{{$strres.l001}}</legend>
			<table id="main2" width="100%">
				<tbody>
{{*
					<tr class="tr1">
						<td>{{$pstrres.f1001}}</td>
						<td>&nbsp;</td>
						<td>
							{{if $env.web_log_mnt}} <input type="radio" id="f1_logdir_web_cur"  name="logdir" value="web_cur"  {{if $logdir == "web_cur" }}checked{{/if}}><label for="f1_logdir_web_cur" >{{$pstrres.f1ld1}}</label>　{{/if}}
							{{if $env.web_log_bak}} <input type="radio" id="f1_logdir_web_bak"  name="logdir" value="web_bak"  {{if $logdir == "web_bak" }}checked{{/if}}><label for="f1_logdir_web_bak" >{{$pstrres.f1ld2}}</label>　{{/if}}
							{{if $env.game_log_mnt}}<input type="radio" id="f1_logdir_game_cur" name="logdir" value="game_cur" {{if $logdir == "game_cur"}}checked{{/if}}><label for="f1_logdir_game_cur">{{$pstrres.f1ld3}}</label>　{{/if}}
							{{if $env.game_log_bak}}<input type="radio" id="f1_logdir_game_bak" name="logdir" value="game_bak" {{if $logdir == "game_bak"}}checked{{/if}}><label for="f1_logdir_game_bak">{{$pstrres.f1ld4}}</label>　{{/if}}

							<input type="radio" id="f1_logdir_web_cur"  name="logdir" value="web_cur"  {{if $logdir == "web_cur" }}checked{{/if}}><label for="f1_logdir_web_cur" >{{$pstrres.f1ld1}}</label>　
							<input type="radio" id="f1_logdir_web_bak"  name="logdir" value="web_bak"  {{if $logdir == "web_bak" }}checked{{/if}}><label for="f1_logdir_web_bak" >{{$pstrres.f1ld2}}</label><br>
							<input type="radio" id="f1_logdir_game_cur" name="logdir" value="game_cur" {{if $logdir == "game_cur"}}checked{{/if}}><label for="f1_logdir_game_cur">{{$pstrres.f1ld3}}</label>　
							<input type="radio" id="f1_logdir_game_bak" name="logdir" value="game_bak" {{if $logdir == "game_bak"}}checked{{/if}}><label for="f1_logdir_game_bak">{{$pstrres.f1ld4}}</label><br>

							<input type="radio" id="f1_logdir_web_bak"  name="logdir" value="web_bak"  {{if $logdir == "web_bak" }}checked{{/if}}><label for="f1_logdir_web_bak" >{{$pstrres.f1ld2}}</label>　
							<input type="radio" id="f1_logdir_game_bak" name="logdir" value="game_bak" {{if $logdir == "game_bak"}}checked{{/if}}><label for="f1_logdir_game_bak">{{$pstrres.f1ld4}}</label>
						</td>
					</tr>
*}}
					<tr class="tr2">
						<td>{{$pstrres.f0002}}</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" name="logdate" value="{{$logdate}}" id="f2_logdate" size="10" class="date">
						</td>
					</tr>
					{{foreach name=select_columns key=k item=i from=$select_columns }}
						<tr class="tr{{cycle values="1,2"}}">
							<td>{{$i.name|escape}}</td>
							
							{{if $k == "1"}}
								<td align="center"><input type="hidden" name="display_cols[]" value="{{$k}}">&nbsp;</td>
							{{else}}
								<td align="center"><input type="checkbox" name="display_cols[]" value="{{$k}}" {{if $display_cols|@is_array}}{{if $k|in_array:$display_cols}}checked{{/if}}{{/if}}></td>
							{{/if}}
							
							{{if $i.type == 'primary' || $i.type == 'key'}}
								<td>
									<input type="text" name="{{$k}}" value="{{$post_data.$k|escape:"html"}}" size="{{$i.size|default:"8"}}">
								</td>
							{{elseif $i.type == 'num' || $i.type == 'INTEGER'}}
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
							{{elseif $i.type == 'string' || preg_match('/^(VAR)?CHAR/', $i.type)}}
								<td>
									<input type="text" name="{{$k}}" value="{{$post_data.$k|escape:"html"}}" size="{{$i.size|default:"20"}}">
								</td>
							{{elseif $i.type == 'datetime' || $i.type == 'unixtime' || $i.type == 'DATETIME'}}
								<td>
									<input type="text" id="f1_{{$k}}_begin" name="{{$k}}[begin]" value="{{if $post_data.$k.begin !== null}}{{$post_data.$k.begin|escape:"html"}}{{else}}{{/if}}" class="datetime" size="20" maxlength="20" />
									　～　
									<input type="text" id="f1_{{$k}}_end" name="{{$k}}[end]" value="{{if $post_data.$k.end !== null}}{{$post_data.$k.end|escape:"html"}}{{else}}{{/if}}" class="datetime" size="20" maxlength="20" />
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
				<button type="submit" name="f1_submit_1">{{$strres.b001}}</button>
				　<button type="button" name="reset" onclick="document.getElementById('f3').submit()">{{$strres.b002}}</button>
{{*
				　<button type="button" name="f1_submit_2" onclick="f1_csv_export();">{{$strres.b003}}</button>
*}}
			</div>
		</fieldset>
	</form>
	<form name="f3" method="POST" id="f3">
		<input type="hidden" name="f1_post" value="1">
		<input type="hidden" name="f2_post" value="1">
	</form>
</div>
{{/if}}

{{if $is_f1_search}}

<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#f2_history').flexigrid(
		{
			url: 'inline.php?menukey={{$menukey}}&include_file=plugin/{{$menukey}}/log_search.php&json=1&type=history',
			title: '{{$pstrres.lh01}}', 
			nomsg: '{{$pstrres.lh02}}', 
			colModel :
			[
				{{foreach name=f2_history_colmodel key=k item=i from=$select_columns }}
					{{if $k == 1}}
						{display:'{{$i.name}}', name:'{{$k}}', sortable:false, align:{{if $i.type=='key' || $i.type=='num'}}'right'{{else}}'left'{{/if}}, width:120 }
					{{elseif $post_data.display_cols|@is_array && $k|in_array:$post_data.display_cols}}
						, {display:'{{$i.name}}', name:'{{$k}}', sortable:false, align:{{if $i.type=='key' || $i.type=='num'}}'right'{{else}}'left'{{/if}}, width:120 }
					{{/if}}
				{{/foreach}}
			],
			
			//sortname: 'log_date',
			//sortorder: 'desc',
			autoload: true, 
			
			// 以下は基本固定で大丈夫なもの
			showTableToggleBtn: true, 
			procmsg: '{{$strres.g001}}',
			pagestat: '{{$strres.g002}}',
			method: 'GET',
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

{{*
<div align="left" style="white-space: nowrap;">
	<form name="f2" method="POST" id="f2">
		<fieldset>
			<legend>{{$strres.l002}}</legend>
			<input type="hidden" name="f2_post" value=1>
			<input type="hidden" name="offset" id="f2_offset" value="{{$paging.offset}}">
			<input type="hidden" name="limit" id="f2_limit" value="{{$paging.limit}}">
			<input type="hidden" name="order" id="f2_order" value="{{$paging.order}}">
			<input type="hidden" name="desc" id="f2_desc" value="{{$paging.desc}}">
			<div width="100%" align="right">

				{{foreach name=f2_paging key=k item=i from=$paging.pages}}
					{{if $k == $paging.offset}}
						<big>{{$i}}</big>
					{{else}}
						<a href="javascript:f2_submit({{$k}}, {{$paging.limit}}, '{{$paging.order}}', '{{$paging.desc}}');">{{$i}}</a>
					{{/if}}
				{{/foreach}}
				
				　({{$paging.begin}} - {{$paging.end}} /{{$paging.max}})　
				
				<select name="limit" onchange="f2_submit(0, this.value, '{{$paging.order}}', '{{$paging.desc}}');">
					{{foreach item=i from=$paging.limit_list}}
						<option value="{{$i}}" {{if $i == $paging.limit}}selected{{/if}}>{{$i}}</option>
					{{/foreach}}
				</select>

			</div>
			<table id="main2" name="history_table" width="100%">
				<thead>
					<tr id="trh">
						{{foreach name=f2_history_colmodel key=k item=i from=$select_columns }}
							{{if $post_data.display_cols|@is_array && $k|in_array:$post_data.display_cols}}
								<td align="center">
									<div style="white-space: nowrap;">
										{{$i.name}}
									</div>
								</td>
							{{/if}}
						{{/foreach}}
					</tr>
				</thead>
				<tbody>
					{{foreach name=ret item=i from=$ret.rows}}
						<tr class="tr{{cycle values="1,2"}}">
							{{foreach item=ii from=$i.cell}}
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
*}}

{{/if}}
