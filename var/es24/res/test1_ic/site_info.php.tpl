<style>
<!--
span.f1_load {
	border: 1px solid gray;
	color: blue;
	border-radius:2px;
	cursor: pointer;
}
tr.trh th {
	white-space:nowrap;
	vertical-align:middle;
}
#f3tbl {width:100%}
-->
</style>
<script language="javascript">
<!--
$(function(){
	
	$('.f1_load').click(function(){
		$.ajax({
			url: "inline.php?menukey={{$menukey}}&include_file=plugin/{{$menukey}}/site_info.php&json=1&id=" + $(this).attr("editid"),
			type: "POST",
			dataType: 'json',
			success: function(data) {
				
				if (data && data.id) {
					
					$('#f1_id_display').text(data.id);
					$('#f1_id').val(data.id);
					
					$('#f1_info_type > option[value="'+data.info_type+'"]').attr("selected", "selected");
					$('#f1_link_type > option[value="'+data.link_type+'"]').attr("selected", "selected");
					
					$('#f1_subject').text(data.subject);
					$('#f1_body').text(data.body);
					
					$('#f1_link').val(data.link);
					$('#f1_start_date').val(data.start_date);
					$('#f1_end_date').val(data.end_date);
					$('#f1_expire_date').val(data.expire_date);
					
					$('#f1_priority > option[value="'+data.priority+'"]').attr("selected", "selected");
					
					$('#f1_subject').focus();
					
					$('.toggle_next > legend').each(function(){
						if ($(this).next().is(':visible')) {
							$(this).click();
						}
					});
					if ($('#f1_div').is(':hidden')) {
						$('#f1_div').prev().click();
					}
				}
			}
		});
	});
	
	
	$('.f4-clear').click(function(){
		var t = $(this).attr('data-clear-target');
		$('.' + t).each(function(){
			var $this = $(this);
			$this.val($this.attr('data-clear-value'));
		});
	});
	
});
//-->
</script>
{{if $message}}
<p>
	{{$message}}
</p>
<hr>
{{/if}}

<div style="float:left">
	<form method="POST" id="f1">
		<input type="hidden" name="f1_submit" value="1">
		<fieldset class="toggle_next">
			<legend>編集</legend>
			<div id="f1_div" style="display:none">
				{{if $message}}
					<div align="left" style="font-size: 12px; padding-left: 1em;">{{$message}}</div>
				{{/if}}
				
				<table class="main2">
					<tbody>
						<tr class="trh">
							<td align="center">項目名</td>
							<td align="center">値</td>
						</tr>
						<tr class="tr1">
							<td align="center">ID</td>
							<td align="left">
								<span id="f1_id_display">新規作成</span>
								<input id="f1_id" type="hidden" name="id" value="">
							</td>
						</tr>
						<tr class="tr2">
							<td align="center">お知らせタイプ</td>
							<td align="left">
								<select id="f1_info_type" id="" name="info_type">
									{{foreach item=i key=k from=$info_types}}
										<option value="{{$k}}">{{$i}}</option>
									{{/foreach}}
								</select>
							</td>
						</tr>
						<tr class="tr1">
							<td align="center">表題</td>
							<td align="left"><textarea id="f1_subject" name="subject" cols="60" rows="3"></textarea></td>
						</tr>
						<tr class="tr2">
							<td align="center">内容</td>
							<td align="left">
								(リンク先が設定されていると表示されません)<br>
								<textarea id="f1_body" name="body" cols="60" rows="10"></textarea>
								
								<br>
								画像一覧:images/infoimg/<input type="text" name="body_img" value="" size="20" class="" data-clear-value="" id="f1_body_img" choser="infoimgchoser">
								
							</td>
						</tr>
						<tr class="tr1">
							<td align="center">リンク種別</td>
							<td align="left">
								<select id="f1_link_type" id="" name="link_type">
									<option value="">なし</option>
									{{foreach item=i key=k from=$link_types}}
										<option value="{{$k}}">{{$i}}</option>
									{{/foreach}}
								</select>
							</td>
						</tr>
						<tr class="tr2">
							<td align="center">リンク先</td>
							<td align="left">
								<input id="f1_link" type="text" name="link" value="" size="60" maxlength="255">
							</td>
						</tr>
						<tr class="tr1">
							<td align="center">表示開始日時</td>
							<td align="left">
								<input type="text" id="f1_start_date" name="start_date" value="" size="20" maxlength="20" class="datetime">
							</td>
						</tr>
						<tr class="tr2">
							<td align="center">新着表示終了日時</td>
							<td align="left">
								<input type="text" id="f1_end_date" name="end_date" value="" size="20" maxlength="20" class="datetime">
							</td>
						</tr>
						<tr class="tr2">
							<td align="center">公開終了日時</td>
							<td align="left">
								<input type="text" id="f1_expire_date" name="expire_date" value="" size="20" maxlength="20" class="datetime">
							</td>
						</tr>
						<tr class="tr1">
							<td align="center">表示優先順位</td>
							<td align="left">
								<select id="f1_priority" name="priority">
									{{foreach item=i key=k from=$priorities}}
										<option value="{{$k}}">{{$i}}</option>
									{{/foreach}}
								</select>
							</td>
						</tr>
					</tbody>
				</table>
				<div style="text-align:center">
					<select name="nl2br">
						<option value="0" selected>改行を&lt;br /&gt;に変換しない</value>
						<option value="1">改行を&lt;br /&gt;に変換する</value>
					</select>
					　<button type="submit" name="submit" onclick="return confirm('更新します、よろしいですか？')">更新</button>
				</div>
			</div>
		</fieldset>
	</form>
