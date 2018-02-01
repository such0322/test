<html>
<head>
<title>{{$strres.system_name}}{{$softname}}</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="css/es.css" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="images/favicon.ico">

<link rel="stylesheet" type="text/css" media="screen" href="js/jquery/css/cupertino/jquery-ui-1.8.16.custom.css" />
<link rel="stylesheet" type="text/css" media="screen" href="js/jqplugin/jdpicker_1.0.3/jdpicker.css" />
<link rel="stylesheet" type="text/css" media="screen" href="js/jqplugin/ui-timepickr/jquery.timepickr.css" />
<link rel="stylesheet" type="text/css" media="screen" href="js/anytime/anytime.css" />
<link rel="stylesheet" type="text/css" media="screen" href="js/jqplugin/flexigrid/css/flexigrid/flexigrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="js/jqplot/jquery.jqplot.css" />

<script type="text/javascript" language="javascript" src="js/jquery/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" language="javascript" src="js/jqplugin/jdpicker_1.0.3/jquery.jdpicker.js"></script>
<script type="text/javascript" language="javascript" src="js/jqplugin/jquery.cookie.js"></script>
<script type="text/javascript" language="javascript" src="js/jqplugin/ui-timepickr/jquery.timepickr.min.js"></script>
<script type="text/javascript" language="javascript" src="js/anytime/anytime.js"></script>
<script type="text/javascript" language="javascript" src="js/jqplugin/flexigrid/flexigrid.js"></script>
<!--[if IE]>
<script type="text/javascript" language="javascript" src="js/jqplot/excanvas.min.js"></script>
<![endif]-->
<script type="text/javascript" language="javascript" src="js/jqplot/jquery.jqplot.min.js"></script>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_preloadImages() { //v3.0
	var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
	var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
	if(a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];} }
}

