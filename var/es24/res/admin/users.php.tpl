<p>
	{{if $msg.type == 'error_username_exists'}}
		<span class='error'>{{$pstrres.es001}}</span><br />
	{{/if}}
	{{if $msg.type == 'error_username_short'}}
		<span class='error'>{{$pstrres.es002}}</span><br />
	{{/if}}
	{{if $msg.type == 'error_username_invalid'}}
		<span class='error'>{{$pstrres.es003}}</span><br />
	{{/if}}
	{{if $msg.type == 'error_password_short'}}
		<span class='error'>{{$pstrres.es004}}</span><br />
	{{/if}}
	{{if $msg.type == 'error_password_invalid'}}
		<span class='error'>{{$pstrres.es005}}</span><br />
	{{/if}}
	{{if $msg.type == 'success_user_regist'}}
		{{$msg.user}} {{$pstrres.es101}}
	{{/if}}
	
	{{if $msg.type == 'chdata'}}
		{{foreach item=user from=$msg.chpass_error}}
			<span class='error'>{{$user}} {{$pstrres.cd001}}</span><br />
		{{/foreach}}
		{{foreach item=user from=$msg.chpass_success}}
			{{$user}} {{$pstrres.cd002}}<br />
		{{/foreach}}
		{{foreach item=user from=$msg.chgrp}}
			{{$user}} {{$pstrres.cd003}}<br />
		{{/foreach}}
	{{/if}}
	
	{{if $msg.type == 'unregist'}}
		{{foreach item=user from=$msg.deletes}}
			{{$user}} {{$pstrres.ur001}}<br />
		{{/foreach}}
	{{/if}}
</p>
<div align="left" style="width:800">
	<form id="f1" method="POST" onsubmit="return confirm('{{$pstrres.f1001}}');">
		<input type="hidden" name="f1_submit" value="1">
		<table>
			<tr>
				<td>
					<fieldset>
						<legend>{{$pstrres.f1002}}</legend>
						<table>
							<tr>
								<td>
									{{$pstrres.f1003}}
								</td>
								<td>
									<input type="text" name="user" value="" size="8">
								</td>
							</tr>
							<tr>
								<td>
									{{$pstrres.f1004}}
								</td>
								<td>
									<input type="password" name="pass" value="" size="8" id="f1_pass">
								</td>
							</tr>
							<tr>
								<td valign="center">
									{{$pstrres.f1005}}
								</td>
								<td>
									{{foreach key=k item=i from=$groups}}
										<input id="f1_group_{{$k}}" type="checkbox" name="group[]" value="{{$k}}">
										<label for="f1_group_{{$k}}" title="{{$k}}">{{$k}}</label> {{if $i}} ({{$i}} {{$pstrres.f1006}}) {{else}} ({{$pstrres.f1007}}) {{/if}}<br>
									{{/foreach}}
								</td>
							</tr>
							<tr>
								<td colspan="2" align="center">
									<button type="submit" name="" value="">{{$pstrres.f1008}}</button>
								</td>
							</tr>
						</table>
					</fieldset>
				</td>
				<td>
					<fieldset>
						<legend>{{$pstrres.f1009}}</legend>
						<input type="text" name="p" id="f1_p" value="" size="16">
						<input type="button" onclick="PasswordGenerate();" value="{{$pstrres.f1010}}">
					</fieldset>
				</td>
			</tr>
		</table>
	</form>

</div>

	
<script>
	function f2_update() {
		if (confirm("{{$pstrres.f2001}}")) {
			document.getElementById("f2_operate").value = "update";
			document.getElementById("f2").submit();
		}
	}
	function f2_delete() {
		if (confirm("{{$pstrres.f2002}}")) {
			document.getElementById("f2_operate").value = "delete";
			document.getElementById("f2").submit();
		}
	}
