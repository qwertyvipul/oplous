$(document).ready(function(){
	var flag=0;
	$('#notifyLoadMore').html('Loading...');
	$.ajax({
		type: "GET",
		url:"get-notifications.php",
		data:'offset='+flag,
		cache:false,
		success:function(data){
			$('#notifyLoadMore').html('Load More');
			$('#notifications').append(data);
			flag+=20;
		}
	});
	
	$('#notifyLoadMore').click(function(){
		$('#notifyLoadMore').html('Loading...');
		$.ajax({
			type: "GET",
			url:"get-notifications.php",
			data:'offset='+flag,
			cache:false,
			success:function(data){
				if(data==''){
					$('#notifyLoadMore').css('display', 'none');
				}else{
					$('#notifyLoadMore').html('Load More');
					$('#notifications').append(data);
					flag+=20;
				}
			}
		});
	});
});
