
$(function(){
	
	$('.es2_submenubtn').bind("mouseenter", function(){
		$(this).addClass('es2_submenus_active');
	});
	$('.es2_submenubtn').bind("mouseleave", function(){
		$(this).removeClass('es2_submenus_active');
	});
	$('.es2_menubtn').bind("mouseenter", function(){
		$(this).addClass('es2_submenus_active');
	});
	$('.es2_menubtn').bind("mouseleave", function(){
		$(this).removeClass('es2_submenus_active');
	});
	
	$('a[name=es_change_password]').click(function(e) {
		
		//Cancel the link behavior
		e.preventDefault();
		//Get the A tag
		var id = $(this).attr('href');
		
		//Get the screen height and width
		var maskHeight = $(document).height();
		var maskWidth = $(window).width();
		
		//Set height and width to mask to fill up the whole screen
		$('#es_cp_mask').css({'top':0,'left':0,'width':maskWidth,'height':maskHeight,'filter':'Alpha(opacity=30)','-moz-opacity':'0.3','opacity':'0.3'});
		//transition effect
		$('#es_cp_mask').fadeIn(200);
		$('#es_cp_mask').fadeTo(200,0.3);
		
		//Get the window height and width
		var winH = $(window).height();
		var winW = $(window).width();
		
		//Set the popup window to center
		$(id).css('top',  winH/2-$(id).height()/2);
		$(id).css('left', winW/2-$(id).width()/2);
		
		//transition effect
		$(id).fadeIn(200);
		
		$("select").hide();
	});
	
	$('.window .close').click(function (e) {
		//Cancel the link behavior
		e.preventDefault();
		$('#es_cp_mask, .window').hide();
		
		$("#_es_change_password_cur").attr("value", "");
		$("#_es_change_password_new").attr("value", "");
		$("#_es_change_password_cnf").attr("value", "");
		
		$("select").show();
	});
	
	$('#es_cp_mask').click(function () {
		$(this).hide();
		$('.window').hide();
		
		$("#_es_change_password_cur").attr("value", "");
		$("#_es_change_password_new").attr("value", "");
		$("#_es_change_password_cnf").attr("value", "");
		
		$("select").show();
	});
});
