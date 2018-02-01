<div align="left" style="white-space: nowrap;">
	<div>
		<form id="f2" method="POST">
			<input type="hidden" name="f2_submit" value="1">
			<fieldset>
				<legend>ランキング再構築</legend>
				<select name="event_id">
					<option value="">取得対象イベントを選択してください</option>
					<option value="">------------------</option>
					{{foreach key=k item=i from=$events}}
						<option value="{{$k}}">{{$k}} : {{$i}}</option>
					{{/foreach}}
				</select>
				<button type="submit" name="" value="" onclick="return confirm('ランキングを再構築します、よろしいですか？')">再構築</button>
			</fieldset>
		</form>
	</div>
</div>
<div align="left" style="white-space: nowrap;">
	<div>
		<form id="f1" method="POST">
			<input type="hidden" name="f1_submit" value="1">
			<fieldset>
				<legend>イベントランキング出力</legend>
				<select name="event_id">
					<option value="">取得対象イベントを選択してください</option>
					<option value="">------------------</option>
					{{foreach key=k item=i from=$events}}
						<option value="{{$k}}">{{$k}} : {{$i}}</option>
					{{/foreach}}
				</select>
				<button type="submit" name="" value="">出力</button>
			</fieldset>
		</form>
	</div>
</div>
