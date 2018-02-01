{{* 文字コード自動判別用文字列 *}}
<style>
<!--
.f2_graph_trigger{
	cursor: pointer;
}
.td_toggle{
	cursor: pointer;
}
-->
</style>
<script>
<!--
$(document).ready(function(){
	$(".f2_graph_trigger").click(function(){
		$(".f2_graph_trigger").css("fontWeight", "normal");
		$(this).css("fontWeight", "bold");
		
		$(".f2_graph").hide();
		if ($(this).attr("target")) {
			$("." + $(this).attr("target")).show();
		}
		return false;
	});
	$(".f2_graph_trigger:first").click();
	
	$(".td_toggle").click(function(){
		if ($(this).attr("target")) {
			$("." + $(this).attr("target")).toggle();
		}
	});
});

//-->
</script>
<div align="left" style="white-space: nowrap;">
	<form method="POST" id="f1">
		<input type="hidden" name="f1_submit" value="1">
		<fieldset>
			<legend>{{$pstrres.f101}}</legend>
			<div align="center">
				<div style="float:left;"><input type="text" name="begin_date" value="{{$begin_date}}" class="date" size="10"></div>
				<div style="float:left;">　～　</div>
				<div style="float:left;"><input type="text" name="end_date"   value="{{$end_date}}"   class="date" size="10"></div>
				<div style="float:left;">　<input type="checkbox" name="is_newchara" id="f1_is_newchara" value="1" {{if $is_newchara}}checked{{/if}}><label for="f1_is_newchara">指定期間内作成キャラクターのみ</label></div>
				<div style="float:left;">　<button type="submit">{{$strres.b001}}</button></div>
			</div>
		</fieldset>
	</form>
</div>

{{if $is_f1_search}}
<div align="left" style="white-space: nowrap;">
	<form name="f2" method="POST" id="f2">
		<input type="hidden" name="f2_submit" value="1">
		<input type="hidden" id="f2_order" name="order" value="{{$order}}">
		<input type="hidden" id="f2_desc"  name="desc"  value="{{$desc}}" >
		<fieldset>
			<legend>{{$pstrres.f201}}</legend>
			
			<table id="main2" name="history_table" style="float:left">
				<thead>
					<tr class="trh">
						<td align="center">{{$pstrres.f202}}</td>
						<td align="center">
							<span class="f2_td2">{{$pstrres.f203}} </span>
							{{*<!--
							<span class="f2_td2 td_toggle" target="f2_td2">&lt;</span><span class="f2_td2 td_toggle" target="f2_td2" style="display:none">&gt;</span>
							-->*}}
						</td>
						<td align="center">
							<span class="f2_td3">{{$pstrres.f204}}</span>
							{{*<!--
							<span class="f2_td3 td_toggle" target="f2_td3">&lt;</span><span class="f2_td3 td_toggle" target="f2_td3" style="display:none">&gt;</span>
							-->*}}
						</td>
						{{*<!--
						<td align="center"><span class="f2_td4">{{$pstrres.f206}} </span><span class="f2_td4 td_toggle" target="f2_td4">&lt;</span><span class="f2_td4 td_toggle" target="f2_td4" style="display:none">&gt;</span></td>
						<td align="center"><span class="f2_td5">{{$pstrres.f207}} </span><span class="f2_td5 td_toggle" target="f2_td5">&lt;</span><span class="f2_td5 td_toggle" target="f2_td5" style="display:none">&gt;</span></td>
						<td align="center"><span class="f2_td6">{{$pstrres.f208}} </span><span class="f2_td6 td_toggle" target="f2_td6">&lt;</span><span class="f2_td6 td_toggle" target="f2_td6" style="display:none">&gt;</span></td>
						-->*}}
						<td align="center">&nbsp;{{strip}}
							{{$pstrres.f205}}
							(
						{{*<!--
							<span class="f2_graph_trigger" target="f2_graph_a">[{{$pstrres.f206}} : {{$pstrres.f207}}]</span>
							&nbsp;|&nbsp;
						-->*}}
							<span class="f2_graph_trigger" target="f2_graph_b">[{{$pstrres.f203}} / {{$pstrres.f204}}]</span>
						{{*<!--
							&nbsp;|&nbsp;
							<span class="f2_graph_trigger" target="f2_graph_c">[{{$pstrres.f206}} - {{$pstrres.f207}}]</span>
						-->*}}
							)
						{{/strip}}&nbsp;</td>
					</tr>
				</thead>
				<tbody>
					{{foreach name=logs item=i from=$logs}}
						<tr class="tr{{cycle values="1,2"}}">
							<td>{{$i.step}} : {{$i.step_name}}</td>
							<td align="right"><span class="f2_td2">{{$i.count|default:0}}</span></td>
							<td align="right"><span class="f2_td3">{{$i.total|default:0}}</span></td>
						{{*<!--
							<td align="right"><span class="f2_td4">{{$i.begin|default:0}}</span></td>
							<td align="right"><span class="f2_td5">{{$i.end|default:0}}</span></td>
							<td align="right"><span class="f2_td6">{{$i.cancel|default:0}}</span></td>
						-->*}}
							<td align="left">{{strip}}
						{{*<!--
								<span class="f2_graph f2_graph_a">
									<img src="images/g/c.gif" width="{{$i.width.b|default:"0"}}" height="8" alt="{{$i.begin|default:0}}"><br>
									<img src="images/g/d.gif" width="{{$i.width.e|default:"0"}}" height="8" alt="{{$i.end|default:0}}">
								</span>
						-->*}}
								<span class="f2_graph f2_graph_b">
									<img src="images/g/a.gif" width="{{$i.width.c|default:"0"}}" height="8" alt="{{$i.begin|default:0}}">
									<img src="images/g/b.gif" width="{{$i.width.ct|default:"0"}}" height="8" alt="{{$i.end|default:0}}">
								</span>
						{{*<!--
								<span class="f2_graph f2_graph_c">
									{{if $i.begin-$i.end > 0}}<img src="images/g/a.gif" width="{{$i.width.be|default:"0"}}" height="8" alt="{{$i.begin|default:0}}">{{$i.begin-$i.end|default:0}}{{/if}}
								</span>
						-->*}}
							{{/strip}}</td>
						</tr>
					{{/foreach}}
				</tbody>
			</table>
		</fieldset>
	</form>
</div>
{{/if}}
