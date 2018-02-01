
<div>
	<form id="f1" method="post">
		<input type="hidden" name="f1_submit" value="1">
		<fieldset>
			<legend>検索条件</legend>
			<table>
				{{*<!--<tr>
					<td>ミッションID</td>
					<td>
						<select>
							
						</select>
					</td>
				</tr>-->*}}
				{{*<!--<tr>
					<td>達成期間</td>
					<td>
						
					</td>
				</tr>-->*}}
				<tr>
					<td>クリア時レベル</td>
					<td>
						<input type="text" name="level[min]" value="{{$defaults.level.min}}" size="4">
						　&lt;=　x　&lt;=　
						<input type="text" name="level[max]" value="{{$defaults.level.max}}" size="4">
					</td>
				</tr>
				{{*<!--<tr>
					<td>会員登録日時</td>
					<td>
						
					</td>
				</tr>-->*}}
				<tr>
					<td colspan="2" style="text-align:center">
						<button type="submit" name="submit">送信</button>
					<td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>

{{if $f1_search}}
	<div>
		<form id="f2" method="post">
			<fieldset>
				<legend>集計結果</legend>
				
				{{if $missions}}
					<table id="f2_result_table" class="main2">
						<thead>
							<tr class="trh">
								<th>
									ミッション
								</th>
								<th>
									達成数
								</th>
								<th>
									表
								</th>
							</tr>
						</thead>
						<tbody>
							{{foreach item=i from=$missions}}
								<tr class="tr{{cycle values="1,2"}}">
									<td>
										{{$i.mission_id}} : {{$mission_names[$i.mission_id]}}
									</td>
									<td style="text-align:right">
										{{$i.cnt|number_format}}
									</td>
									<td style="text-align:left">
										<img src="images/g/a.gif" height="8" width="{{$i.width*400|intval}}">
									</td>
								</tr>
							{{/foreach}}
						</tbody>
					</table>
				{{else}}
					該当するログが有りません。
				{{/if}}
				
				
			</fieldset>
		</form>
	</div>
{{/if}}