</div>
<div style="float:left">
	<form method="POST" id="f4">
		<input type="hidden" name="f4_submit" value="1">
		<fieldset class="toggle_next">
			<legend>大バナー管理</legend>
			<div>
				<table class="main2">
					<thead>
						<tr class="trh">
							<td align="center"></td>
							<td align="center">順番</td>
							<td align="center">画像ファイルパス</td>
							<td align="center">公開開始日時</td>
							<td align="center">公開終了日時</td>
							<td align="center">リンク先</td>
							<td align="center"></td>
						</tr>
					</thead>
					<tbody id="banner_order_sortable">
						{{foreach item=i key=k from=$banners}}
							<tr id="banner_order_sortable_line_{{$k}}" class="{{cycle values="tr1,tr2"}}">
								<td align="center">
									<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
								</td>
								<td>
									<input type="hidden" class="order" name="order[]" value="{{$k}}">
									<span class="ordertext">{{$k}}</span>
									<input type="hidden" name="banner[{{$k}}][id]" value="{{$i.id}}">
								</td>
								<td align="center">
									<input type="text" name="banner[{{$k}}][img]" value="{{$i.img}}" size="20" class=" f4-banner-{{$k}}" data-clear-value="" id="f4lb_{{$k}}_img" choser="lbchoser">
								</td>
								<td align="center">
									<input type="text" name="banner[{{$k}}][start_date]" value="{{$i.start_date|default:"0000-01-01 00:00:00"}}" size="18"  class="datetime f4-banner-{{$k}}" id="f4_banner_start_date_{{$k}}" data-clear-value="0000-01-01 00:00:00">
								</td>
								<td align="center">
									<input type="text" name="banner[{{$k}}][end_date]" value="{{$i.end_date|default:"0000-01-01 00:00:00"}}" size="18"  class="datetime f4-banner-{{$k}}" id="f4_banner_end_date_{{$k}}" data-clear-value="0000-01-01 00:00:00">
								</td>
								<td>
									<input type="text" name="banner[{{$k}}][link]" value="{{if $i.link}}{{$i.link}}{{else}}{{/if}}" size="4" class=" f4-banner-{{$k}}" data-clear-value="" >
								</td>
								<td>
									<input type="button" name="" value="clear" class="f4-clear" data-clear-target="f4-banner-{{$k}}">
								</td>
							</tr>
						{{/foreach}}
					</tbody>
				</table>
				<div align="center">
					<button type="submit" name="submit"  onclick="return confirm('更新します、よろしいですか？')">更新</button>
				</div>
			</div>
		</fieldset>
	</form>
</div>

