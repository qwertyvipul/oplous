$(document).ready(function(){
	var centHeight = $(".Cent").outerHeight();
	var screenHeight = $(window).height();
	$(".Cent").css("margin-top", (screenHeight-centHeight)/2);
	$(window).resize(function(){
		$(".Cent").css("margin-top", (screenHeight-centHeight)/2);
	});
});
