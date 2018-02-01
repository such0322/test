<!-- 文字コードの自動判別につかう文字列 -->
<script type='text/javascript'>
<!--
$(function(){
	
	var $f = $('#fn_backup_from_player_id');
	$f.change(function(){
		var $t = $('#fn_backup_to_player_id');
		if (! $t.val()) {$t.val($f.val());}
	});
});
//-->
</script>
<div>
	<div style="display:inline-block;vertical-align:top">
		<form id="fn_backup" method="POST">
			<input type="hidden" name="fn_submit" value="backup">
			<fieldset>
				<legend>backup</legend>
				<table>
					<tr>
						<td>
							player_id
						</td>
						<td>
							<input type="text" id="fn_backup_from_player_id" name="from_player_id" value="" size="8">
						</td>
					</tr>
					<tr>
						<td>
							to_player_id
						</td>
						<td>
							<input type="text" id="fn_backup_to_player_id" name="to_player_id" value="" size="8">
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center">
							<button type="submit">bakcup</button>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
	
	<div style="display:inline-block;vertical-align:top">
		<form id="fn_restore" enctype="multipart/form-data" method="POST">
			<input type="hidden" name="fn_submit" value="restore">
			<fieldset>
				<legend>restore</legend>
				
				<input type="file" name="dumpfile">
				
				<button type="submit">restore</button>
				
			</fieldset>
		</form>
	</div>
	
</div>
