{{* 文字コード自動判別用文字列 *}}
<script type='text/javascript' src="js/vue.min.js"></script>
<div align="left" style="white-space: nowrap;width:60%;float:left;">
	<form method="POST" id="f1">
		<input type="hidden" name="f1_post" value="1">
		<input type="hidden" id="f1_export" name="export" value="0">
		<input type="hidden" name="f1_submit" value=1>
		<fieldset>
			<legend>{{$strres.l001}}</legend>
			<table id="main2" width="100%">
				<tbody>
					<tr class="tr1">
						<td>
							logdate
						</td>
						<td>
							<input type="text" id="f1_log_date_begin" name="log_date[begin]" value="{{if $where_values.log_date.begin !== null}}{{$where_values.log_date.begin|escape:"html"}}{{else}}{{/if}}" class="datetime" size="20" maxlength="20" />
							　～　
							<input type="text" id="f1_log_date_end" name="log_date[end]" value="{{if $where_values.log_date.end !== null}}{{$where_values.log_date.end|escape:"html"}}{{else}}{{/if}}" class="datetime" size="20" maxlength="20" />
							{{if $is_change_tz}}
								<input type="checkbox" name="is_jst_show" class="jst_show_check" id="f1_log_date_jst" target=""><label for="f1_log_date_jst">{{$timezone_label}}{{$strres.deshow}}</label>
							{{/if}}
						</td>
					</tr>
					<tr class="tr2">
						<td>pf</td>
						<td>
							<input type="text" id="f1_pf_type" name="pf_type" value="{{$where_values.pf_type|escape:"html"}}" size="5">
						</td>
					</tr>
					<tr class="tr1">
						<td>UID</td>
						<td>
							<input type="text" id="f1_uid" name="uid" value="{{$where_values.uid|escape:"html"}}" size="{{$i.size|default:"20"}}">
						</td>
					</tr>
					<tr class="tr2">
						<td>user_id</td>
						<td>
							<input type="text" id="f1_user_id" name="user_id" value="{{if $where_values.user_id>0}}{{$where_values.user_id|escape:"html"}}{{/if}}" size="{{$i.size|default:"12"}}">
						</td>
					</tr>
					<tr class="tr1">
						<td>chara_id</td>
						<td>
							<input type="text" id="f1_chara_id" name="chara_id" value="{{if $where_values.chara_id>0}}{{$where_values.chara_id|escape:"html"}}{{/if}}" size="{{$i.size|default:"12"}}">
						</td>
					</tr>
				</tbody>
				<tbody id="f1_where_extension">
					<input type="hidden" name="table" v-model="table">
					<tr v-repeat="cols" v-attr="class:trc">
						<td v-text="name">
							
						</td>
						<td>
							<input type="text" size="4" value="" v-model="val" v-attr="name:col">
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<div align="center">
				　<button type="submit" id="f1_where_update">{{$strres.b001}}</button>
				　<button type="button" id="f1_csv_export">{{$strres.b003}}</button>
			</div>
		</fieldset>
	</form>
	<form name="f3" method="POST" id="f3">
		<input type="hidden" name="f3_submit" value="1">
		<input type="hidden" id="f3_export_target" name="export_target" value="">
		<input type="hidden" name="export" value="1">
	</form>
</div>
<div align="left" style="white-space: nowrap;width:40%;float:left;">
	<form id="fu">
		<fieldset>
			<legend>ユーザ情報</legend>
			<table class="main2" width="100%">
				<tbody>
					<tr class="tr1">
						<td>
							UID
						</td>
						<td align="right"><span id="fu_uid"></span></td>
					</tr>
					<tr class="tr2">
						<td>
							ユーザID
						</td>
						<td align="right"><span id="fu_user_id"></span></td>
					</tr>
					<tr class="tr1">
						<td>
							会員登録日
						</td>
						<td align="right"><span id="fu_regist_date"></span></td>
					</tr>
					<tr class="tr2">
						<td>
							最終ログイン日
						</td>
						<td align="right"><span id="fu_last_login_date"></span></td>
					</tr>
					<tr class="tr1">
						<td>
							累計決済額
						</td>
						<td align="right"><span id="fu_rm_p"></span></td>
					</tr>
					<tr class="tr2">
						<td>
							累計課金ポイント購入額
						</td>
						<td align="right"><span id="fu_rm_b"></span></td>
					</tr>
					<tr class="tr1">
						<td>
							累計課金ポイント消費額
						</td>
						<td align="right"><span id="fu_rm_u"></span></td>
					</tr>
					<tr class="tr2">
						<td>
							累計課金ポイント消失額
						</td>
						<td align="right"><span id="fu_rm_l"></span></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</form>
