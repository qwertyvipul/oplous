$(document).ready(function(){
	
	$('#supportTitle').keyup(function(){
		if(this.value.length > 60){
			$("#remainSubject").html("Maximum limit exceeded!");
			$("#remainSubject").css("color", "red");
		}else{
			$("#remainSubject").html((60 - this.value.length)+" characters remaining.");
			$("#remainSubject").css("color", "black");
		}
	});
	
	$('#supportInfo').keyup(function(){
		if(this.value.length > 255){
			$("#remainDescription").html("Maximum limit exceeded!");
			$("#remainDescription").css("color", "red");
		}else{
			$("#remainDescription").html((255 - this.value.length)+" characters remaining.");
			$("#remainDescription").css("color", "black");
		}
	});
})