<div style="float:left">
	<form method="POST" id="f4s">
		<input type="hidden" name="f4s_submit" value="1">
		<fieldset class="toggle_next">
			<legend>小バナー管理</legend>
			<div>
				<table class="main2">
					<thead>
						<tr class="trh">
							<td align="center"></td>
							<td align="center">順番</td>
							<td align="center">画像ファイルパス</td>
							<td align="center">公開開始日時</td>
							<td align="center">公開終了日時</td>
							<td align="center">リンク先</td>
							<td align="center"></td>
						</tr>
					</thead>
					<tbody id="banner_order_sortable">
						{{foreach item=i key=k from=$banners_small}}
							<tr id="banner_order_sortable_line_{{$k}}" class="{{cycle values="tr1,tr2"}}">
								<td align="center">
									<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
								</td>
								<td>
									<input type="hidden" class="order" name="order[]" value="{{$k}}">
									<span class="ordertext">{{$k}}</span>
									<input type="hidden" name="banner[{{$k}}][id]" value="{{$i.id}}">
								</td>
								<td align="center">
									<input type="text" name="banner[{{$k}}][img]" value="{{$i.img}}" size="20" class=" f4s-banner-{{$k}}" data-clear-value="" id="f4sb_{{$k}}_img" choser="sbchoser">
								</td>
								<td align="center">
									<input type="text" name="banner[{{$k}}][start_date]" value="{{$i.start_date|default:"0000-01-01 00:00:00"}}" size="18"  class="datetime f4s-banner-{{$k}}" id="f4s_banner_start_date_{{$k}}" data-clear-value="0000-01-01 00:00:00">
								</td>
								<td align="center">
									<input type="text" name="banner[{{$k}}][end_date]" value="{{$i.end_date|default:"0000-01-01 00:00:00"}}" size="18"  class="datetime f4s-banner-{{$k}}" id="f4s_banner_end_date_{{$k}}" data-clear-value="0000-01-01 00:00:00">
								</td>
								<td>
									<input type="text" name="banner[{{$k}}][link]" value="{{if $i.link}}{{$i.link}}{{else}}{{/if}}" size="4" class=" f4s-banner-{{$k}}" data-clear-value="" >
								</td>
								<td>
									<input type="button" name="" value="clear" class="f4-clear" data-clear-target="f4s-banner-{{$k}}">
								</td>
							</tr>
						{{/foreach}}
					</tbody>
				</table>
				<div align="center">
					<button type="submit" name="submit"  onclick="return confirm('更新します、よろしいですか？')">更新</button>
				</div>
			</div>
		</fieldset>
	</form>
</div>

<div style="float:left">
	<fieldset class="toggle_next">
		<legend>画像アップロード</legend>
		<div style="display:none">
			<form method="POST" id="f5" enctype="multipart/form-data">
				<input type="hidden" name="f5_submit" value="1">
				<fieldset>
					<legend>アップロード</legend>
					<table>
						<tr>
							<td>
								用途
							</td>
							<td>
								<select name="image_dir">
									<option value=""></option>
									{{foreach item=i key=k from=$image_dir_labels}}
										<option value="{{$k}}">{{$i}}</option>
									{{/foreach}}
								</select>
							</td>
						</tr>
						<tr>
							<td>
								元ファイル<br>
								※gif, jpeg, png のみ許可
							</td>
							<td>
								<input type="file" name="banner_img" size="40">
							</td>
						</tr>
						<tr>
							<td>
								アップロード時のファイル名
							</td>
							<td>
								<input type="text" name="tofilename" size="20">
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<button type="submit" name="" onclick="alert('転送を行うまでサービス環境には乗りません。');return confirm('送信します、よろしいですか？')">送信</button>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
			<form method="POST" id="f5b">
				<input type="hidden" name="f5b_submit" value="1">
				<fieldset>
					<legend>転送</legend>
					<select name="image_dir">
						<option value=""></option>
						{{foreach item=i key=k from=$image_dir_labels}}
							<option value="{{$k}}">{{$i}}</option>
						{{/foreach}}
					</select>
					<button type="submit" name="" onclick="return confirm('アップロードしたファイルを転送します、よろしいですか？')">転送</button>
				</fieldset>
			</form>
		</div>
	</fieldset>
