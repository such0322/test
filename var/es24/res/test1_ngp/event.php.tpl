{{* 文字コード自動判別用文字列 *}}
<style>
<!--
/*
table.main2 {
	border: 1px solid #D9D9D9;
	font-size:12px;
	line-height:16px;
	border-collapse: collapse;
	border-color: #D9D9D9;
	background-color: #D9D9D9;
}
tr.trh{background-image: url(images/trh.gif);}
tr.tda{background-image: url(images/frame_base_r3_c5.gif);}
tr.tr1{background-color: #EDF3FE;height:20px;}
tr.tr2{background-color: #ffffff;height:20px;}
tr.trh > td {border:1px solid gray;}
tr.trh > th {border:1px solid gray;}
tr.tr1 > td {border:1px solid gray;}
tr.tr2 > td {border:1px solid gray;}
tr.trselect{background-color: #3D80DF;color: #FFFFFF;height:20px;}
*/
td.ratio_10 {background-color:#0000FF;}
td.ratio_9  {background-color:#0065FF;}
td.ratio_8  {background-color:#00CBFF;}
td.ratio_7  {background-color:#00FFCB;}
td.ratio_6  {background-color:#00FF65;}
td.ratio_5  {background-color:#00FF00;}
td.ratio_4  {background-color:#65FF00;}
td.ratio_3  {background-color:#CBFF00;}
td.ratio_2  {background-color:#FFCB00;}
td.ratio_1  {background-color:#FF6500;}
td.ratio_0  {background-color:#FF0000;}
span.toggle_btn {cursor: pointer;}
-->
</style>
<div align="left" style="white-space: nowrap;float:left;">
	<form method="POST" id="f1">
		<input type="hidden" name="f1_submit" value="1">
		<fieldset>
			<legend>{{$pstrres.f101}}</legend>
			<div align="center">
				<table>
					<tr>
						<td>
							<select name="event_id">
								{{foreach name=event_id_list key=k item=i from=$event_id_list}}
									<option value="{{$k}}" {{if $event_id == $k}}selected{{/if}}>{{$k}} : {{$i}}</option>
								{{/foreach}}
							</select>
						</td>
						<td><input type="text" name="begin_date" size="10" class="date" value="{{$begin_date}}"></td>
						<td>～</td>
						<td><input type="text" name="end_date" size="10" class="date" value="{{$end_date}}"></td>
						<td>
							<button type="submit">{{$strres.b001}}</button>
						</td>
					</tr>
				</table>
			</div>
		</fieldset>
	</form>
</div>
<br style="clear:both">

{{if $is_f1_search}}
<script>
<!--
	function f2_change_order(order, desc) {
		$("#f2_order").val(order);
		$("#f2_desc").val(desc);
		$("#f2").submit();
	}

$(function(){
	
	$(".toggle_btn").click(function(){
		$("#f2_steps_div").toggle();
		$("#f3_dur_div").toggle();
	});
	
});
//-->
</script>

<div id="f2_steps_div" align="left" style="white-space: nowrap;">
	<form name="f2" method="POST" id="f2">
		<input type="hidden" name="f2_submit" value="1">
		<input type="hidden" id="f2_order" name="order" value="{{$order}}">
		<input type="hidden" id="f2_desc"  name="desc"  value="{{$desc}}" >
		<fieldset>
			<legend>{{$pstrres.f201}} <span class="toggle_btn">({{$pstrres.f301}})</span></legend>
			
			<table id="main2" name="history_table" style="float:left">
				<thead>
					<tr class="trh">
						<td align="center">
							{{$pstrres.f202}}
							<a href="javascript:f2_change_order('step' , 'asc' );">{{if $order == "step"  && $desc == "asc" }}▲{{else}}△{{/if}}</a>
							<a href="javascript:f2_change_order('step' , 'desc');">{{if $order == "step"  && $desc == "desc"}}▼{{else}}▽{{/if}}</a>
						</td>
						<td align="center">
							{{$pstrres.f203}}
							<a href="javascript:f2_change_order('count', 'asc' );">{{if $order == "count" && $desc == "asc" }}▲{{else}}△{{/if}}</a>
							<a href="javascript:f2_change_order('count', 'desc');">{{if $order == "count" && $desc == "desc"}}▼{{else}}▽{{/if}}</a>
						</td>
						<td align="center">
							{{$pstrres.f204}}
							<a href="javascript:f2_change_order('total', 'asc' );">{{if $order == "total" && $desc == "asc" }}▲{{else}}△{{/if}}</a>
							<a href="javascript:f2_change_order('total', 'desc');">{{if $order == "total" && $desc == "desc"}}▼{{else}}▽{{/if}}</a>
						</td>
						<td align="center">{{$pstrres.f205}}</td>
					</tr>
				</thead>
				<tbody>
					{{foreach name=logs item=i from=$logs}}
						<tr class="tr{{cycle values="1,2"}}">
							<td>{{$i.step}} : {{$i.step_name}}</td>
							<td align="right">{{$i.count|default:0}}</td>
							<td align="right">{{$i.total|default:0}}</td>
							<td align="left">
								<img src="images/g/a.gif" width="{{$i.width_c|default:"0"}}" height="8" alt="{{$i.count|default:0}}"><img src="images/g/b.gif" width="{{$i.width_t|default:"0"}}" height="8" alt="{{$i.total|default:0}}">
							</td>
						</tr>
					{{/foreach}}
				</tbody>
			</table>
			
		</fieldset>
	</form>
</div>

<div id="f3_dur_div" align="left" style="white-space: nowrap;display:none">
	<form method="POST" id="f3">
		<fieldset>
			<legend>{{$pstrres.f301}} <span class="toggle_btn">({{$pstrres.f201}})</span></legend>
			{{if $logs}}
				<div>
					<table class="main2" style="min-width:800px">
						
						<tr class="trh">
							<td align="center">　{{$pstrres.f302}}　</td>
							<td align="center">　{{$pstrres.f303}}　</td>
							<td align="center">　{{$pstrres.f304}}　</td>
							<td align="center">　{{$pstrres.f305}}　</td>
							{{section name=f1t_sec loop=15 start=1 max=15}}
								<td align="center" style="min-width:30px">{{$smarty.section.f1t_sec.index-1}}</td>
							{{/section}}
							<td align="center" style="min-width:30px">14～</td>
							<td align="center" style="min-width:30px">30～</td>
							<td align="center" style="min-width:30px">60～</td>
						</tr>
						
						{{foreach name=logs key=k item=i from=$dur_logs}}
							<tr class="tr{{cycle values="1,1,2,2"}}">
								<td align="center" rowspan="2">{{$k}}</td>
								<td align="right" rowspan="2">{{$i.login|number_format}}</td>
								<td align="right" rowspan="2">{{$i.sum|number_format}} / {{$i.rate}}</td>
								<td align="right" rowspan="2">{{$i.new|number_format}}</td>
								{{section name=f1t_sec loop=15 start=1 max=15}}
									<td align="right" class="logs_{{$smarty.section.f1t_sec.index}} {{if $i[$smarty.section.f1t_sec.index] > 0}}ratio_{{$ratios[$k][$smarty.section.f1t_sec.index]/10|intval}}{{/if}}"><span>{{$i[$smarty.section.f1t_sec.index]|number_format}}</span></td>
								{{/section}}
								<td align="right" class="logs_15 {{if $i.15 > 0}}ratio_{{$ratios.$k.15/10|intval}}{{/if}}">{{$i.15|number_format}}</td>
								<td align="right" class="logs_30 {{if $i.30 > 0}}ratio_{{$ratios.$k.30/10|intval}}{{/if}}">{{$i.30|number_format}}</td>
								<td align="right" class="logs_60 {{if $i.60 > 0}}ratio_{{$ratios.$k.60/10|intval}}{{/if}}">{{$i.60|number_format}}</td>
							</tr>
							<tr class="tr{{cycle values="1,1,2,2"}}">
								{{section name=f1t_sec loop=15 start=1 max=15}}
									<td align="right" class="logs_$smarty.section.f1t_sec.index {{if $i[$smarty.section.f1t_sec.index] > 0}}ratio_{{$ratios[$k][$smarty.section.f1t_sec.index]/10|intval}}{{/if}}">{{if $i[$smarty.section.f1t_sec.index] > 0}}<span>{{"%2.1f %%"|sprintf:$ratios[$k][$smarty.section.f1t_sec.index]}}</span>{{/if}}</td>
								{{/section}}
								<td align="right" class="logs_15 {{if $i.15 > 0}}ratio_{{$ratios.$k.15/10|intval}}{{/if}}">{{if $i.15 > 0}}{{"%2.1f %%"|sprintf:$ratios.$k.15}}{{/if}}</td>
								<td align="right" class="logs_30 {{if $i.30 > 0}}ratio_{{$ratios.$k.30/10|intval}}{{/if}}">{{if $i.30 > 0}}{{"%2.1f %%"|sprintf:$ratios.$k.30}}{{/if}}</td>
								<td align="right" class="logs_60 {{if $i.60 > 0}}ratio_{{$ratios.$k.60/10|intval}}{{/if}}">{{if $i.60 > 0}}{{"%2.1f %%"|sprintf:$ratios.$k.60}}{{/if}}</td>
							</tr>
						{{/foreach}}
						
						
					</table>
				</div>
			{{else}}
				<p>{{$strres.lnf}}</p>
			{{/if}}
		</fieldset>
	</form>
</div>

{{/if}}
