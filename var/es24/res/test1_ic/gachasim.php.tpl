<script>
<!--
leftgachatest = 0;

function gachatest(){
	var url = 'inline.php';
	var params = {
		"menukey": "{{$menukey}}", 
		"include_file": "plugin/{{$menukey}}/gachasim.php", 
		"ajax" : "gacha", 
		"gacha_id" : parseInt($("#f2_gacha_id").val())
	};
	$.get(url, params, function(data){
		if (--leftgachatest > 0) {
			window.setTimeout(gachatest, 0.1);
		} else {
			gachatestresult();
		}
		$("#f2_testrun_count").val(leftgachatest);
	});
}

function gachatestresult(){
	var gacha_id = parseInt($("#f2_gacha_id").val());
	if (gacha_id > 0) {
		var url = 'inline.php';
		var params = {
			"menukey": "{{$menukey}}", 
			"include_file": "plugin/{{$menukey}}/gachasim.php", 
			"ajax" : "drop", 
			"gacha_id" : gacha_id
		};
		$.get(url, params, function(data){
			data.forEach(function(a){
				$("#f2_gachadrop_" + a.gacha_drop_id + "_cnt").text(a.cnt);
				$("#f2_gachadrop_" + a.gacha_drop_id + "_rate").text(a.rate);
			});
		});
	}
}


$(function(){
	$("#f2_testrun_btn").click(function(){
		var cnt = parseInt($("#f2_testrun_count").val());
		leftgachatest = cnt;
		window.setTimeout(gachatest, 0.1);
	});
	gachatestresult();
});

//-->
</script>
<style>
tr.tr1 > td {
	text-align:right;
}
tr.tr2 > td {
	text-align:right;
}
</style>

<div>
	<form method="post">
		<fieldset>
			<legend>ガチャ台選択</legend>
			<input type="hidden" name="f1_submit" value="gacha_id">
			<select name="gacha_id">
				<option value=""></option>
				{{foreach item=i from=$gacha_ides}}
					<option value="{{$i}}" {{if $i==$gacha_id}}selected="selected"{{/if}}>{{$i}}</option>
				{{/foreach}}
			</select>
			<button type="submit">選択</button>
		</fieldset>
	</form>
	
	{{if $gacha_id > 0}}
		<div align="left">
			
			<table>
				<tr>
					<td>
						テスト実行
					</td>
					<td>
						<form method="post">
							<input type="hidden" name="f2_testrun" value="1">
							<input type="hidden" id="f2_gacha_id" name="gacha_id" value="{{$gacha_id}}">
							100 * <input type="text" id="f2_testrun_count" name="" value="1" size="4">
							<button type="button" id="f2_testrun_btn">実行</button>
						</form>
					</td>
				</tr>
				<tr>
					<td>
						ログリセット
					</td>
					<td>
						<form method="post">
							<input type="hidden" name="f2_reset" value="1">
							<input type="hidden" name="gacha_id" value="{{$gacha_id}}">
							<button type="submit">リセット</button>
						</form>
					</td>
				</tr>
				<tr>
					<td>
						抽選状況
					</td>
					<td>
						
						<table class="main2">
							<thead>
								<tr class="trh">
									<td>
										id
									</td>
									<td>
										card_id
									</td>
									<td>
										rate
									</td>
									<td>
										rank
									</td>
									<td>&nbsp;</td>
									<td>
										抽選回数
									</td>
									<td>
										割合
									</td>
								</tr>
							</thead>
							<tbody>
								{{foreach item=i from=$gacha_drop_master}}
									{{if $i.gacha_id == $gacha_id}}
										<tr class="tr{{cycle values="1,2"}}">
											<td>
												{{$i.id}}
											</td>
											<td>
												{{$i.card_id}}
											</td>
											<td>
												{{$i.rate}}
											</td>
											<td>
												{{$i.rank}}
											</td>
											<td>&nbsp;</td>
											<td>
												<span id="f2_gachadrop_{{$i.id}}_cnt">
													
												</span>
											</td>
											<td>
												<span id="f2_gachadrop_{{$i.id}}_rate">
													
												</span>
											</td>
										</tr>
									{{/if}}
								{{/foreach}}
							</tbody>
						</table>
						
					</td>
				</tr>
			</table>
			
		</div>
	{{/if}}
</div>