</div>

{{*
<div style="float:left">
	<fieldset class="toggle_next">
		<legend>記事用画像アップロード</legend>
		<div style="display:none">
			<form method="POST" id="f3" enctype="multipart/form-data">
				<input type="hidden" name="f6_submit" value="1">
				<fieldset>
					<legend>アップロード</legend>
					<table>
						<tr>
							<td>
								元ファイル<br>
								※gif, jpeg, png のみ許可
							</td>
							<td>
								<input type="file" name="infoimg_img" size="40">
							</td>
						</tr>
						<tr>
							<td>
								アップロード時のファイル名
							</td>
							<td>
								<input type="text" name="infoimg" id="f6_infoimg" size="10">
								<span id="f6_infoimg_filename_check" style="display:none">ファイルの有無を確認中</span>
								<span id="f6_infoimg_filename_ok" style="display:none">このファイル名は使用されてません</span>
								<span id="f6_infoimg_filename_ng" style="display:none" class="error">※指定したファイルは既に使用されています</span>
							</td>
						</tr>
						<tr>
							<td>
								記事中に使う際のタグ
							</td>
							<td>
								<input type="text" id="f6_infoimg_tag" value="" size="40" readonly>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<button type="submit" name="" onclick="alert('転送を行うまでサービス環境には乗りません。');return confirm('送信します、よろしいですか？')">送信</button>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
			<form method="POST" id="f6b">
				<input type="hidden" name="f6b_submit" value="1">
				<fieldset>
					<legend>転送</legend>
					<div align="center">
						<button type="submit" name="" onclick="return confirm('アップロードしたファイルを転送します、よろしいですか？')">転送</button>
					</div>
				</fieldset>
			</form>
		</div>
	</fieldset>
</div>
*}}

{{foreach key=l item=d from=$images}}
	<div id="{{$l}}choser" class="choser">
		<span class="{{$l}} choseitem" value=""><span>clear</span></span><br>
		{{foreach key=k item=i from=$d}}
			<div class="{{$l}} choseitem" value="{{$i}}" title="{{$i}}"><span class="">{{$i}}</span><br>
			<img src="inline.php?menukey={{$menukey}}&include_file=plugin/{{$menukey}}/site_info.php&image_dir={{$l}}&img={{$i}}" width="150" height="40" alt=""></div>
		{{/foreach}}
	</div>
{{/foreach}}


