function saveData(){
	var name = 
}

function viewData(){
	$.ajax({
		type: "GET",
		url: "server.php",
		success: function(data){
			$('tbody').html(data);
		}
	});
}

function updateData(str){
	var id = str;
	var name = $('#nm-'+str).val();
	var email = $('#add-'+str).val();
	$.ajax({
		type: "POST",
		url: "server.php",
		data: "nm="+name+"&em="email+"&add"+address,
		success: function(data){
			viewData();
		}
	});

	function viewData(){
		
	}
}
