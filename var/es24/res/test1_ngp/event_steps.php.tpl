{{* 文字コード自動判別用文字列 *}}
<div align="left" style="white-space: nowrap;float:left;">
	<form method="POST" id="f1">
		<input type="hidden" name="f1_submit" value="1">
		<fieldset>
			<legend>{{$pstrres.f101}}</legend>
			<div align="center">
				<table><tr><td><input type="text" name="begin_date" size="10" class="date" value="{{$begin_date}}"></td><td>～</td><td><input type="text" name="end_date" size="10" class="date" value="{{$end_date}}"></td></tr></table>
				
				<select name="event_id">
					{{foreach name=event_id_list key=k item=i from=$event_id_list}}
						<option value="{{$k}}" {{if $event_id == $k}}selected{{/if}}>{{$k}} : {{$i}}</option>
					{{/foreach}}
				</select>
				　<button type="submit">{{$strres.b001}}</button>
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
//-->
</script>
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
{{/if}}
