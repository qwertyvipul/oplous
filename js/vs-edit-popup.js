//jQuery and Ajax Functions
$(document).ready(function(){
	alignCenter();
	$(window).resize(function(){
		alignCenter();
	});
});

function alignCenter(){
	var screenHeight = $(window).height();
	var popupHeight = $("#vs-popup").outerHeight();
	$("#vs-popup").css("max-width", '400px');
	$("#vs-popup").css("margin-top", (screenHeight-popupHeight)/2);
}


//Javascript Functions
function editStatus(aid, roll) {
	document.getElementById("vs-popup").style.display = 'block';
	document.getElementById("mainContent").style.display = 'none';
	currentStatus(aid, roll);
}

function currentStatus(aid, roll){
	$.ajax({
		type:"GET",
		url:"get-current-status.php",
		data:'aid='+aid+'&roll='+roll,
		cache:false,
		success:function(response){
			if(response == 'error'){
				document.getElementById("vsp-details").innerHTML = 'Some unknown error occured, please try again!';
			}else{
				document.getElementById("vsp-details").innerHTML = response;
			}
			alignCenter();
		}
	});
}

function updateStatus(aid, roll, astatus){
	$.ajax({
		type:"GET",
		url:"change-current-status.php",
		data:'aid='+aid+'&roll='+roll+'&status='+astatus,
		cache:false,
		success:function(response){
			if(response == 'success'){
				currentStatus(aid, roll);
				if(astatus==0){
					updateList(roll, 'Present');
				}else if(astatus==1){
					updateList(roll, 'Absent');
				}
				//alert('Successfully Updated!');
			}else{
				alert(response);
			}
		}
	});
}

function updateList(roll, newStatus){
	document.getElementById(roll+"-status").innerHTML = newStatus;
}

function hide() {
	document.getElementById("vs-popup").style.display = 'none';
	document.getElementById("mainContent").style.display = 'block';
}

//To detect escape button
document.onkeydown = function(evt) {
	evt = evt || window.event;
	if (evt.keyCode == 27){
		hide();
	}
};