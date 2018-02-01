
<div>
	<div align="center" style="width:200;float:left">
		<form name="f1" id="f1" method="POST">
			<input type="hidden" name="f1_submit" value="1">
			<fieldset>
				<legend>検索条件</legend><br />
				
				{{if $uids_sizeover}}
					<span class="error">入力件数が多すぎます、１回につき 100 件程度を目安としてください。</span>
				{{/if}}
				
				<textarea name="uids" cols="20" rows="20">{{$uids|escape}}</textarea><br />
				<button type="submit" name="submit">変換</button>
			</fieldset>
		</form>
	</div>
	
	{{if $uids}}
		<div align="center" style="float:left">
			<form name="f2" method="POST" id="f2">
				<fieldset>
					<legend>取得結果</legend>
					<table id="main2" width="100%">
						<tbody>
							<tr id="trh">
								<th align="center">uid</th>
								<th align="center">ユーザID</th>
							</tr>
							
							{{foreach item=user_id key=uid from=$results}}
								<tr class="tr{{cycle values="1,2"}}">
									<td align="left">{{$uid}}</td>
									<td align="right">{{$user_id}}</td>
								</tr>
							{{/foreach}}
							
						</tbody>
					</table>
					
					{{if $uid_list}}
						<br>
						<div align="left" style="border: solid 1px;border-radius:5px">
							取得失敗UID<br>
							{{$nokori|implode:','}}
						</div>
					{{/if}}
					
				</fieldset>
			</form>
		</div>
	{{/if}}
	
</div>
<br style="clear:both">
