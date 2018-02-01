
{{if $result}}
	<p>
		{{if $result == 'f3ok'}}
			<span>メンテフラグを更新しました</span>
		{{elseif $result == 'f2ok'}}
			<span>バージョンを変更しました</span>
		{{elseif $result == 'f4ok'}}
			<span class="error">指定したユーザに強制ログアウトを施しました</span>
		{{/if}}
	</p>
{{/if}}

<div style="white-space:nowrap;">
	<div>
		<form id="f4" method="POST">
			<input type="hidden" name="f4_submit" value="1">
			<fieldset>
				<legend>強制排出</legend>
				
				<select name="pf_type">
					<option value="">---- 排出対象を選択 ----</option>
					<option value="all">全員</option>
					
					<option value="100">Android</option>
					<option value="200">iOS</option>
					<option value="400">その他</option>
				</select>
				
				<button type="submit" name="submit" onclick="return confirm('指定した区分のユーザを強制的にログアウトさせます、よろしいですか？');">強制排出</button>
			</fieldset>
		</form>
	</div>
	
	<hr>
	
	<div align="left" style="float:left;">
		<form name="f2" method="POST">
			<input type="hidden" name="f2a_submit" value="1">
			<fieldset>
				<legend>Android バージョン設定 (現在のバージョン：{{$version_android.version}}) </legend>
				<table>
					<tr>
						<td>バージョン</td>
						<td>
							<input type="text" name="version" value="{{$version_android.version}}" size="8">
						</td>
					</tr>
					<tr>
						<td>バージョン説明</td>
						<td>
							<textarea name="versioninfo" cols="40" rows="4">{{$version_android.versioninfo|replace:"|":"\n"}}</textarea>
						</td>
					</tr>
{{*
					<tr>
						<td>自社誘導</td>
						<td>
							<select name="guide_enable">
								<option value="0" {{if $version_android.guide_enable == 0}}selected{{/if}}>表示しない</option>
								<option value="1" {{if $version_android.guide_enable == 1}}selected{{/if}}>表示する</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>起動用URL</td>
						<td>
							<input type="text" name="guide_url" value="{{$version_android.guide_url}}" size="48">
						</td>
					</tr>
					<tr>
						<td>バナー画像のパス</td>
						<td>
							<input type="text" name="guide_img" value="{{$version_android.guide_img}}" size="32">
						</td>
					</tr>
					<tr>
						<td>一言文言</td>
						<td>
							<input type="text" name="guide_comment" value="{{$version_android.guide_comment}}" size="48">
						</td>
					</tr>
*}}
					<tr>
						<td>appver : svrver</td>
						<td>
							<textarea name="svrvers" rows="4" cols="20">{{if $version_android.svrvers}}{{foreach key=k item=i from=$version_android.svrvers}}{{$k}}	{{$i}}
{{/foreach}}{{/if}}</textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center">
							<button type="submit" onclick="return confirm('バージョンを更新します、よろしいですか？');">更新</button>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
	
	<div align="left" style="float:left;">
		<form name="f2" method="POST">
			<input type="hidden" name="f2i_submit" value="1">
			<fieldset>
				<legend>iOS バージョン設定  (現在のバージョン：{{$version_ios.version}})</legend>
				<table>
					<tr>
						<td>バージョン</td>
						<td>
							<input type="text" name="version" value="{{$version_ios.version}}" size="8">
						</td>
					</tr>
					<tr>
						<td>バージョン説明</td>
						<td>
							<textarea name="versioninfo" cols="40" rows="4">{{$version_ios.versioninfo|replace:"|":"\n"}}</textarea>
						</td>
					</tr>
{{*
					<tr>
						<td>自社誘導</td>
						<td>
							<select name="guide_enable">
								<option value="0" {{if $version_ios.guide_enable == 0}}selected{{/if}}>表示しない</option>
								<option value="1" {{if $version_ios.guide_enable == 1}}selected{{/if}}>表示する</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>起動用URL</td>
						<td>
							<input type="text" name="guide_url" value="{{$version_ios.guide_url}}" size="48">
						</td>
					</tr>
					<tr>
						<td>バナー画像のパス</td>
						<td>
							<input type="text" name="guide_img" value="{{$version_ios.guide_img}}" size="32">
						</td>
					</tr>
					<tr>
						<td>一言文言</td>
						<td>
							<input type="text" name="guide_comment" value="{{$version_ios.guide_comment}}" size="48">
						</td>
					</tr>
*}}
					<tr>
						<td>appver : svrver</td>
						<td>
							<textarea name="svrvers" rows="4" cols="20">{{if $version_ios.svrvers}}{{foreach key=k item=i from=$version_ios.svrvers}}{{$k}}	{{$i}}
{{/foreach}}{{/if}}</textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center">
							<button type="submit" onclick="return confirm('バージョンを更新します、よろしいですか？');">更新</button>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
	
	<br style="clear:both;">
	<hr>
	
	
	<div align="left" style="clear:both;">
		
		<form name="f3v" id="f3v" method="POST">
			<input type="hidden" name="f3v_submit" value="1">
			<input type="hidden" name="edit_version" id="f3v_edit_version" value="">
		</form>
		
		<form name="f3" method="POST">
			<input type="hidden" name="f3_submit" value="1">
			<fieldset>
				<legend>
					メンテお知らせ
					
					<select name="edit_version" id="f3_edit_version">
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
								<label><input type="checkbox" name="update_vers[]" value="" {{if !$edit_version}}checked{{/if}}>バージョン指定なし</label><br>
								{{foreach from=$versions item=i key=k}}
									<label><input type="checkbox" name="update_vers[]" value="{{$k}}" {{if $edit_version==$k}}checked{{/if}}>{{$k}} : {{$i}}</label><br>
								{{/foreach}}
							</td>
						</tr>
						<tr>
							<td>
								メンテフラグ
							</td>
							<td>
								<select name="mainte_flg" id="f3_mainte_flg">
									<option value="1" {{if $mainte_flg == "1"}}selected{{/if}}>メンテナンス状態に変更</option>
									<option value="0" {{if $mainte_flg == "0"}}selected{{/if}}>サービス状態に変更</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								完全遮断フラグ
							</td>
							<td>
								<select name="is_close" id="f3_is_close">
									<option value="0" {{if $is_close == "0"}}selected{{/if}}>アクセス可能状態に変更</option>
									<option value="1" {{if $is_close == "1"}}selected{{/if}}>常にセッション切れ状態に変更</option>
								</select>
