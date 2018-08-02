$(document).ready(function(){
	$("#markAllCheck").change(function(){
		var request = $(this).find(":selected").text();
		if(request == "Present"){
			$("input[type=radio][value='1']").prop("checked", true);
		}else if(request == "Absent"){
			$("input[type=radio][value='0']").prop("checked", true);
		}
	});
});