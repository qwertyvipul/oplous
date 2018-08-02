//Javascript Functions
function changeStatus(aid, roll, astatus) {
	$.ajax({
		type:"GET",
		//a new different script
		url:"change-current-status.php",
		data:'aid='+aid+'&roll='+roll+'&status='+astatus,
		cache:false,
		success:function(response){
			if(response == 'success'){
				newDetails(aid, roll);
				newStatus(aid, roll);
			}else{
				alert('Some unknown error occured, please try again!');
			}
		}
	});
}

function newDetails(aid, roll){
	$.ajax({
		type:"POST",
		url:"student-new-details.php?flow=1",
		data:'aid='+aid+'&roll='+roll,
		cache:false,
		success:function(response){
			document.getElementById('ts-calculations').innerHTML = response;
		}
	});
}

function newStatus(aid, roll){
	$.ajax({
		type:"POST",
		url:"student-new-details.php?flow=2",
		data:'aid='+aid+'&roll='+roll,
		cache:false,
		success:function(response){
			if(response=='A'){
				document.getElementById(roll+"-"+aid+"-button").innerHTML = "<button class=\"ID-item-button\" onclick=\"changeStatus("+aid+", "+roll+", 0)\">Change</button>";
			}else if(response=='P'){
				document.getElementById(roll+"-"+aid+"-button").innerHTML = "<button class=\"ID-item-button\" onclick=\"changeStatus("+aid+", "+roll+", 1)\">Change</button>";
			}else{
				document.getElementById(roll+"-"+aid+"-button").innerHTML = "<button>Change</button>";
			}
			document.getElementById(roll+"-"+aid+"-status").innerHTML = response;
		}
	});
}