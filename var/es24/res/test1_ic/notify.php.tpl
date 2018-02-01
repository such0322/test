<script language="javascript">
<!--
$(function(){
	$("tr[trigger_type]").hide();
	$('#f1_trigger_type').change(function(){
		$("tr[trigger_type]").hide();
		$("tr[trigger_type='"+$("#f1_trigger_type option:selected").val()+"']").show();
	});
	$('#f1_trigger_type').change();
});
-->
</script>
<div align="left" style="float:left;">
	<form name="f1" id="f1" method="POST">
		<input type="hidden" name="f1_submit" value="1">
		<input type="hidden" name="edit_id" value="0">
		<fieldset>
			<legend>登録・変更</legend>
			<table>
				<tbody>
					<tr>
						<td align="right" style="white-space: nowrap;">ID</td>
						<td>&nbsp;</td>
						<td>
							{{if $f1_defaults.id > 0}}
								<input type="text" id="f1_id" name="id" value="{{$f1_defaults.id}}" size="8">を編集中
								<span style="font-size:xx-small">
									<a href="javascript:f5_set_edit_id(document.getElementById('f1_id').value)">再読み込み</a>
								</span>
							{{else}}
								<input type="text" name="id" value="0" size="8"> ※ 0 を指定すると自動的に番号が振られます
							{{/if}}
						</td>
					</tr>
{{*
					<tr>
						<td align="right" style="white-space: nowrap;">通知グループ</td>
						<td>&nbsp;</td>
						<td>
							<select id="f1_apns_group_id" name="apns_group_id">
								<option value="0" {{if $f1_defaults.apns_group_id==0}}selected="selected"{{/if}}>指定なし</value>
								{{foreach item=i key=k from=$apns_group_id_list}}
									<option value="{{$k}}" {{if $f1_defaults.apns_group_id==$k}}selected="selected"{{/if}}>{{$k}}:{{$i}}</value>
								{{/foreach}}
							</select>
						</td>
					</tr>
*}}
					<tr>
						<td align="right" style="white-space: nowrap;">種別</td>
						<td>&nbsp;</td>
						<td>
							<select id="f1_trigger_type" name="trigger_type">
								{{* <option value="0" {{if $f1_defaults.trigger_type==0}}selected="selected"{{/if}}>送信しない</value> *}}
								{{foreach item=i key=k from=$trigger_type_list}}
									<option value="{{$k}}" {{if $f1_defaults.trigger_type==$k}}selected="selected"{{/if}}>{{$k}}:{{$i}}</value>
								{{/foreach}}
							</select>
						</td>
					</tr>
					
					<tr trigger_type="1">
						<td align="right" style="white-space: nowrap;">送信予定日時</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" id="f1_trigger_datetime" name="trigger_datetime" value="{{$f1_defaults.trigger_datetime|default:$smarty.now|date_format:"%Y-%m-%d %H:%M:00"}}" size="20" maxlength="20" class="datetime">
						</td>
					</tr>
					
					<tr trigger_type="2">
						<td align="right" style="white-space: nowrap;">期間</td>
						<td>&nbsp;</td>
						<td>
							<table><tbody><tr><td>
								<input id="f1_begin_date" type="text" name="begin_date" value="{{if $f1_defaults.begin_date}}{{$f1_defaults.begin_date}}{{else}}{{$smarty.now|date_format:"%Y-%m-%d"}}{{/if}}" size="10" maxlength="10" class="date">
							</td><td>　～　</td><td>
								<input id="f1_end_date" type="text" name="end_date" value="{{if $f1_defaults.end_date}}{{$f1_defaults.end_date}}{{else}}{{$smarty.now|date_format:"%Y-%m-%d"}}{{/if}}" size="10" maxlength="10" class="date">
							</td></tr></tbody></table>
						</td>
					</tr>
					<tr trigger_type="2">
						<td align="right" style="white-space: nowrap;">適用時間</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" id="f1_trigger_time" name="trigger_time" value="{{$f1_defaults.trigger_time|default:"00:00:00"}}" size="8" class="time">
						</td>
					</tr>
					<tr trigger_type="2">
						<td align="right" style="white-space: nowrap;">発動曜日</td>
						<td>&nbsp;</td>
						<td>
							<select name="trigger_wday[]" multiple size="7">
								<option value="0" {{if isset($f1_defaults.trigger_wday_checker.0)}}selected="selected"{{/if}}>0 : 日曜日</option>
								<option value="1" {{if $f1_defaults.trigger_wday_checker.1}}selected="selected"{{/if}}>1 : 月曜日</option>
								<option value="2" {{if $f1_defaults.trigger_wday_checker.2}}selected="selected"{{/if}}>2 : 火曜日</option>
								<option value="3" {{if $f1_defaults.trigger_wday_checker.3}}selected="selected"{{/if}}>3 : 水曜日</option>
								<option value="4" {{if $f1_defaults.trigger_wday_checker.4}}selected="selected"{{/if}}>4 : 木曜日</option>
								<option value="5" {{if $f1_defaults.trigger_wday_checker.5}}selected="selected"{{/if}}>5 : 金曜日</option>
								<option value="6" {{if $f1_defaults.trigger_wday_checker.6}}selected="selected"{{/if}}>6 : 土曜日</option>
							</select>
						</td>
					</tr>
					
					<tr><td colspan="3"><hr></td></tr>
					
					<tr>
						<td align="right" style="white-space: nowrap;">本文</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" name="msg" value="{{$f1_defaults.msg}}" size="50">
						</td>
					</tr>
					
					<tr><td colspan="3"><hr></td></tr>
					
					<tr>
						<td align="right" style="white-space: nowrap;">管理者用メモ</td>
						<td>&nbsp;</td>
						<td>
							<textarea name="memo" cols="40" rows="3">{{$f1_defaults.memo}}</textarea>
						</td>
					</tr>
					
					<tr>
						<td colspan="3" align="center">
							<button type="submit" name="f1_submit_1" value="" onclick="return confirm('指定した内容で更新します、よろしいですか？');">更新</button>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</form>
