
{{if $result}}
	<p>
		{{if $result == 'f2ok'}}
			<span>global_bonus を更新しました</span>
		{{elseif $result == 'error'}}
			<span class="error">問題が発生しました</span>
		{{/if}}
	</p>
{{/if}}

<div align="left" style="clear:both;">
	<form name="f1" id="f1" method="POST">
		<input type="hidden" name="f1_submit" value="1">
		<input type="hidden" name="edit_version" id="f1_edit_version" value="">
	</form>
	
	
	<div align="left" style="clear:both;">
		
		<form name="f2" method="POST">
			<input type="hidden" name="f2_submit" value="1">
			<input type="hidden" name="edit_version" value="{{$edit_version}}">
			<fieldset>
				<legend>
					報酬倍率
					<select name="edit_version" id="f2_edit_version">
						<option value="">バージョン指定なし</option>
						{{foreach from=$versions item=i key=k}}
							<option value="{{$k}}" {{if $edit_version==$k}}selected{{/if}}>{{$k}} : {{$i}}</option>
						{{/foreach}}
					</select>
				</legend>
				
				<div style="float:left;margin:1px 5px;">
					<table>
						<tr>
							<td>
								更新対象
							</td>
							<td>
								{{foreach from=$versions item=i key=k}}
									<label><input type="checkbox" name="update_vers[]" value="{{$k}}" {{if $edit_version==$k}}checked{{/if}}>{{$k}} : {{$i}}</label><br>
								{{/foreach}}
							</td>
						</tr>
						
						{{foreach key=k item=i from=$global_bonus_labels}}
							<tr>
								<td>
									{{$i}}
								</td>
								<td>
									<input type="text" name="{{$k}}" value="{{$global_bonus[$k]|default:'100'}}" size="4">
								</td>
							</tr>
						{{/foreach}}
						
						<tr>
							<td colspan="2" align="center">
								<button type="submit" name="submit" onclick="return confirm('メンテ状態を変更します、よろしいですか？');">更新</button>
							</td>
						</tr>
					</table>
				</div>
				
			</fieldset>
		</form>
	</div>
</div>

<script>
<!--
$(function(){
	// バージョン変更の再読み込み
	$('#f2_edit_version').change(function(){
		$('#f1_edit_version').val($(this).val());
		$('#f1').submit();
	});
});
//-->
</script>
