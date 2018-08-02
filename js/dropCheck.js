$(document).ready(function(){
	
	//Validate account type in Login Form
	$("#loginForm").submit(function(e){
		var accountType = $("select#aTDrop").val();
		if(accountType=="0"){
			e.preventDefault();
			alert("Please select your account type.");
		}
	});
	
	//Validate singup credentials
	$("#signupForm").submit(function(e){
		var accountType = $("select#aTDrop").val();
		if(accountType=="0"){
			e.preventDefault();
			alert("Please select your account type.");
		}
	});
	
	//Validate account type in Find Form
	$("#findForm").submit(function(e){
		var accountType = $("select#aTDrop").val();
		if(accountType=="0"){
			e.preventDefault();
			alert("Please select your account type.");
		}
	});
	
	

	//Validate mark credentials
	$("#mf_year").change(function(){
		$(".Child1").css("display", "none");
		$(".Child2").css("display", "none");
		$("#mf_batch").val("0");
		$("#mf_class").val("0");
		$("#mf_batch").prop("disabled", true);
		$("#mf_class").prop("disabled", true);
		$("#mf-button").prop("disabled", true);
		var mf_year = $(this).val();
		if(mf_year != "0"){
			$("#mf_batch").prop("disabled", false);
			var id = $(this).children(":selected").attr("id");
			$("."+id).css("display", "block");
			
		}else{
			$("#mf_batch").prop("disabled", true);
		}
	});

	$("#mf_batch").change(function(){
		$(".Child2").css("display", "none");
		$("#mf_class").val("0");
		$("#mf_class").prop("disabled", true);
		$("#mf-button").prop("disabled", true);
		var mf_year = $(this).val();
		if(mf_year != "0"){
			$("#mf_class").prop("disabled", false);
			var id = $(this).children(":selected").attr("id");
			$("."+id).css("display", "block");
		}else{
			$("#mf_class").prop("disabled", true);
		}
	});

	$("#mf_class").change(function(){
		var mf_year = $(this).val();
		if(mf_year != "0"){
			$("#mf-button").prop("disabled", false);
		}else{
			$("#mf-button").prop("disabled", true);
		}
	});
	
	//Validate class view credentials
	$("#cv_year").change(function(){
		$(".Child3").css("display", "none");
		$(".Child4").css("display", "none");
		$("#cv_batch").val("0");
		$("#cv_class").val("0");
		$("#cv_batch").prop("disabled", true);
		$("#cv_class").prop("disabled", true);
		$("#classViewButton").prop("disabled", true);
		var cv_year = $(this).val();
		if(cv_year != "0"){
			$("#cv_batch").prop("disabled", false);
			var id = $(this).children(":selected").attr("id");
			$("."+id).css("display", "block");
			
		}else{
			$("#cv_batch").prop("disabled", true);
		}
	});

	$("#cv_batch").change(function(){
		$(".Child4").css("display", "none");
		$("#cv_class").val("0");
		$("#cv_class").prop("disabled", true);
		$("#classViewButton").prop("disabled", true);
		var cv_year = $(this).val();
		if(cv_year != "0"){
			$("#cv_class").prop("disabled", false);
			var id = $(this).children(":selected").attr("id");
			$("."+id).css("display", "block");
		}else{
			$("#cv_class").prop("disabled", true);
		}
	});

	$("#cv_class").change(function(){
		var cv_year = $(this).val();
		if(cv_year != "0"){
			$("#classViewButton").prop("disabled", false);
		}else{
			$("#classViewButton").prop("disabled", true);
		}
	});
	
	//Validate total view credentials
	$("#tv_year").change(function(){
		$(".Child5").css("display", "none");
		$(".Child6").css("display", "none");
		$("#tv_batch").val("0");
		$("#tv_subject").val("0");
		$("#tv_batch").prop("disabled", true);
		$("#tv_subject").prop("disabled", true);
		$("#totalViewButton").prop("disabled", true);
		var tv_year = $(this).val();
		if(tv_year != "0"){
			$("#tv_batch").prop("disabled", false);
			var id = $(this).children(":selected").attr("id");
			$("."+id).css("display", "block");
			
		}else{
			$("#tv_batch").prop("disabled", true);
		}
	});

	$("#tv_batch").change(function(){
		$(".Child6").css("display", "none");
		$("#tv_subject").val("0");
		$("#tv_subject").prop("disabled", true);
		$("#totalViewButton").prop("disabled", true);
		var tv_year = $(this).val();
		if(tv_year != "0"){
			$("#tv_subject").prop("disabled", false);
			var id = $(this).children(":selected").attr("id");
			$("."+id).css("display", "block");
		}else{
			$("#tv_subject").prop("disabled", true);
		}
	});

	$("#tv_subject").change(function(){
		var cv_year = $(this).val();
		if(cv_year != "0"){
			$("#totalViewButton").prop("disabled", false);
		}else{
			$("#totalViewButton").prop("disabled", true);
		}
	});
	
	
	//Validate custom notifications credentials
	$("#notify_year").change(function(){
		$("#notifyButton").prop("disabled", true);
		var tv_year = $(this).val();
		if(tv_year != "0"){
			$("#notifyButton").prop("disabled", false);
		}
	});

});