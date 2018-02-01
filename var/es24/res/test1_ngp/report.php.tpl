{{* 文字コード自動判別用文字列 *}}
<script language="javascript">
<!--
	function f1_csv_export() {
		document.getElementById("f1_export").value = "1";
		document.getElementById("f1").submit();
		document.getElementById("f1_export").value = "0";
	}
//-->
</script>
<div align="left" style="white-space: nowrap;float:left;">
	<form method="POST" id="f1">
		<input type="hidden" name="f1_submit" value="1">
		<input type="hidden" id="f1_export" name="export" value="0">
		<fieldset>
			<legend>検索期間</legend>
			
			<table>
				<tr>
					<td>プラットフォーム</td>
					<td>
						<select name="pftype">
							<option value="0">問わず</option>
							{{foreach key=k item=i from=$pftypes}}
								<option value="{{$k}}" {{if $k==$pftype}}selected{{/if}}>{{$i}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
				<tr>
					<td>集計単位</td>
					<td>
						<select name="unit">
							{{foreach key=k item=i from=$units}}
								<option value="{{$k}}" {{if $k==$unit}}selected{{/if}}>{{$i}}</option>
							{{/foreach}}
							
						</select>
					</td>
				</tr>
				<tr>
					<td>期間</td>
					<td>
						<div style="float:left;"><input type="text" name="begin_date" value="{{$begin_date}}" class="date" size="10"></div>
						<div style="float:left;">　～　</div>
						<div style="float:left;"><input type="text" name="end_date"   value="{{$end_date}}"   class="date" size="10"></div>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<label><input type="checkbox" name="is_reload" value="1">キャッシュを使わない</label>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<button type="submit">{{$strres.b001}}</button>
						<button type="button" onclick="f1_csv_export();">{{$strres.b003}}</button>
					</td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>
<br style="clear:both">

{{if $is_f1_search}}

<script>
<!--

$(function(){
	$(".f2_togglecols").change(function(){
		$("."+$(this).attr("target")).toggle($(this).is(":checked"));
	});
	$(".f2_togglecols").each(function(){
		$(this).change();
	});
	
	$(".f2_tr").click(function(){
		$(this).toggleClass("f2_tr_pickup");
	});
});

//-->
</script>

<style>
td.f2_col_1 {background-color:#ffffff;border:1px solid gray;}
td.f2_col_2 {background-color:#f0f8ff;border:1px solid gray;}
td.f2_col_3 {background-color:#faf0e6;border:1px solid gray;}
td.f2_col_4 {background-color:#f5f5dc;border:1px solid gray;display:none;}
td.f2_col_5 {background-color:#fae6f0;border:1px solid gray;display:none;}
td.f2_col_6 {background-color:#fff8dc;border:1px solid gray;display:none;}
td.f2_col_7 {background-color:#f5fffa;border:1px solid gray;display:none;}
td.f2_col_8 {background-color:#fdf5e6;border:1px solid gray;display:none;}

td.f2_col_kikan  {background-color:#ffffff;border:1px solid gray;}
td.f2_col_users  {background-color:#f0f8ff;border:1px solid gray;}
td.f2_col_charge {background-color:#faf0e6;border:1px solid gray;}
td.f2_col_fm_add {background-color:#f5f5dc;border:1px solid gray;display:none;}
td.f2_col_kakin  {background-color:#fae6f0;border:1px solid gray;display:none;}
td.f2_col_gacha  {background-color:#fff8dc;border:1px solid gray;display:none;}
td.f2_col_rm_sub {background-color:#f5fffa;border:1px solid gray;display:none;}
td.f2_col_fm_sub {background-color:#fdf5e6;border:1px solid gray;display:none;}

tr.f2_tr:hover {border: 2px solid black;}
tr.f2_tr_pickup {border: 2px solid black;}

</style>



<div align="left" style="white-space: nowrap;">
	<form name="f2" method="POST" id="f2">
		<fieldset>
			<legend>{{$strres.l006}}</legend>
			
			<fieldset>
				<legend>表示切替</legend>
				<div>
					<label><input type="checkbox" id="" class="f2_togglecols" target="f2_col_users " name="" value=""  checked>{{$group_labels.users}} </label>
					<label><input type="checkbox" id="" class="f2_togglecols" target="f2_col_charge" name="" value=""  checked>{{$group_labels.charge}}</label>
					<label><input type="checkbox" id="" class="f2_togglecols" target="f2_col_fm_add" name="" value=""         >{{$group_labels.fm_add}}</label>
					<label><input type="checkbox" id="" class="f2_togglecols" target="f2_col_kakin " name="" value=""         >{{$group_labels.kakin}} </label>
					<label><input type="checkbox" id="" class="f2_togglecols" target="f2_col_gacha " name="" value=""         >{{$group_labels.gacha}} </label>
					<label><input type="checkbox" id="" class="f2_togglecols" target="f2_col_rm_sub" name="" value=""         >{{$group_labels.rm_sub}}</label>
					<label><input type="checkbox" id="" class="f2_togglecols" target="f2_col_fm_sub" name="" value=""         >{{$group_labels.fm_sub}}</label>
				</div>
			</fieldset>
			
			<br>
			
			<table class="main2" name="history_table" width="100%">
				<thead>
					<tr class="">
						<td align="center" class="f2_col_kikan " colspan="2">{{$group_labels.kikan}} </td>
						<td align="center" class="f2_col_users " colspan="3">{{$group_labels.users}} </td>
						<td align="center" class="f2_col_charge" colspan="7">{{$group_labels.charge}}</td>
						<td align="center" class="f2_col_fm_add" colspan="3">{{$group_labels.fm_add}}</td>
						<td align="center" class="f2_col_kakin " colspan="3">{{$group_labels.kakin}} </td>
						<td align="center" class="f2_col_gacha " colspan="3">{{$group_labels.gacha}} </td>
						<td align="center" class="f2_col_rm_sub" colspan="3">{{$group_labels.rm_sub}}</td>
						<td align="center" class="f2_col_fm_sub" colspan="3">{{$group_labels.fm_sub}}</td>
					</tr>
					<tr class="">
						<td align="center" class="f2_col_kikan" >{{$col2label.start}}</td>
						<td align="center" class="f2_col_kikan" >{{$col2label.end}}</td>
						
						<td align="center" class="f2_col_users" >{{$col2label.regist_count}}</td>
						<td align="center" class="f2_col_users" >{{$col2label.leave_count}}</td>
						<td align="center" class="f2_col_users" >{{$col2label.login_count}}</td>
						
						<td align="center" class="f2_col_charge">{{$col2label.charge_user}}</td>
						<td align="center" class="f2_col_charge">{{$col2label.charge_user_first}}</td>
						<td align="center" class="f2_col_charge">{{$col2label.charge_total}}</td>
						<td align="center" class="f2_col_charge">{{$col2label.rm_add_total}}</td>
						<td align="center" class="f2_col_charge">{{$col2label.charge_arpu}}</td>
						<td align="center" class="f2_col_charge">{{$col2label.charge_arppu}}</td>
						<td align="center" class="f2_col_charge">{{$col2label.charge_user_rate}}</td>
						
						<td align="center" class="f2_col_fm_add">{{$col2label.fm_add_user}}</td>
						<td align="center" class="f2_col_fm_add">{{$col2label.fm_add_user_first}}</td>
						<td align="center" class="f2_col_fm_add">{{$col2label.fm_add_total}}</td>
						
						<td align="center" class="f2_col_kakin" >{{$col2label.kakin_user}}</td>
						<td align="center" class="f2_col_kakin" >{{$col2label.kakin_user_first}}</td>
						<td align="center" class="f2_col_kakin" >{{$col2label.kakin_total}}</td>
						
						<td align="center" class="f2_col_gacha" >{{$col2label.gacha_user}}</td>
						<td align="center" class="f2_col_gacha" >{{$col2label.gacha_user_first}}</td>
						<td align="center" class="f2_col_gacha" >{{$col2label.gacha_total}}</td>
						
						<td align="center" class="f2_col_rm_sub">{{$col2label.rm_sub_user}}</td>
						<td align="center" class="f2_col_rm_sub">{{$col2label.rm_sub_user_first}}</td>
						<td align="center" class="f2_col_rm_sub">{{$col2label.rm_sub_total}}</td>
						
						<td align="center" class="f2_col_fm_sub">{{$col2label.fm_sub_user}}</td>
						<td align="center" class="f2_col_fm_sub">{{$col2label.fm_sub_user_first}}</td>
						<td align="center" class="f2_col_fm_sub">{{$col2label.fm_sub_total}}</td>
					</tr>
				</thead>
				<tbody>
					{{foreach name=logs key=k item=i from=$logs}}
						<tr class="f2_tr">
							<td align="center" class="f2_col_kikan">{{$i.start}}</td>
							<td align="center" class="f2_col_kikan">{{$i.end}}</td>
							
							<td align="right" class="f2_col_users" >{{$i.regist_count|number_format}}     </td>
							<td align="right" class="f2_col_users" >{{$i.leave_count|number_format}}      </td>
							<td align="right" class="f2_col_users" >{{$i.login_count|number_format}}      </td>
							
							<td align="right" class="f2_col_charge">{{$i.charge_user|number_format}}      </td>
							<td align="right" class="f2_col_charge">{{$i.charge_user_first|number_format}}</td>
							<td align="right" class="f2_col_charge">{{$i.charge_total|number_format}}     </td>
							<td align="right" class="f2_col_charge">{{$i.rm_add_total|number_format}}     </td>
							<td align="right" class="f2_col_charge">{{$i.charge_arpu|number_format}}</td>
							<td align="right" class="f2_col_charge">{{$i.charge_arppu|number_format}}</td>
							<td align="right" class="f2_col_charge">{{$i.charge_user_rate*100}} %</td>
							
							<td align="right" class="f2_col_fm_add">{{$i.fm_add_user|number_format}}      </td>
							<td align="right" class="f2_col_fm_add">{{$i.fm_add_user_first|number_format}}</td>
							<td align="right" class="f2_col_fm_add">{{$i.fm_add_total|number_format}}     </td>
							
							<td align="right" class="f2_col_kakin" >{{$i.kakin_user|number_format}}       </td>
							<td align="right" class="f2_col_kakin" >{{$i.kakin_user_first|number_format}} </td>
							<td align="right" class="f2_col_kakin" >{{$i.kakin_total|number_format}}      </td>
							
							<td align="right" class="f2_col_gacha" >{{$i.gacha_user|number_format}}       </td>
							<td align="right" class="f2_col_gacha" >{{$i.gacha_user_first|number_format}} </td>
							<td align="right" class="f2_col_gacha" >{{$i.gacha_total|number_format}}      </td>
							
							<td align="right" class="f2_col_rm_sub">{{$i.rm_sub_user|number_format}}      </td>
							<td align="right" class="f2_col_rm_sub">{{$i.rm_sub_user_first|number_format}}</td>
							<td align="right" class="f2_col_rm_sub">{{$i.rm_sub_total|number_format}}     </td>
							
							<td align="right" class="f2_col_fm_sub">{{$i.fm_sub_user|number_format}}      </td>
							<td align="right" class="f2_col_fm_sub">{{$i.fm_sub_user_first|number_format}}</td>
							<td align="right" class="f2_col_fm_sub">{{$i.fm_sub_total|number_format}}     </td>
						</tr>
					{{/foreach}}
				</tbody>
			</table>
		</fieldset>
	</form>
</div>



{{*

	ここからグラフの描画とか

*}}

<div>
	<fieldset>
		<div style="float:left">
			<span class="chart_select" target="chart_users"   style="cursor:pointer;font-size:small"><label><input type="radio" name="chart_select">{{$group_labels.users}}</label></span>　
			<span class="chart_select" target="chart_arpu"    style="cursor:pointer;font-size:small"><label><input type="radio" name="chart_select">ARPU</label></span>　
			<span class="chart_select" target="chart_charge"  style="cursor:pointer;font-size:small"><label><input type="radio" name="chart_select">{{$group_labels.charge}}</label></span>　
			<span class="chart_select" target="chart_fm_add"  style="cursor:pointer;font-size:small"><label><input type="radio" name="chart_select">{{$group_labels.fm_add}}</label></span>　
			<span class="chart_select" target="chart_kakin"   style="cursor:pointer;font-size:small"><label><input type="radio" name="chart_select">{{$group_labels.kakin}} </label></span>　
			<span class="chart_select" target="chart_gacha"   style="cursor:pointer;font-size:small"><label><input type="radio" name="chart_select">{{$group_labels.gacha}} </label></span>　
			<span class="chart_select" target="chart_rm_sub"  style="cursor:pointer;font-size:small"><label><input type="radio" name="chart_select">{{$group_labels.rm_sub}}</label></span>　
			<span class="chart_select" target="chart_fm_sub"  style="cursor:pointer;font-size:small"><label><input type="radio" name="chart_select">{{$group_labels.fm_sub}}</label></span>　
		</div>
		<br style="clear:both;">
		<div class="charts" id="chart_users"  style="width:800;height:600;"></div>
		<div class="charts" id="chart_arpu"   style="width:800;height:600;"></div>
		<div class="charts" id="chart_charge" style="width:800;height:600;"></div>
		<div class="charts" id="chart_fm_add" style="width:800;height:600;"></div>
		<div class="charts" id="chart_kakin"  style="width:800;height:600;"></div>
		<div class="charts" id="chart_gacha"  style="width:800;height:600;"></div>
		<div class="charts" id="chart_rm_sub" style="width:800;height:600;"></div>
		<div class="charts" id="chart_fm_sub" style="width:800;height:600;"></div>
	</fieldset>
</div>

<style>
<!--
.jqplot-table-legend { white-space: nowrap;}
-->
</style>
<script language="javascript" type="text/javascript" src="js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="js/jqplot/plugins/jqplot.pointLabels.min.js"></script>
<script language="javascript" type="text/javascript" src="js/jqplot/plugins/jqplot.enhancedLegendRenderer.js"></script>
<script language="javascript" type="text/javascript" src="js/jqplot/plugins/jqplot.barRenderer.js"></script>
<script language="javascript" type="text/javascript">
<!--

$(function(){
	
	var tickers = [
		{{foreach name=gt key=k item=i from=$logs}}
			"{{$k|strtotime|date_format:"%m-%d"}}"
			{{if ! $smarty.foreach.gt.last}}
				,
			{{/if}}
		{{/foreach}}
	];
	
	var src_users = [
		 [{{foreach name=src_a_regist item=i key=k from=$logs}}{{$i.regist_count}} {{if ! $smarty.foreach.src_a_regist.last}},{{/if}}{{/foreach}}]
		,[{{foreach name=src_a_login  item=i key=k from=$logs}}{{$i.login_count}}  {{if ! $smarty.foreach.src_a_login.last}},{{/if}}{{/foreach}} ]
	];
	
	var src_arpu = [
		 [{{foreach name=src_a_arpu  item=i key=k from=$logs}}{{$i.charge_arpu}}          {{if ! $smarty.foreach.src_a_arpu.last}},{{/if}}{{/foreach}} ]
		,[{{foreach name=src_a_arppu item=i key=k from=$logs}}{{$i.charge_arppu}}         {{if ! $smarty.foreach.src_a_arppu.last}},{{/if}}{{/foreach}}]
		,[{{foreach name=src_a_rate  item=i key=k from=$logs}}{{$i.charge_user_rate*100}} {{if ! $smarty.foreach.src_a_rate.last}},{{/if}}{{/foreach}} ]
	];
	
	var src_charge = [
		 [{{foreach name=src_charge_c item=i key=k from=$logs}}{{$i.charge_user}}       {{if ! $smarty.foreach.src_charge_c.last}},{{/if}}{{/foreach}}]
		,[{{foreach name=src_charge_f item=i key=k from=$logs}}{{$i.charge_user_first}} {{if ! $smarty.foreach.src_charge_f.last}},{{/if}}{{/foreach}}]
		,[{{foreach name=src_charge_t item=i key=k from=$logs}}{{$i.charge_total}}      {{if ! $smarty.foreach.src_charge_t.last}},{{/if}}{{/foreach}}]
	];
	
	var src_fm_add = [
		 [{{foreach name=src_fm_add_c item=i key=k from=$logs}}{{$i.fm_add_user}}       {{if ! $smarty.foreach.src_fm_add_c.last}},{{/if}}{{/foreach}}]
		,[{{foreach name=src_fm_add_f item=i key=k from=$logs}}{{$i.fm_add_user_first}} {{if ! $smarty.foreach.src_fm_add_f.last}},{{/if}}{{/foreach}}]
		,[{{foreach name=src_fm_add_t item=i key=k from=$logs}}{{$i.fm_add_total}}      {{if ! $smarty.foreach.src_fm_add_t.last}},{{/if}}{{/foreach}}]
	];
	
	var src_kakin = [
		 [{{foreach name=src_kakin_c item=i key=k from=$logs}}{{$i.kakin_user}}       {{if ! $smarty.foreach.src_kakin_c.last}},{{/if}}{{/foreach}}]
		,[{{foreach name=src_kakin_f item=i key=k from=$logs}}{{$i.kakin_user_first}} {{if ! $smarty.foreach.src_kakin_f.last}},{{/if}}{{/foreach}}]
		,[{{foreach name=src_kakin_t item=i key=k from=$logs}}{{$i.kakin_total}}      {{if ! $smarty.foreach.src_kakin_t.last}},{{/if}}{{/foreach}}]
	];
	
	var src_gacha = [
		 [{{foreach name=src_gacha_c item=i key=k from=$logs}}{{$i.gacha_user}}       {{if ! $smarty.foreach.src_gacha_c.last}},{{/if}}{{/foreach}}]
		,[{{foreach name=src_gacha_f item=i key=k from=$logs}}{{$i.gacha_user_first}} {{if ! $smarty.foreach.src_gacha_f.last}},{{/if}}{{/foreach}}]
		,[{{foreach name=src_gacha_t item=i key=k from=$logs}}{{$i.gacha_total}}      {{if ! $smarty.foreach.src_gacha_t.last}},{{/if}}{{/foreach}}]
	];
	
	var src_rm_sub = [
		 [{{foreach name=src_rm_sub_c item=i key=k from=$logs}}{{$i.rm_sub_user}}       {{if ! $smarty.foreach.src_rm_sub_c.last}},{{/if}}{{/foreach}}]
		,[{{foreach name=src_rm_sub_f item=i key=k from=$logs}}{{$i.rm_sub_user_first}} {{if ! $smarty.foreach.src_rm_sub_f.last}},{{/if}}{{/foreach}}]
		,[{{foreach name=src_rm_sub_t item=i key=k from=$logs}}{{$i.rm_sub_total}}      {{if ! $smarty.foreach.src_rm_sub_t.last}},{{/if}}{{/foreach}}]
	];
	
	var src_fm_sub = [
		 [{{foreach name=src_fm_sub_c item=i key=k from=$logs}}{{$i.fm_sub_user}}       {{if ! $smarty.foreach.src_fm_sub_c.last}},{{/if}}{{/foreach}}]
		,[{{foreach name=src_fm_sub_f item=i key=k from=$logs}}{{$i.fm_sub_user_first}} {{if ! $smarty.foreach.src_fm_sub_f.last}},{{/if}}{{/foreach}}]
		,[{{foreach name=src_fm_sub_t item=i key=k from=$logs}}{{$i.fm_sub_total}}      {{if ! $smarty.foreach.src_fm_sub_t.last}},{{/if}}{{/foreach}}]
	];
	
	
	
	$('#chart_users').bind("showprot", function(e, myName){
		$('#chart_users').width($('#chart_users').parent().width() - 200);
		var prot_users = $.jqplot('chart_users', src_users, {
			showMarker: false,
			seriesDefaults: {
				fill: true,
				rendererOptions: {
					highlightMouseDown: true
				},
				pointLabels: {
					show: true
				}
			},
			series:[
				{label:'登録数',     yaxis:'yaxis',  fill: false},
				{label:'ログイン数', yaxis:'yaxis',  fill: false}
			],
			legend: {
				width: '200px', 
				marginLeft: '50px', 
				placement: "outside", 
				renderer: $.jqplot.EnhancedLegendRenderer,
				show: true,
				location: 'ne'
			},
			axes: {
				xaxis: {
					pad: 0
					,renderer: $.jqplot.CategoryAxisRenderer
					,ticks: tickers
				},
				yaxis: {
					min: 0,
					showTicks: true,
					tickOptions: {
						formatString: '%d', 
						mark: 'outside'
					}, 
					padMin: 0
				}
			}
		});
	});
	$('#chart_arpu').bind("showprot", function(e, myName){
		$('#chart_arpu').width($('#chart_arpu').parent().width() - 200);
		var prot_arpu = $.jqplot('chart_arpu', src_arpu, {
			showMarker: false,
			seriesDefaults: {
				fill: true,
				rendererOptions: {
					highlightMouseDown: true
				},
				pointLabels: {
					show: true
				}
			},
			series:[
				{label:'ARPU',   yaxis:'yaxis',  fill: false},
				{label:'ARPPU',  yaxis:'yaxis',  fill: false},
				{label:'課金率', yaxis:'y2axis', fill: false}
			],
			legend: {
				width: '200px', 
				marginLeft: '50px', 
				placement: "outside", 
				renderer: $.jqplot.EnhancedLegendRenderer,
				show: true,
				location: 'ne'
			},
			axes: {
				xaxis: {
					pad: 0
					,renderer: $.jqplot.CategoryAxisRenderer
					,ticks: tickers
				},
				yaxis: {
					min: 0,
					showTicks: true,
					tickOptions: {
						formatString: '%d', 
						mark: 'outside'
					}, 
					padMin: 0
				},
				y2axis: {
					min: 0,
					showTicks: true,
					tickOptions: {
						formatString: '%.2f %%', 
						mark: 'outside'
					}, 
					padMin: 0
				}
			}
		});
	});
	
	
	$('#chart_charge').bind("showprot", function(e, myName){
		$('#chart_charge').width($('#chart_charge').parent().width() - 200);
		var prot_charge = $.jqplot('chart_charge', src_charge, {
			showMarker: false,
			seriesDefaults: {
				fill: true,
				rendererOptions: {
					highlightMouseDown: true
				},
				pointLabels: {
					show: true
				}
			},
			series:[
				{label:'課金ユーザ数',       yaxis:'yaxis'},
				{label:'課金ユーザ数 (初回)',yaxis:'yaxis'},
				{label:'課金売上総額',       yaxis:'y2axis', fill: false}
			],
			legend: {
				marginLeft: '50px', 
				placement: "outside", 
				renderer: $.jqplot.EnhancedLegendRenderer,
				show: true,
				location: 'ne'
			},
			axes: {
				xaxis: {
					pad: 0
					,renderer: $.jqplot.CategoryAxisRenderer
					,ticks: tickers
				},
				yaxis: {
					min: 0,
					showTicks: true,
					tickOptions: {
						formatString: '%d', 
						mark: 'outside'
					}, 
					padMin: 0
				},
				y2axis: {
					min: 0,
					showTicks: true,
					tickOptions: {
						formatString: '%d', 
						mark: 'outside'
					}, 
					padMin: 0
				}
			}
		});
	});
	$('#chart_fm_add').bind("showprot", function(e, myName){
		$('#chart_fm_add').width($('#chart_fm_add').parent().width() - 200);
		var prot_fm_add = $.jqplot('chart_fm_add', src_fm_add, {
			showMarker: false,
			seriesDefaults: {
				fill: true,
				rendererOptions: {
					highlightMouseDown: true
				},
				pointLabels: {
					show: true
				}
			},
			series:[
				{label:'無料付与ユーザ数',       yaxis:'yaxis'},
				{label:'無料付与ユーザ数 (初回)',yaxis:'yaxis'},
				{label:'無料付与総額',           yaxis:'y2axis', fill: false}
			],
			legend: {
				width: '200px', 
				marginLeft: '50px', 
				placement: "outside", 
				renderer: $.jqplot.EnhancedLegendRenderer,
				show: true,
				location: 'ne'
			},
			axes: {
				xaxis: {
					pad: 0
					,renderer: $.jqplot.CategoryAxisRenderer
					,ticks: tickers
				},
				yaxis: {
					min: 0,
					showTicks: true,
					tickOptions: {
						formatString: '%d', 
						mark: 'outside'
					}, 
					padMin: 0
				},
				y2axis: {
					min: 0,
					showTicks: true,
					tickOptions: {
						formatString: '%d', 
						mark: 'outside'
					}, 
					padMin: 0
				}
			}
		});
	});
	$('#chart_kakin').bind("showprot", function(e, myName){
		$('#chart_kakin').width($('#chart_kakin').parent().width() - 200);
		var prot_kakin = $.jqplot('chart_kakin', src_kakin, {
			showMarker: false,
			seriesDefaults: {
				fill: true,
				rendererOptions: {
					highlightMouseDown: true
				},
				pointLabels: {
					show: true
				}
			},
			series:[
				{label:'課金アイテム購入者数',       yaxis:'yaxis'},
				{label:'課金アイテム購入者数 (初回)',yaxis:'yaxis'},
				{label:'課金アイテム売上総額',       yaxis:'y2axis', fill: false}
			],
			legend: {
				width: '200px', 
				marginLeft: '50px', 
				placement: "outside", 
				renderer: $.jqplot.EnhancedLegendRenderer,
				show: true,
				location: 'ne'
			},
			axes: {
				xaxis: {
					pad: 0
					,renderer: $.jqplot.CategoryAxisRenderer
					,ticks: tickers
				},
				yaxis: {
					min: 0,
					showTicks: true,
					tickOptions: {
						formatString: '%d', 
						mark: 'outside'
					}, 
					padMin: 0
				},
				y2axis: {
					min: 0,
					showTicks: true,
					tickOptions: {
						formatString: '%d', 
						mark: 'outside'
					}, 
					padMin: 0
				}
			}
		});
	});
	$('#chart_gacha').bind("showprot", function(e, myName){
		$('#chart_gacha').width($('#chart_gacha').parent().width() - 200);
		var prot_gacha = $.jqplot('chart_gacha', src_gacha, {
			showMarker: false,
			seriesDefaults: {
				fill: true,
				rendererOptions: {
					highlightMouseDown: true
				},
				pointLabels: {
					show: true
				}
			},
			series:[
				{label:'ガチャ消費ユーザ数',       yaxis:'yaxis'},
				{label:'ガチャ消費ユーザ数 (初回)',yaxis:'yaxis'},
				{label:'ガチャ消費総額',           yaxis:'y2axis', fill: false}
			],
			legend: {
				width: '200px', 
				marginLeft: '50px', 
				placement: "outside", 
				renderer: $.jqplot.EnhancedLegendRenderer,
				show: true,
				location: 'ne'
			},
			axes: {
				xaxis: {
					pad: 0
					,renderer: $.jqplot.CategoryAxisRenderer
					,ticks: tickers
				},
				yaxis: {
					min: 0,
					showTicks: true,
					tickOptions: {
						formatString: '%d', 
						mark: 'outside'
					}, 
					padMin: 0
				},
				y2axis: {
					min: 0,
					showTicks: true,
					tickOptions: {
						formatString: '%d', 
						mark: 'outside'
					}, 
					padMin: 0
				}
			}
		});
	});
	$('#chart_rm_sub').bind("showprot", function(e, myName){
		$('#chart_rm_sub').width($('#chart_rm_sub').parent().width() - 200);
		var prot_rm_sub = $.jqplot('chart_rm_sub', src_rm_sub, {
			showMarker: false,
			seriesDefaults: {
				fill: true,
				rendererOptions: {
					highlightMouseDown: true
				},
				pointLabels: {
					show: true
				}
			},
			series:[
				{label:'課金通貨消費ユーザ数',       yaxis:'yaxis'},
				{label:'課金通貨消費ユーザ数 (初回)',yaxis:'yaxis'},
				{label:'課金通貨消費総額',           yaxis:'y2axis', fill: false}
			],
			legend: {
				width: '200px', 
				marginLeft: '50px', 
				placement: "outside", 
				renderer: $.jqplot.EnhancedLegendRenderer,
				show: true,
				location: 'ne'
			},
			axes: {
				xaxis: {
					pad: 0
					,renderer: $.jqplot.CategoryAxisRenderer
					,ticks: tickers
				},
				yaxis: {
					min: 0,
					showTicks: true,
					tickOptions: {
						formatString: '%d', 
						mark: 'outside'
					}, 
					padMin: 0
				},
				y2axis: {
					min: 0,
					showTicks: true,
					tickOptions: {
						formatString: '%d', 
						mark: 'outside'
					}, 
					padMin: 0
				}
			}
		});
	});
	// 面グラフの方を生成
	$('#chart_fm_sub').bind("showprot", function(e, myName){
		$('#chart_fm_sub').width($('#chart_fm_sub').parent().width() - 200);
		var prot_fm_sub = $.jqplot('chart_fm_sub', src_fm_sub, {
			showMarker: false,
			seriesDefaults: {
				fill: true,
				rendererOptions: {
					highlightMouseDown: true
				},
				pointLabels: {
					show: true
				}
			},
			series:[
				{label:'無料付与通貨消費ユーザ数',       yaxis:'yaxis'},
				{label:'無料付与通貨消費ユーザ数 (初回)',yaxis:'yaxis'},
				{label:'無料付与通貨消費総額',           yaxis:'y2axis', fill: false}
			],
			legend: {
				width: '200px', 
				marginLeft: '50px', 
				placement: "outside", 
				renderer: $.jqplot.EnhancedLegendRenderer,
				show: true,
				location: 'ne'
			},
			axes: {
				xaxis: {
					pad: 0
					,renderer: $.jqplot.CategoryAxisRenderer
					,ticks: tickers
				},
				yaxis: {
					min: 0,
					showTicks: true,
					tickOptions: {
						formatString: '%d', 
						mark: 'outside'
					}, 
					padMin: 0
				},
				y2axis: {
					min: 0,
					showTicks: true,
					tickOptions: {
						formatString: '%d', 
						mark: 'outside'
					}, 
					padMin: 0
				}
			}
		});
	});
	
	
	// グラフの表示切替とか
	$(".charts").hide();
	$(".charts:first").show();
	$(".chart_select").click(function(){
		$(".charts").hide();
		var p = $("#"+$(this).attr("target"));
		p.show();
		if (! p.attr("loaded")) {
			p.trigger("showprot", []);
			p.attr("loaded", 1);
		}
	});
	$("input[name='chart_select']:radio:first").attr("checked", "checked").trigger("click", []);
	
	
	
});
//-->
</script>

{{/if}}

<br style="clear:both">
