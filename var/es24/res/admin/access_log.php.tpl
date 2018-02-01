<div align="left" style="width:800">
	<form id="f1" method="POST">
		<input type="hidden" name="f1_submit" value="1">
		<fieldset>
			<legend>{{$pstrres.l001}}</legend>
			<table>
				<tr>
					<td>
						{{$pstrres.l101}}
					</td>
					<td>
						<input type="radio" name="type" id="f1_radio_access" value="access" {{if $where.type == "access"}}checked{{/if}}><label for="f1_radio_access">{{$pstrres.s1011}}</label><br />
						<input type="radio" name="type" id="f1_radio_post"   value="post"   {{if $where.type == "post"  }}checked{{/if}}><label for="f1_radio_post"  >{{$pstrres.s1012}}</label><br />
					</td>
				</tr>
				<tr>
					<td>
						{{$pstrres.l102}}
					</td>
					<td>
						<input type="text" name="user" value="{{$where.user|escape:"html"}}" size="8">
						<select name="user_mt">
							<option value="full" {{if $where.user_mt == "full"}}selected{{/if}}>{{$pstrres.s1021}}</option>
							<option value="head" {{if $where.user_mt == "head"}}selected{{/if}}>{{$pstrres.s1022}}</option>
							<option value="part" {{if $where.user_mt == "part"}}selected{{/if}}>{{$pstrres.s1023}}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						{{$pstrres.l103}}
					</td>
					<td>
						<input type="text" name="date" value="{{if $where.date}}{{$where.date|escape:"html"}}{{else}}{{$smarty.now|date_format:"%Y-%m-%d"}}{{/if}}" size="10" class="date">
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<button type="submit" name="" value="">{{$strres.b001}}</button>
					</td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>
<div align="center" style="width:600">
	<form id="f2" method="POST">
		<input type="hidden" name="f2_submit" value="1">
		<input type="hidden" id="f2_operate" name="operate" value="0">
		<fieldset>
			<legend>{{$pstrres.l002}}</legend>
			
			{{if $access_logs}}
				<table id="main2">
					<tr id="trh">
						<th>{{$pstrres.h201}}</th>
						<th>{{$pstrres.h202}}</th>
						<th>{{$pstrres.h203}}</th>
					</tr>
					
					{{foreach name=access_logs item=i from=$access_logs}}
						<tr class="tr{{cycle values="1,2"}}">
							<td>
								{{$i.date}}
							</td>
							<td>
								{{$i.user}}
							</td>
							<td>
								({{$i.menu}}) - [{{$i.tab}}]
							</td>
						</tr>
					{{/foreach}}
				</table>
			{{else}}
				{{if $error == 'n'}}
					{{$pstrres.e201}}
				{{elseif $error == 'd'}}
					{{$pstrres.e202}}
				{{elseif $error == 'w'}}
					{{$pstrres.e203}}
				{{else}}
					{{$pstrres.e204}}
				{{/if}}
			{{/if}}
		</fieldset>
	</form>
</div>
