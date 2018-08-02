//jQuery and Ajax Functions

$(document).ready(function(){
	var mainContentWidth = $(".MainContent").outerWidth();
	var screenHeight = $(window).height();
	var popupHeight = $("#tsEditPopup").outerHeight();
	$("#tsEditPopup").css("max-width", '400px');
	$("#tsEditPopup").css("margin-top", (screenHeight-popupHeight)/2);

	$(window).resize(function(){
		var mainContentWidth = $(".MainContent").outerWidth();
		var screenHeight = $(window).height();
		$("#tsEditPopup").css("max-width", '400px');
		$("#tsEditPopup").css("margin-top", (screenHeight-popupHeight)/2);
	});
});


//Javascript Functions
function editStatus(aid, roll) {
	document.getElementById("tsEditPopup").style.display = 'block';
	document.getElementById("mainContent").style.display = 'none';
	currentStatus(aid, roll);
}

function currentStatus(aid, roll){
	$.ajax({
		type:"POST",
		url:"get-current-status.php",
		data:'aid='+aid+'&roll='+roll,
		cache:false,
		success:function(response){
			if(response == 'error'){
				document.getElementById("tsep-Details").innerHTML = 'Some unknown error occured, please try again!';
			}else{
				document.getElementById("tsep-Details").innerHTML = response;
			}
		}
	});
}

function updateStatus(aid, roll, astatus){
	$.ajax({
		type:"POST",
		url:"change-current-status.php",
		data:'aid='+aid+'&roll='+roll+'&status='+astatus,
		cache:false,
		success:function(response){
			if(response == 'success'){
				currentStatus(aid, roll);
				if(astatus==0){
					updateList(roll, 'P');
				}else if(astatus==1){
					updateList(roll, 'A');
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

function hide(div) {
	document.getElementById("vsEditPopup").style.display = 'none';
	document.getElementById("mainContent").style.display = 'block';
}

//To detect escape button
document.onkeydown = function(evt) {
	evt = evt || window.event;
	if (evt.keyCode == 27){
		hide('popDiv');
	}
};