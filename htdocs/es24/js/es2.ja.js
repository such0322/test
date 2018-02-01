
$(function(){
	$('input.time').timepickr({
		format24 : "{h:02.d}:{m:02.d}:00", 
		resetOnBlur : false
	});
	$('input.date').jdPicker();
	$('input.date').before('<div style="float:left;">');
	$('input.date').after('</div>');
	$("input.datetime").AnyTime_picker({
		format: "%Y-%m-%d %H:%i:%s"
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