</script>
<table><tr><td>
<div align="center" style="width:auto">
	<form id="f2" method="POST">
		<input type="hidden" name="f2_submit" value="1">
		<input type="hidden" id="f2_operate" name="operate" value="0">
		<fieldset style="width:auto">
			<legend>{{$pstrres.f2003}}</legend>
			<table id="main2">
				<tr id="trh">
					<th>　{{$pstrres.f2004}}　</th>
					<th>　{{$pstrres.f2005}}　</th>
					{{foreach item=i from=$group_list}}
					<th>　{{$i}}　</th>
					{{/foreach}}
					<th>　&nbsp;　</th>
					<th>　{{$pstrres.f2006}}　</th>
				</tr>
				
				{{foreach name=user_list item=i from=$user_list}}
					<tr class="tr{{if $smarty.foreach.user_list.iteration is odd}}1{{else}}2{{/if}}">
						<td>
							{{$i.user}}
							<input type="hidden" id="f2_group_update_{{$i.user}}" name="group_update[{{$i.user}}]" value="0">
						</td>
						<td>
							<input type="password" name="pass[{{$i.user}}]" value="" size="8">
						</td>
						
						{{foreach item=g from=$group_list}}
							<td align="center">
								<input type="checkbox" name="group[{{$i.user}}][]" value="{{$g}}" onchange="document.getElementById('f2_group_update_{{$i.user}}').value = 1;" {{if $g|in_array:$i.group}}checked{{/if}}>
							</td>
						{{/foreach}}
						
						<td>
							&nbsp;
						</td>
						<td align="center">
							<input type="checkbox" name="delete[{{$i.user}}]" value="{{$i.user}}">
						</td>
					</tr>
				{{/foreach}}
				
			</table>
			<br>
			
			<button type="button" name="" value="" onclick="f2_update();">{{$pstrres.f2007}}</button>　
			<button type="button" name="" value="" onclick="f2_delete();">{{$pstrres.f2008}}</button>
		</fieldset>
	</form>
</div>
</td></tr></table>
<pre style="border: 1px solid gray;">
{{$pstrres.usage|strip_tags}}
{{*
使い方
○新規登録
	文字通り新規ユーザを作成する際に使用します。
	[ユーザ名] と [パスワード] を入力し所属させるグループをチェックし (登録する) ボタンを押すとユーザが作成されます。
	ユーザ名は半角英数字４文字以上、既存ユーザと同じユーザ名は使えません。
	パスワードは半角英数字８文字以上、よく使われるパスワード (12345678 や password) は禁止される事があります。

○ユーザ一覧
	登録済みのユーザ情報を参照します。
	また各ユーザのパスワードと所属するグループの変更が行なえます。
	
	パスワードの変更は各ユーザのパスワード欄に変更後のパスワードを入力し (更新) ボタンを押します、
	空欄で (更新) ボタンを押した場合はパスワードの変更を行ないません。
	またパスワードは新規登録時と同様の使用可能チェックが入ります。
	
	グループの変更は各ユーザ行の横にあるグループ列のチェックボックスを変更することにより行えます。
	(更新) ボタンを押すと全アカウント分のグループ情報 (とパスワード) が変更対象となります、
	個々のユーザの情報は操作できません。
	自身のアカウントから admin 権限を外すとユーザ管理画面が使えなくなるのでご注意ください。


usage

*new registration
Use when making new user.
 User will be made when (register) button is pressed after the input of [user name], [password], and when the assigned group have been checked.
 User name must be in 4 or more single-byte Roman characters and alpha numerals, and unable to use existing user name.
 Password must be in 8 or more single-byte Roman characters and alpha numerals, and passwords used often like (12345678 or password) may be forbidden.

*user list
Refer to registered user information.
 Also be able to change each user's password and assigned group.
 
 To change password, input the new password to the password space and press the (update) button.
 When the (update) button is pressed while password space is empty, the password will not be changed.
Also password will have an available  check like during new registration.
 
Able to change group by changing the checkbox at the group column on the side of user row.
 When the (update) button is pressed, group information(password included) from all accounts will be updated.

Cannot control user information individually.
 If you remove the admin authority by yourself, you won't be able to use the user manage window anymore so be careful.

*}}
</pre>
<script language="javascript">
<!--
// Copyright (C) 伊織舞也 (http://www.losttechnology.jp/)
function PasswordGenerate() {
  if ((document.all)||(document.getElementById)) {
    table=pass='';
    for (lp=0x30; lp<0x3A; lp++) {
      table+=String.fromCharCode(lp);
    }
    for (lp=0x41; lp<0x5b; lp++) {
      table+=String.fromCharCode(lp);
    }
    for (lp=0x61; lp<0x7b; lp++) {
      table+=String.fromCharCode(lp);
    }
    p=document.getElementById('f1_p');
    
    ln=parseInt(Math.random()*8) + 8;
    RandMax=62;
    for (lp=0; lp<ln; lp++) {
      c=parseInt(Math.random()*RandMax);
      pass+=table.charAt(c);
    }
    p.value=pass;
    p.select(0,p.value.length);
    
    document.getElementById('f1_pass').value = pass;
  }
}
//-->
</script>