<input type="hidden" name="mainte_label" valeu="">
<input type="hidden" name="mainte_info" valeu="">
							</td>
						</tr>
{{*
						<tr>
							<td>
								メンテラベル
							</td>
							<td>
								<input type="text" name="mainte_label" id="f3_mainte_label" size="40" value="{{$mainte_label|escape}}">
							</td>
						</tr>
						<tr>
							<td>
								メンテお知らせ
							</td>
							<td>
								<textarea name="mainte_info" id="f3_mainte_info" cols="44" rows="4">{{$mainte_info|replace:"|":"\n"}}</textarea>
							</td>
						</tr>
*}}
						<tr>
							<td>
								メンテ無視アカウント
							</td>
							<td>
								<textarea name="pass_accounts" cols="40" rows="15">{{$pass_accounts}}</textarea><br>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="right">
								<button type="submit" name="submit" onclick="return confirm('メンテ状態を変更します、よろしいですか？');">更新</button>
							</td>
						</tr>
					</table>
				</div>
				
			</fieldset>
		</form>
	</div>
	<div style="clear:both;"></div>
</div>


<script>
<!--

$(function(){
	
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
	
	// メンテお知らせのバージョン変更の再読み込み
	$('#f3_edit_version').change(function(){
		$('#f3v_edit_version').val($(this).val());
		$('#f3v').submit();
	});

});

//-->
</script>