function MM_swapImgRestore() { //v3.0
	var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_findObj(n, d) { //v4.01
	var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
	d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
	if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
	for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
	if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
	var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
	if((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function toggle_select(tr, id_name) {
	if (tr.className == 'trselect') {
		tr.className = tr.getAttribute('org_class');
		tr.setAttribute('id', tr.getAttribute('org_id'));
	}
	else {
		var obj = document.getElementById(id_name);
		if (obj) {
			obj.className = obj.getAttribute('org_class');
			obj.setAttribute('id', '');
		}
		tr.setAttribute('org_class', tr.className);
		tr.setAttribute('org_id', tr.getAttribute('id'));
		tr.setAttribute('id', id_name);
		tr.setAttribute('className', 'trselect');
	}
}


// よくつかうフォームの設定
$(function(){
	$('input.time').timepickr({
		format24 : "{h:02.d}:{m:02.d}:00", 
		resetOnBlur : false
	});
	$('input.date').jdPicker(
	);
	$("input.datetime").AnyTime_picker({
		format: "%Y-%m-%d %H:%i:00"
		, labelTitle: "日付時刻入力"
		, labelYear: "年"
		, labelMonth: "月"
		, labelDayOfMonth: "日"
		, labelHour: "時"
		, labelMinute: "分"
		, labelSecond: "秒"
		, firstDOW: 0
		//, monthNames: ["睦月", "如月", "弥生", "卯月", "皐月", "水無月", "文月", "葉月", "長月", "神無月", "霜月", "師走"]
		, monthAbbreviations: ["１月", "２月", "３月", "４月", "５月", "６月", "７月", "８月", "９月", "１０月", "１１月", "１２月"]
		, monthNames: ["１月", "２月", "３月", "４月", "５月", "６月", "７月", "８月", "９月", "１０月", "１１月", "１２月"]
		, dayAbbreviations: ["日", "月", "火", "水", "木", "金", "土"]
		, dayNames: ["日", "月", "火", "水", "木", "金", "土"]
	});
	$('input.datetime').after($('<span class="datetime_clear">×</span>').click(function(){$(this).prev().val("");}));
});

// パスワード変更処理用
$(document).ready(function() {
	
	// パスワード変更窓を開く をクリックした時
	$('a[name=es_change_password]').click(function(e) {
		
		//Cancel the link behavior
		e.preventDefault();
		//Get the A tag
		var id = $(this).attr('href');
		
		//Get the screen height and width
		var maskHeight = $(document).height();
		var maskWidth = $(window).width();
		
		//Set height and width to mask to fill up the whole screen
		$('#es_cp_mask').css({'top':0,'left':0,'width':maskWidth,'height':maskHeight,'filter':'Alpha(opacity=30)','-moz-opacity':'0.3','opacity':'0.3'});
		//transition effect
		$('#es_cp_mask').fadeIn(200);
		$('#es_cp_mask').fadeTo(200,0.3);
		
		//Get the window height and width
		var winH = $(window).height();
		var winW = $(window).width();
		
		//Set the popup window to center
		$(id).css('top',  winH/2-$(id).height()/2);
		$(id).css('left', winW/2-$(id).width()/2);
		
		//transition effect
		$(id).fadeIn(200);
		
		$("select").hide();
	});
	
	// 窓閉じるボタンをクリックした場合
	$('.window .close').click(function (e) {
		//Cancel the link behavior
		e.preventDefault();
		$('#es_cp_mask, .window').hide();
		
		$("#_es_change_password_cur").attr("value", "");
		$("#_es_change_password_new").attr("value", "");
		$("#_es_change_password_cnf").attr("value", "");
		
		$("select").show();
	});
	
	// マスクの方をクリックした場合
	$('#es_cp_mask').click(function () {
		$(this).hide();
		$('.window').hide();
		
		$("#_es_change_password_cur").attr("value", "");
		$("#_es_change_password_new").attr("value", "");
		$("#_es_change_password_cnf").attr("value", "");
		
		$("select").show();
	});
});

$(function(){
	// submitイベントハンドラ
	$("#_es_change_password").submit(function(){
		var data = "_es_change_password=1&user=" + $("#_es_change_password_user").attr("value")
		         + "&cur=" + $("#_es_change_password_cur").attr("value")
		         + "&new=" + $("#_es_change_password_new").attr("value")
		         + "&cnf=" + $("#_es_change_password_cnf").attr("value")
		;
		
		$.ajax({
			type: "POST",
			url: "pass.php",
			data: data,
			success: function(msg){
				if (res == "OK") {
					alert( "パスワードを変更しました" );
				}
				else {
					alert( "パスワードの変更に失敗しました" );
				}
			},
			error: function(msg){
				alert( "エラーが発生しました : " + msg );
			}
		});
		
		return false;
	});
});

//-->
</script>

<style>
/*
	パスワード変更フォーム用のスタイル類、
	"#es_cp_mask" の z-index は "#es_cp_boxes .window" より低くないとまずい  
*/
#es_cp_mask {
  position:absolute;
  z-index:9000;
  background-color:#000;
  display:none;
}
#es_cp_boxes .window {
  position:absolute;
  width:440px;
  height:200px;
  display:none;
  z-index:9999;
  padding:20px;
}
#es_cp_boxes #es_cp_dialog {
  border: 1px solid white;
  background-color: #fefefe; 
}
</style>
</head>
<body bgColor=#ffffff leftmargin="10" topmargin="10" marginwidth="10" marginheight="10">
	<table width="100%" height="100%" border=0 cellPadding=0 cellSpacing=0>
		<tbody>
			<tr>
				<td><img height=1 alt="" src="./images/spacer.gif" width=9 border=0></td>
				<td><img height=1 alt="" src="./images/spacer.gif" width=128 border=0></td>
				<td><img height=1 alt="" src="./images/spacer.gif" width=40 border=0></td>
				<td><img height=1 alt="" src="./images/spacer.gif" width=4 border=0></td>
				<td><img height=1 alt="" src="./images/spacer.gif" width=370 border=0></td>
				<td><img height=1 alt="" src="./images/spacer.gif" width=198 border=0></td>
				<td><img height=1 alt="" src="./images/spacer.gif" width=6 border=0></td>
				<td><img height=1 alt="" src="./images/spacer.gif" width=5 border=0></td>
				<td><img height=1 alt="" src="./images/spacer.gif" width=1 border=0></td>
			</tr>
			<tr>
				<td colSpan=2><img height=19 alt="" src="./images/frame_base_r1_c1.gif" width=137 border=0 name=frame_base_r1_c1></td>
				<td colSpan=4 valign="bottom" background="./images/frame_base_r1_c3.gif" class="px12">
					<table width="100%" height="16" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="bottom">
								<font color="#ffffff">
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td>
												<font color="#ffffff">
													{{$strres.system_name}}{{$softname}}
												</font>
											</td>
											<td align="right" valign="bottom">
{{if $is_login}}
												<a href="#es_cp_dialog" name="es_change_password">{{$strres.i002}}</a>&nbsp;
												<a href="index.php?logout=1"><img src="images/logout.gif" width="75" height="14" border="0"></a>
{{/if}}
											</td>
										</tr>
									</table>
								</font>
							</td>
						</tr>
					</table>
				</td>
				<td colSpan=2 rowSpan=5 valign="top" background="./images/frame_base_r6_c7.gif"><img height=69 alt="" src="./images/frame_base_r1_c7.gif" width=11 border=0 name=frame_base_r1_c7></td>
				<td><img height=19 alt="" src="./images/spacer.gif" width=1 border=0></td>
			</tr>
			<tr>
				<td rowSpan=3 valign="top"><img height=31 alt="" src="./images/frame_base_r2_c1.gif" width=9 border=0 name=frame_base_r2_c1></td>
				<td colSpan=5 background="./images/frame_base_r2_c2.gif"><img src="./images/spacer.gif" alt="" name=frame_base_r2_c2 width=1 height=1 border=0></td>
				<td><img height=7 alt="" src="./images/spacer.gif" width=1 border=0></td>
			</tr>
			<tr>
				<td colSpan=3 valign="middle" background="./images/frame_base_r3_c2.gif" class="px12">
					<font color="#666666">&nbsp;{{$username}}</font>
				</td>
				<td colSpan=2 background="./images/frame_base_r3_c5.gif" class="px12">&nbsp;</td>
				<td><img height=16 alt="" src="./images/spacer.gif" width=1 border=0></td>
			</tr>
			<tr>
				<td colSpan=3><img height=8 alt="" src="./images/frame_base_r4_c2.gif" width=172 border=0 name=frame_base_r4_c2></td>
				<td colSpan=2 rowSpan=2 class="px12" valign="top">

{{if $is_login}}
	{{foreach name=toptabs item=tabs from=$tabitems}}
					<table border="0" cellpadding="0" cellspacing="0" class="submenu" >
						<tr>
		{{foreach name=toptab item=tab from=$tabs}}
							<td>
								<img src="images/spacer.gif" width="6" height="1">
							</td>
							<td height="15">
								<table  border="0" cellspacing="0" cellpadding="0">
									<tr>
			{{if $tab.is_active}}
										<td><img src="images/submenu2_r1_c1.gif" width="6" height="16"></td>
										<td nowrap width="100%" background="images/submenu2_r1_c2.gif" align="center">
			{{else}}
										<td><img src="images/submenu_r1_c1.gif" width="6" height="16"></td>
										<td nowrap width="100%" background="images/submenu_r1_c2.gif" align="center">
			{{/if}}
											<a href="index.php?menukey={{$tab.menukey}}&include_file={{$tab.menukey}}/{{$tab.filename}}">
												{{$tab.title}}
											</a>
										</td>
			{{if $tab.is_active}}
										<td><img src="images/submenu2_r1_c3.gif" width="6" height="16"></td>
			{{else}}
										<td><img src="images/submenu_r1_c3.gif" width="6" height="16"></td>
			{{/if}}
									</tr>
								</table>
							</td>
		{{/foreach}}
						</tr>
					</table>
	{{/foreach}}
{{else}}
					&nbsp;
{{/if}}

				</td>
				<td><img height=8 alt="" src="./images/spacer.gif" width=1 border=0></td>
			</tr>
			<tr>
				<td rowSpan=2 background="./images/frame_base_r5_c1.gif"><img src="./images/spacer.gif" width="1" height="1"></td>
				<td colSpan=2 rowSpan=2 align="center" valign="top" background="./images/frame_base_r5_c2.gif" class="px12">
{{if $is_login}}
	{{* ログイン中ならば左メニューの表示 *}}
					<table width="150" border="0" cellspacing="0" cellpadding="0">
	{{foreach name=leftmenu item=menu from=$menuitems}}
						<tr>
							<td align="center">&nbsp;</td>
						</tr>
						<tr>
							<td align="center">
		{{if $menu.is_active}}
								<span class="leftmenu_active">
		{{else}}
								<span class="leftmenu">
		{{/if}}
		{{if $menu.firstkey}}
									<a href="./index.php?menukey={{$menu.menukey}}&include_file={{$menu.menukey}}/{{$menu.firstkey}}" onMouseOver="MM_swapImage( 'Image1','','images/frame_base_r7_c5.gif',1 )" onMouseOut="MM_swapImgRestore()">
										{{$menu.label}}
									</a>
		{{else}}
									<a href="./index.php?menukey={{$menu.menukey}}" onMouseOver="MM_swapImage( 'Image1','','images/frame_base_r7_c5.gif',1 )" onMouseOut="MM_swapImgRestore()">
										{{$menu.label}}
									</a>
		{{/if}}
								</span>
							</td>
						</tr>
	{{/foreach}}
					</table>

{{else}}
	{{* 非ログイン状態ならばログインフォームの表示 *}}
					<br>
					<form name="INDEX_2" method="post" action="index.php">
						<table border="0" cellpadding="0" cellspacing="0" width="160">
							<tr>
								<td><img src="./images/spacer.gif" width="5" height="1" border="0" alt=""></td>
								<td><img src="./images/spacer.gif" width="120" height="1" border="0" alt=""></td>
								<td><img src="./images/spacer.gif" width="5" height="1" border="0" alt=""></td>
								<td><img src="./images/spacer.gif" width="1" height="1" border="0" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img name="group_r1_c1" src="./images/group_r1_c1.gif" width="160" height="5" border="0" alt=""></td>
								<td><img src="./images/spacer.gif" width="1" height="5" border="0" alt=""></td>
							</tr>
							<tr>
								<td background="./images/group_r2_c1.gif"><img src="./images/spacer.gif" width="1" height="1"></td>
								<td>
									<font color="#778FF4">ACCOUNT</font><br>
									<input style="width:100%" type="text" name="account" class="input"><br>
									<font color="#778FF4">PASSWORD</font><br>
									<input style="width:100%" type="password" name="password" class="input"><br>
									<br>
									<div align="right">
										<button type="submit"><font color="#ffffff"><b>LOGIN</b></font></button>
									</div>
								</td>
								<td background="./images/group_r2_c3.gif"><img src="./images/spacer.gif" width="1" height="1"></td>
								<td><img src="./images/spacer.gif" width="1" height="12" border="0" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img name="group_r3_c1" src="./images/group_r3_c1.gif" width="160" height="6" border="0" alt=""></td>
								<td><img src="./images/spacer.gif" width="1" height="6" border="0" alt=""></td>
							</tr>
						</table>
					</form>
{{/if}}
				</td>
				<td rowSpan=2 background="./images/frame_base_r5_c4.gif"><img src="./images/spacer.gif" width="1" height="1"></td>
				<td><img height=19 alt="" src="./images/spacer.gif" width=1 border=0></td>
			</tr>
			<tr>
				<td colSpan=2 valign="top" class="px12">
					<table cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
						<tr>
							<td><img src="images/spacer.gif" width="10" height="1"></td>
							<td width="100%">
								<br>
{{if $notes}}
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><img name="box_blue_r1_c1" src="images/box_blue_r1_c1.gif" width="8" height="8" border="0" alt=""></td>
										<td width="100%" background="images/box_blue_r1_c2.gif"><img name="box_blue_r1_c2" src="images/box_blue_r1_c2.gif" width="8" height="8" border="0" alt=""></td>
										<td align="right"><img name="box_blue_r1_c3" src="images/box_blue_r1_c3.gif" width="8" height="8" border="0" alt=""></td>
										<td><img src="images/spacer.gif" width="1" height="8" border="0" alt=""></td>
									</tr>
									<tr>
										<td background="images/box_blue_r2_c1.gif"><img name="box_blue_r2_c1" src="images/box_blue_r2_c1.gif" width="8" height="8" border="0" alt=""></td>
										<td width="100%" bgcolor="#EDF3FE">
											{{$notes}}
										</td>
										<td align="right" background="images/box_blue_r2_c3.gif"><img name="box_blue_r2_c3" src="images/box_blue_r2_c3.gif" width="8" height="8" border="0" alt=""></td>
										<td><img src="images/spacer.gif" width="1" height="8" border="0" alt=""></td>
									</tr>
									<tr>
										<td><img name="box_blue_r3_c1" src="images/box_blue_r3_c1.gif" width="8" height="8" border="0" alt=""></td>
										<td width="100%" background="images/box_blue_r3_c2.gif"><img name="box_blue_r3_c2" src="images/box_blue_r3_c2.gif" width="8" height="8" border="0" alt=""></td>
										<td align="right"><img name="box_blue_r3_c3" src="images/box_blue_r3_c3.gif" width="8" height="8" border="0" alt=""></td>
										<td><img src="images/spacer.gif" width="1" height="8" border="0" alt=""></td>
									</tr>
								</table>
{{/if}}
								<br>
{{if $is_login}}
								{{if $is_plugin_error}}
									<p>{{$strres.i004}}</p>
								{{else}}
{{$plugins}}
									{{if $plugins}}
										{{foreach item=i from=$plugins}}
											{{$i}}
										{{/foreach}}
										<hr style="clear:both">
									{{/if}}
									{{$mainstage}}
								{{/if}}
{{else}}
								<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td align="center" valign="middle"><img src="./images/logo.gif" width="361" height="59"></td>
									</tr>
								</table>
{{/if}}
							</td>
						</tr>
					</table>
				</td>
				<td height="100%" colSpan=2 background="./images/frame_base_r6_c7.gif"><img src="./images/spacer.gif" width="1" height="1"></td>
				<td><img height=321 alt="" src="./images/spacer.gif" width=1 border=0></td>
			</tr>
			<tr>
				<td rowSpan=3><img height=29 alt="" src="./images/frame_base_r7_c1.gif" width=9 border=0 name=frame_base_r7_c1></td>
				<td colSpan=3><img height=8 alt="" src="./images/frame_base_r7_c2.gif" width=172 border=0 name=frame_base_r7_c2></td>
				<td colSpan=2><img height=8 alt="" src="./images/frame_base_r7_c5.gif" width=568 border=0 name=frame_base_r7_c5></td>
				<td colSpan=2 rowSpan=3><img height=29 alt="" src="./images/frame_base_r7_c7.gif" width=11 border=0 name=frame_base_r7_c7></td>
				<td><img height=8 alt="" src="./images/spacer.gif" width=1 border=0></td>
			</tr>
			<tr>
				<td colSpan=3><img height=16 alt="" src="./images/frame_base_r8_c2.gif" width=172 border=0 name=frame_base_r8_c2></td>
				<td width="100%" background="./images/frame_base_r8_c5.gif" class="px12">&nbsp;</td>
				<td rowSpan=2><img height=21 alt="" src="./images/frame_base_r8_c6.gif" width=198 border=0 name=frame_base_r8_c6></td>
				<td><img height=16 alt="" src="./images/spacer.gif" width=1  border=0></td>
			</tr>
			<tr>
				<td colSpan=4 background="./images/frame_base_r9_c2.gif"><img src="./images/spacer.gif" alt="" name=frame_base_r9_c2 width=1 height=1 border=0></td>
				<td><img height=5 alt="" src="./images/spacer.gif" width=1 border=0></td>
			</tr>
		</tbody>
	</table>
	<div id="es_cp_boxes">
		<div id="es_cp_dialog" class="window" align="center">
			<form id="_es_change_password" method="POST" action="pass.php">
				<input type="hidden" name="_es_change_password" value="1">
				<input type="hidden" name="user" value="{{$username}}" id="_es_change_password_user">
				<fieldset>
					<legend>Change Password</legend>
					<table>
						<tr>
							<td>current </td>
							<td><input type="password" name="cur" id="_es_change_password_cur" value="" size="8"></td>
						</tr>
						<tr>
							<td>new </td>
							<td><input type="password" name="new" id="_es_change_password_new" value="" size="8"></td>
						</tr>
						<tr>
							<td>new (again)</td>
							<td><input type="password" name="cnf" id="_es_change_password_cnf" value="" size="8"></td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<button type="submit" name="submit">change</button>　<button type="button" class="close">cancel</button>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
		
		{{* 下の div はモードキダイアログの背景に使うので消したらダメです *}}
		<div id="es_cp_mask"></div>
	</div>
</body>
</html>