</div>
<br style="clear:both;">

<!--
<div align="left" style="width:400">
	<form name="f2" id="f2" method="POST">
		<fieldset>
			<legend>検索</legend>
			<input type="hidden" name="f2_submit" value=1>
			<input type="hidden" name="f2_reset" id="f2_reset" value=0>
			<table border=0>
				<tr>
					<td>日時</td>
					<td>
						<input type="text" name="date_begin_year"  value="" size=4 maxlength=4>
						-
						<input type="text" name="date_begin_month" value="" size=2 maxlength=2>
						-
						<input type="text" name="date_begin_day"   value="" size=2 maxlength=2>
						　～　
						<input type="text" name="date_end_year"    value="" size=4 maxlength=4>
						-
						<input type="text" name="date_end_month"   value="" size=2 maxlength=2>
						-
						<input type="text" name="date_end_day"     value="" size=2 maxlength=2>
					</td>
				</tr>
				<tr>
					<td colspan=2 align="center">
						<button name="submit" type="submit">検索</button>
						<button name="submit" type="submit" onclick="document.getElementById('f2_reset').value=1">リセット</button>
					</td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>
-->
<script language="javascript">
<!--
function f4_submit_confirm() {
	return confirm('選択したレコードを削除します、よろしいですか？');
}
function f5_set_edit_id(id) {
	document.getElementById("f5_id").value = id;
	document.getElementById("f5").submit();
	
}
function f6_submit(offset, limit, order, desc) {
	document.getElementById("f6_offset").value = offset;
	document.getElementById("f6_limit").value = limit;
	document.getElementById("f6_order").value = order;
	document.getElementById("f6_desc").value = desc;
	
	document.getElementById("f6").submit();
}
-->
</script>
<style>
.f4_naiyou {
	border-collapse: collapse;
	borde-bottomr: 1px solid gray;
}
.f4_naiyou td {
    border-bottom: 1px solid gray;
    white-space: nowrap;
}
</style>
<div align="left">
	<form name="f4" method="POST" id="f4">
		<fieldset>
			<legend>通知一覧</legend>
			
			<input type="hidden" name="f4_submit" value="1">
			
			<div width="100%" align="right">
				{{foreach name=f6_paging key=k item=i from=$paging.pages}}
					{{if $k == $paging.offset}}
						<big>{{$i}}</big>
					{{else}}
						<a href="javascript:f6_submit({{$k}}, {{$paging.limit}}, '{{$paging.order}}', '{{$paging.desc}}');">{{$i}}</a>
					{{/if}}
				{{/foreach}}
				　({{$paging.begin}} - {{$paging.end}} /{{$paging.max}})　
				<select name="limit" onchange="f6_submit(0, this.value, '{{$paging.order}}', '{{$paging.desc}}');">
					{{foreach item=i from=$paging.limit_list}}
						<option value="{{$i}}" {{if $i == $paging.limit}}selected{{/if}}>{{$i}}</option>
					{{/foreach}}
				</select>
			</div>
			
			<table class="main2" width="98%">
				<tbody>
					<tr id="trh">
						<th align="center" style="white-space:nowrap">　ID　</th>
						<th align="center" style="white-space:nowrap">　発動種別/条件　</th>
						<th align="center" style="white-space:nowrap">　有効期限　</th>
						<th align="center" style="white-space:nowrap">　発信内容　</th>
						<th align="center" style="white-space:nowrap">　メモ　</th>
						<th align="center" style="white-space:nowrap">　最終送信日時　</th>
						<th>&nbsp;</th>
						<th align="center" style="white-space:nowrap">　削除　</th>
					</tr>
					{{strip}}
						{{foreach key=k item=i from=$apnss}}
							<tr class="tr{{cycle values='1,2'}}">
								<td align="center">
									<a href="javascript:f5_set_edit_id({{$i.id}});">{{$i.id}}</a>
								</td>
								<td>
									<span style="white-space:nowrap">{{$i.trigger_type}}:{{$trigger_type_list[$i.trigger_type]}}</span><br>
									<br>
									{{if $i.trigger_type==1}}
										<span style="white-space:nowrap">{{$i.trigger_datetime}}</span><br>
									{{elseif $i.trigger_type==2}}
										<span style="white-space:nowrap">{{$i.begin_date}}　～　{{$i.end_date}} </span><br>
										<span style="white-space:nowrap">({{$i.trigger_wday_names}}) {{$i.trigger_time}}</span><br>
									{{else}}
										&nbsp;
									{{/if}}
								</td>
								<td style="white-space:nowrap;text-align:right">
									{{$i.expiry}}
								</td>
								<td>
									{{if $i.is_payload_size_over}}
										<span class="error">本文が長すぎるため送信の失敗が予想されます (length: {{$i.payload_size}})</span>
										<hr>
									{{/if}}
									<table class="f4_naiyou">
										<tbody>
											<tr>
												<td>
													{{$i.msg|escape|nl2br}}
												</td>
											</tr>
										</tbody>
									</table>
								</td>
								<td style="white-space:nowrap">
									{{$i.memo|escape|nl2br}}
								</td>
								<td style="white-space:nowrap">
									{{$i.last_send_date|escape}}
								</td>
								
								<td>
									&nbsp;
								</td>
								<td align="center">
									<input type="checkbox" name="delete_id[]" value="{{$i.id}}">
								</td>
							</tr>
						{{/foreach}}
					{{/strip}}
				</tbody>
			</table>
			<hr>
			<div align="right">
				<button type="button" name="f4_submit_1" value="" onclick="return f5_set_edit_id(0);">新規作成</button>
				　<button type="submit" name="f4_submit_1" value="" onclick="return f4_submit_confirm();">削除</button>
			</div>
		</fieldset>
	</form>
	<form name="f5" method="POST" id="f5">
		<input type="hidden" name="f5_submit" value="1">
		<input type="hidden" name="id" id="f5_id" value="">
	</form>
	<form name="f6" method="POST" id="f6">
		<input type="hidden" name="f6_submit" value="1">
		<input type="hidden" name="offset" id="f6_offset" value="{{$paging.offset}}">
		<input type="hidden" name="limit" id="f6_limit" value="{{$paging.limit}}">
		<input type="hidden" name="order" id="f6_order" value="{{$paging.order}}">
		<input type="hidden" name="desc" id="f6_desc" value="{{$paging.desc}}">
	</form>
</div>
<br style="clear:both;">
