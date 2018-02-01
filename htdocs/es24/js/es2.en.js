
$(function(){
	$('input.time').timepickr({
		format24 : "{h:02.d}:{m:02.d}:00", 
		resetOnBlur : false
	});
	$('input.date').jdPicker();
	$('input.date').before('<div style="float:left;">');
	$('input.date').after('</div>');
	$("input.datetime").AnyTime_picker({
		format: "%Y-%m-%d %H:%i:00"
	});
	$('input.datetime').after($('<span class="datetime_clear">×</span>').click(function(){$(this).prev().val("");}));
	
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
					alert( "OK" );
				}
				else {
					alert( "NG" );
				}
			},
			error: function(msg){
				alert( "ERROR : " + msg );
			}
		});
		
		return false;
	});
});