<style>
<!--
.choser {
	position:absolute;
	width:500px;
	height:200px;
	border: 1px solid gray;
	border-radius: 2px;
	background-color:#fdfefe;
	margine:3px;
	padding:5px;
	overflow-y:scroll;
	display:none;
	line-height:1.5;
	float:left;"
}
span.choseitem {
	border: 1px solid green;
	border-radius: 2px;
	cursor: pointer;
	margin:2px;
	padding:2px;
	white-space:nowrap;
}
div.choseitem {
	float:left;
	text-align:center;
	border: 1px solid green;
	border-radius: 2px;
	cursor: pointer;
	margin:2px;
	padding:2px;
	white-space:nowrap;
}
.nobr {white-space:nowrap;}
span.shownext{
	cursor: pointer;
	color:blue;
	text-decoration:underline;
}
-->
</style>
<script>
<!--
var timer_handle = null;
$(function(){
	
	$("input[choser]").each(function(){
		var l = $(this).attr('id') + "_chooserlabel";
		$(this).attr("for", l);
		if ($(this).next().is("span:not([id])")) {
			$(this).next().attr('id', l).addClass("nobr");
		} else {
			$(this).after('<span class="nobr" id="' + l + '"></span>');
		}
	}).focus(function(){
		// 選択箱を出す input.text にフォーカスが入ったら
		
		// 閉じる処理の
		$(".choser:visible").hide();
		if (timer_handle) {window.clearTimeout(timer_handle);timer_handle = null;}
		
		// 一覧を開いてどこをターゲットするかメモ
		$("#" + $(this).attr("choser")).show().position({
			my: "left top", 
			at: "left bottom", 
			of: $(this)
		}).disableSelection().attr("target", $(this).attr("id"));
		
	}).blur(function(){
		// フォーカスが外れたら
		//$("#" + $(this).attr("choser")).attr("target", "").hide();
		//timer_handle = window.setTimeout(function(){$(".choser").hide();timer_handle = null;}, 100);
	}).change(function(){
		// 値が変更されたら
		//$("#" + $(this).attr("choser")).attr("target", "").hide();
		
		timer_handle = window.setTimeout(function(){$(".choser").hide();timer_handle = null;}, 100);
	});
	
	// よその要素にフォーカスが移ったら選択リストを閉じる
	$(":not([choser])").focus(function(){
		timer_handle = window.setTimeout(function(){$(".choser").hide();timer_handle = null;}, 100);
	});
	
	$(".choser .choseitem").click(function(){
		var p = $(this).parents(".choser").get(0);
		var t = $(p).attr("target")
		if (t) {
			$("#" + t).val($(this).attr("value"));
			$("#" + t).change();
			$("#" + t).blur();
			//$("#" + $("#" + t).attr("choser")).attr("target", "").hide();
			
			if ($("#" + t).attr("for")) {
				$("#" + $("#" + t).attr("for")).next().attr("src", "inline.php?menukey={{$menukey}}&include_file=plugin/{{$menukey}}/site_info.php&banner=" + $(this).attr("title"));
//				$("#" + $("#" + t).attr("for")).text("(" + $(this).attr("title") + ")");
			}
			
			timer_handle = window.setTimeout(function(){$(".choser").hide();timer_handle = null;}, 100);
		}
	});
	
	$("input.banner").change(function(){
		if ($(this).val() > 0) {
			$(this).next().attr("src", "inline.php?menukey={{$menukey}}&include_file=plugin/{{$menukey}}/site_info.php&banner=" + $(this).val());
		} else {
			$(this).next().text("");
		}
	});
	
	
	// 記事中画像のファイル名
	$("#f6_infoimg").change(function(){
		$("#f6_infoimg_tag").val('<img src="{{$infoimg_dir}}/' + $(this).val() + '" alt="">');
		
		var getdata = {
			"menukey" : "{{$menukey}}", 
			"include_file" : "plugin/{{$menukey}}/site_info.php", 
			"json" : 1, 
			"infoimg_exists" :  $(this).val()
		};
		$("#f6_infoimg_filename_check").show();
		$.get("inline.php", getdata, function(data){
			if (data.result) {
				$("#f6_infoimg_filename_check").hide();
				if (data.exists) {
					// すでにそのファイルは存在する
					$("#f6_infoimg_filename_ok").hide();
					$("#f6_infoimg_filename_ng").show();
				} else {
					// そのファイルは存在しない
					$("#f6_infoimg_filename_ok").show();
					$("#f6_infoimg_filename_ng").hide();
				}
			}
		}, "json")
		
	});
	
	$('.shownext').click(function(){
		$(this).hide();
		$(this).next().show();
	});

});
//-->
</script>
<br style="clear:both">

<script language="javascript">
<!--
function f2_submit(offset, limit, order, desc) {
	document.getElementById("f2_offset").value = offset;
	document.getElementById("f2_limit").value = limit;
	document.getElementById("f2_order").value = order;
	document.getElementById("f2_desc").value = desc;
	
	document.getElementById("f2").submit();
}

$(function(){
	
	// バナーの並び替えとか
	$("#banner_order_sortable").sortable({
		disabled: false, 
		axis: "y", 
		stop: function(ev, ui) {
			var a = $("#banner_order_sortable").sortable("toArray");
			for (i = 0;i < a.length;i++) {
				$("#" + a[i] + " span.ordertext").text(i)
			}
		}
	});
	$("#banner_order_sortable").disableSelection();
	
	// 検索フォームの表示切替とか
	//$('fieldset.toggle_next > legend').children(':last').after('<span class="toggle_arrow">▽</span><span class="toggle_arrow" style="display:none">△</span>')
	$('fieldset.toggle_next > legend').each(function(){
		if ($(this).next().is(":visible")) {
			$(this).html($(this).html() + '<span class="toggle_arrow" style="display:none">▽</span><span class="toggle_arrow">△</span>');
		} else {
			$(this).html($(this).html() + '<span class="toggle_arrow">▽</span><span class="toggle_arrow" style="display:none">△</span>');
		}
	});
	
	$('fieldset.toggle_next > legend').click(function(e){
		$(this).next().toggle(500);
		$(this).children(".toggle_arrow").toggle();
	}).css('cursor', 'pointer');
	
});