</div>

<br style="clear:both" />

<style>
<!--
.f2t_btn {
	height:14px;
	font-size:12px;
	
	margin:0 2px 1px 2px;
	padding:1px 3px 1px 3px;
	
	border: solid 1px #0f60c3;
	border-radius: 5px 5px 0px 0px;
	vertical-align:bottom;
	float:left;
}
.f2t_a { color: #333; text-decoration: none; }
.f2t_a:hover { text-decoration: underline; color : #000; }
-->
</style>
{{strip}}
<div align="left" id="f2_tabs" style="white-space: nowrap;">
	{{foreach name=loglist_t key=k item=i from=$loglist}}
		<a class="f2t_a" name="f2n_{{$k}}" href="#" target="{{$k}}">
			<div id="f2td_{{$k}}" class="f2t_btn {{if $smarty.foreach.loglist_t.first}}gbg_btn_a{{else}}gbg_btn_b{{/if}}">
				{{$i.name}}
			</div>
		</a>
	{{/foreach}}
</div>
{{/strip}}

<br style="clear:both;">

<script type="text/javascript">
<!--

var f1_postdata = {};

$(document).ready(function(){
	
	var f1_extension = new Vue({
		el: '#f1_where_extension', 
		data : {cols:[]}
	});
	
	// CSV出力
	$("#f1_csv_export").click(function(){
		$("#f3").submit();
		//document.getElementById("f3").submit();
	});
	
	// ユーザ情報の読み込み最初から検索条件が指定されてた時用 (他画面から移ってきた時とか)
	$.ajax({
		type: "GET",
		url: "inline.php",
		dataType: "json",
		data: {"menukey"      : "{{$menukey}}"
		     , "include_file" : "plugin/{{$menukey}}/log_search_db.php"
		     , "ajax"         : 1
		     , "fu_submit"    : 1
		}, 
		success: function(dat){
			if (dat.result == "success") {
				$("#fu_uid").text(dat.uid);
				$("#fu_user_id").text(dat.usr);
				$("#fu_regist_date").text(dat.red);
				$("#fu_last_login_date").text(dat.lld);
				$("#fu_rm_p").text(dat.rmp);
				$("#fu_rm_b").text(dat.rmb);
				$("#fu_rm_u").text(dat.rmu);
				$("#fu_rm_l").text(dat.rml);
			}
		}
	});
	
	// 検索条件の変更
	$("#f1_where_update").click(function(){
		
		f1_postdata = [
			{"name":"f1_submit","value":"1"}
			, {"name":"ajax","value":"1"}
			, {"menukey":"menukey","value":"{{$menukey}}"}
			, {"name":"include_file","value":"plugin/{{$menukey}}/log_search_db.php"}
		];
		$("#f1 input").each(function(){
			f1_postdata.push({"name" : $(this).attr("name"), "value" : $(this).val()});
		});
		
		$.get("inline.php", f1_postdata, function(data){
			$('.grid_table').each(function(){$(this).attr('isLoad', '0');});
			$('.current_grid').flexReload();
			$('.current_grid').attr('isLoad', 1);
			
			$.ajax({
				type: "GET",
				url: "inline.php",
				dataType: "json",
				data: {"menukey"      : "{{$menukey}}"
				     , "include_file" : "plugin/{{$menukey}}/log_search_db.php"
				     , "fu_submit"    : 1
				     , "ajax"         : 1
				}, 
				success: function(dat){
					if (dat.result == "success") {
						$("#fu_uid").text(dat.uid);
						$("#fu_user_id").text(dat.usr);
						$("#fu_regist_date").text(dat.red);
						$("#fu_last_login_date").text(dat.lld);
						$("#fu_rm_p").text(dat.rmp);
						$("#fu_rm_b").text(dat.rmb);
						$("#fu_rm_u").text(dat.rmu);
						$("#fu_rm_l").text(dat.rml);
					}
				}
			});
		});
		
		return false;
	});
	
	// タブ選んだ時の挙動
	$('.f2t_a').click(function(){
		var t = $(this).attr('target');
		
		// テーブル情報の取得と検索条件の変更
		f1_extension.table = t;
		var getparams = {
			 menukey:"{{$menukey}}"
			,include_file:"plugin/{{$menukey}}/log_search_db.php"
			,ajax:1
			,tableinfo:1
			,table:t
		};
		$.get("inline.php", getparams, function(data){
			f1_extension.cols = data;
			
			// tr1, tr2 の調整
			var n = 1;
			for (var k in data) {
				f1_extension.cols[k].trc = "tr" + n;
				n = (n == 1 ? 2 : 1);
			}
		});
		
		// CSV出力の対象を変更
		$("#f3_export_target").val(t);
		
		// タブの表示切替
		$('.f2_grids').hide();
		$('#f2_d-' + t).show();
		
		// 現在表示しているタブ を変更
		$('.grid_table').removeClass('current_grid');
		$('#f2_' + t).addClass('current_grid');
		
		// 再読み込みが必要なら再読み込み
		if ($('#f2_' + t).attr('isLoad') == '0') {
			$('#f2_' + t).flexReload();
			$('#f2_' + t).attr('isLoad', '1');
		}
		
		// タブの方の表示切替
		$('.f2t_a > .gbg_btn_a').removeClass('gbg_btn_a').addClass('gbg_btn_b');
		$('#f2td_' + t).removeClass('gbg_btn_b').addClass('gbg_btn_a');
		
		return false;
	});
	
	
{{foreach name=loglist_j key=k item=i from=$loglist}}
	$('#f2_{{$k}}').flexigrid(
		{
			url: 'inline.php?menukey={{$menukey}}&include_file=plugin/{{$menukey}}/log_search_db.php&json=1&ajax=1&type=history&k={{$k}}',
			title: 'logs', 
			nomsg: 'nomsg', 
			colModel :
			[
				{name:'log_id', display:'ログID', sortable:false, align:'right', width:60 }
				{{foreach name=f2_history_colmodel key=kk item=ii from=$i.cols }}
					, {name:'{{$kk}}', display:'{{$ii.name}}', sortable:false, align:{{if $ii.type=='INTEGER'}}'right'{{else}}'left'{{/if}}, width:{{if $ii.width}}{{$ii.width}}{{elseif $ii.type=='INTEGER'}}70{{else}}200{{/if}} }
				{{/foreach}}
			],
			
			sortname: '1',
			sortorder: 'desc',
			autoload: {{if $smarty.foreach.loglist_j.first}}true{{else}}false{{/if}}, 
			
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
		}
	);
{{/foreach}}
	
	// JST対応
	$(".jst_show_check").change(function(){
		if ($(this).attr("checked") == "checked") {
			$(".jst_" + $(this).attr("target")).show();
			$(".notjst_" + $(this).attr("target")).hide();
		}
		else {
			$(".jst_" + $(this).attr("target")).hide();
			$(".notjst_" + $(this).attr("target")).show();
		}
	});
});
//-->
</script>

{{foreach name=loglist_d key=k item=i from=$loglist}}
<div align="left" class="f2_grids" id="f2_d-{{$k}}" style="white-space: nowrap;{{if ! $smarty.foreach.loglist_d.first}}display:none;{{/if}}">
	<table id="f2_{{$k}}" class="grid_table" isLoad="0">
		<thead>
		</thead>
		<tfoot>
		</tfoot>
		<tbody>
		</tbody>
	</table>
</div>
{{/foreach}}
