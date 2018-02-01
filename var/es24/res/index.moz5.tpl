<!doctype html>
<html lang="{{$strres.lang}}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>{{$strres.system_name}}{{$softname}}</title>
<link href="css/es.css" rel="stylesheet" type="text/css">
<link href="css/es23.css" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="images/favicon.ico">

{{if $jquery_version|version_compare:"3.0":">="}}
	<link rel="stylesheet" type="text/css" media="screen" href="js/jquery/jquery-ui-1.12.0.cupertino/jquery-ui.min.css">
	{{* .show() とか不都合があるので jq3 系は現状未対応 
	<script type="text/javascript" language="javascript" src="js/jquery/js/jquery-3.1.0.min.js"></script>
	*}}
	<script type="text/javascript" language="javascript" src="js/jquery/js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery/jquery-ui-1.12.0.cupertino/jquery-ui.min.js"></script>
{{elseif $jquery_version|version_compare:"1.11":">="}}
	<link rel="stylesheet" type="text/css" media="screen" href="js/jquery/jquery-ui-1.12.0.cupertino/jquery-ui.min.css">
	<script type="text/javascript" language="javascript" src="js/jquery/js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery/jquery-ui-1.12.0.cupertino/jquery-ui.min.js"></script>
	
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqplugin/jdpicker_1.0.3/jdpicker.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqplugin/ui-timepickr/jquery.timepickr.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="js/anytime/anytime.css" />
	<script type="text/javascript" language="javascript" src="js/jqplugin/jdpicker_1.0.3/jquery.jdpicker.js"></script>
	<script type="text/javascript" language="javascript" src="js/jqplugin/jquery.cookie.js"></script>
	<script type="text/javascript" language="javascript" src="js/jqplugin/ui-timepickr/jquery.timepickr.min.js"></script>
	<script type="text/javascript" language="javascript" src="js/anytime/anytime.js"></script>
{{else}}
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
	{{if $strres.lang == "en"}}
		<script type="text/javascript" language="javascript" src="js/es2.en.js"></script>
	{{else}}
		<script type="text/javascript" language="javascript" src="js/es2.ja.js"></script>
	{{/if}}
{{/if}}
<script type="text/javascript" language="javascript" src="js/es2.js"></script>

</head>
<body>
	<div class="es2_header gbg_header">
		<div style="float:right">server time : {{$svrdate}}</div>
		<a href="index.php">{{$strres.system_name}}</a>{{$softname}}
		<br style="clear:both;">
	</div>
	
	{{if $is_login}}
		<span style="float:right;padding:1px;max-height:0px" class="">
			{{strip}}
				<span id="es2_header2_show"><img src="images/arrow_down.gif" height="19" width="19" style="cursor:pointer"></span>
				<span id="es2_header2_hide"><img src="images/arrow_up.gif" height="19" width="19"   style="cursor:pointer"></span>
			{{/strip}}
		</span>
		
		{{* ここからヘッダ *}}
		<div class="es2_header2" style="{{if $bgimg}}background-image:url({{$bgimg}});{{/if}}">
			<div style="border-radius: 5px;background-color: rgba(255,255,255,0.8)">
				<div class="es2_menu" align="left" style="float:left;">
					{{foreach name=leftmenu item=menu from=$menuitems}}
						<a href="index.php?menukey={{$menu.menukey}}{{if $menu.firstkey}}&include_file={{$menu.menukey}}/{{$menu.firstkey}}{{/if}}"><div class="es2_menubtn {{if $menu.is_active}}gbg_btn_a{{else}}gbg_btn_b{{/if}}">{{$menu.label}}</div></a>
					{{/foreach}}
					
					<br />
					[ {{$username}} ] {{$strres.i001}}<br />
					<a href="#es_cp_dialog" name="es_change_password" onclick="$('#_es_change_password_div').toggleClass('hidden');$('#_es_change_password_cur').focus()"><div class="es2_menubtn gbg_btn_b">{{$strres.i002}}</div></a>
					
					{{* パスワード変更フォーム *}}
					<div id="_es_change_password_div" class="hidden">
						<form id="_es_change_password" method="POST" action="pass.php">
							<fieldset>
								<legend>change password</legend>
								<input type="hidden" name="_es_change_password" value="1">
								<input type="hidden" name="user" value="{{$username}}" id="_es_change_password_user">
								<table>
									<tr>
										<td style="text-align:left">current </td>
									</tr>
									<tr>
										<td style="text-align:right"><input type="password" name="cur" id="_es_change_password_cur" value="" size="8"></td>
									</tr>
									<tr>
										<td style="text-align:left">new </td>
									</tr>
									<tr>
										<td style="text-align:right"><input type="password" name="new" id="_es_change_password_new" value="" size="8"></td>
									</tr>
									<tr>
										<td style="text-align:left">new (again)</td>
									</tr>
									<tr>
										<td style="text-align:right"><input type="password" name="cnf" id="_es_change_password_cnf" value="" size="8"></td>
									</tr>
									<tr>
										<td align="center">
											<button type="submit" name="submit">change</button>
										</td>
									</tr>
								</table>
							</fieldset>
						</form>
					</div>
					
					<a href="index.php?logout=1"><div class="es2_menubtn gbg_btn_b">{{$strres.i003}}</div></a>
				</div>
				
				<div class="es2_submenus" align="left" style="float:left;">
					
					{{foreach name=topsubmenus item=submenus from=$tabitems}}
						{{foreach name=topsubmenu item=submenu from=$submenus}}
							<a href="index.php?menukey={{$submenu.menukey}}&include_file={{$submenu.menukey}}/{{$submenu.filename}}">
								<div class="es2_submenubtn {{if $submenu.is_active}}gbg_btn_a{{else}}gbg_btn_b{{/if}}" title="{{$submenu.notes}}">
									{{$submenu.title}}
								</div>
							</a>
						{{/foreach}}
					{{/foreach}}
					{{if $notes}}
						
						<br style="clear: both;">
						
						<div class="es2_submenunote">{{$notes}}</div>
					{{/if}}
				</div>
				
				<br style="clear: both;">
			</div>
		</div>
		
		{{* ここから本体 *}}
		{{if $is_plugin_error}}
			<div class="es2_mainstage">
				<div>
					{{$strres.i004}}
				</div>
			</div>
		{{elseif $mainstage}}
			<div class="es2_mainstage">
				<div>
<!--
{{$plugins|var_dump}}
-->
					{{if $plugins}}
						{{foreach item=i from=$plugins}}
							{{$i}}
						{{/foreach}}
						<hr style="clear:both">
					{{/if}}
					{{$mainstage}}
				</div>
			</div>
		{{else}}
		{{/if}}
		
	{{else}}
		<div class="es2_header2" style="{{if $bgimg}}background-image:url({{$bgimg}});{{/if}}">
			<div style="border-radius: 5px;background-color: rgba(255,255,255,0.8)">
				{{* ログインフォーム *}}
				<div align="center">
					<br />
					<div align="center" class="es2_loginform">
						<form method="post" action="index.php">
							ACCOUNT<br>
							<input type="text" name="account" class="input"><br>
							PASSWORD<br>
							<input type="password" name="password" class="input"><br>
							<br>
							<button type="submit">LOGIN</button>
						</form>
					</div>
					<br />
				</div>
			</div>
		</div>
	{{/if}}
	
	<div align="right" class="es2_footer" style="clear:both;">
		<div style="float:left;width:98%;">EitaroSoft, inc. All rights reserved.</div><div></div>
	</div>
	

</body>
</html>