//-->
</script>
<br style="clear:both;>
<div align="center">
	<form name="f2" method="POST" id="f2">
		<input type="hidden" name="f2_submit" value="1">
		<input type="hidden" name="offset" id="f2_offset" value="{{$paging.offset}}">
		<input type="hidden" name="limit" id="f2_limit" value="{{$paging.limit}}">
		<input type="hidden" name="order" id="f2_order" value="{{$paging.order}}">
		<input type="hidden" name="desc" id="f2_desc" value="{{$paging.desc}}">
	</form>
	<div width="100%" align="right">
		{{foreach key=k item=i from=$paging.pages}}
			{{if $k == $paging.offset}}
				<big>{{$i}}</big>
			{{else}}
				<a href="javascript:f2_submit({{$k}}, {{$paging.limit}}, '{{$paging.order}}', '{{$paging.desc}}');">{{$i}}</a>
			{{/if}}
		{{/foreach}}
		　({{$paging.start}} - {{$paging.end}} /{{$paging.max}})　
		<select name="limit" onchange="f2_submit(0, this.value, '{{$paging.order}}', '{{$paging.desc}}');">
			{{foreach item=i from=$paging.limit_list}}
				<option value="{{$i}}" {{if $i == $paging.limit}}selected{{/if}}>{{$i}}</option>
			{{/foreach}}
		</select>
	</div>
	
	
	{{if $records}}
		<form name="f3" method="POST">
			<input type="hidden" name="f3_submit" value="1">
			<fieldset>
				<input type="hidden" name="f3_submit" value=1>
				<legend>
					お知らせ一覧
				</legend>
				<div>
					<table id="f3tbl" class="main2">
						<tbody>
							<tr class="trh">
								<th rowspan="2">ID</th>
								<th>表題</th>
								<th>掲載期間</th>
								<th rowspan="2">編集</th>
								<th rowspan="2">削除</th>
							</tr>
							<tr class="trh">
								<th>お知らせ内容</th>
								<th>リンク先</th>
							</tr>
							
							{{foreach key=k item=i from=$records}}
								<tr class="tr{{cycle values="1,1,2,2"}}">
									<td rowspan="2">
										{{$i.id}}
									</td>
									<td style="border-bottom:1px solid gray;">
										{{$i.subject|escape}}
									</td>
									<td rowspan="2">
										{{$i.start_date|escape}}　～　{{$i.end_date|escape}}<br>
										<br>
										
										{{if $i.link_type}}
											inapp://{{$i.link_type}}/{{$i.link}}
										{{else}}
											
										{{/if}}
										
										
										<br>
									</td>
									
									<td rowspan="2">
										<span class="f1_load" editid="{{$i.id}}">編集</span>
									</td>
									<td rowspan="2">
										<input type="checkbox" name="delete_id[]" value="{{$i.id}}">
									</td>
								</tr>
								<tr class="tr{{cycle values="1,1,2,2"}}">
									<td>
										<span class="shownext">(show)</span>
										<div style="display:none">
											{{if $i.body}}
												{{$i.body|escape|nl2br}}
											{{else}}
												no data
											{{/if}}
										</div>
									</td>
								</tr>
								
							{{/foreach}}
							
						</tbody>
					</table>
					<input type="hidden" name="delete" value=1><br>
{{*
					<button type="button" name="insert_btn" onclick="window._edit_frame.location.href('inline.php?include_file={$if_url}&menukey={$menukey}&id=');document.getElementById('_edit_frame').focus()">新規登録</button>
					　
*}}
					<button type="submit" name="delete_btn" onclick="return confirm('チェックした項目を削除します、よろしいでしょうか？')">削除</button>
				</div>
			</fieldset>
		</form>
	{{else}}
		<p>現在お知らせはありません。</p>
	{{/if}}
</div>
