<?php
require_once('php/checkRequire.php');
verifyRequire('menu-header');
$userId = $_SESSION['userId'];
$type = $_SESSION['\''.$userId.'\''];
echo('
	<header class="HomeHead">
	<div class="CommonHead">
		<a href="info.php"><div class="AppName"><span>OPLOUS</span></div></a>
		<div style="clear:both;"></div>
	</div>
	<ul class="HeadMenu">
');
echo('
		<a href="index.php"><button class="MenuItem"><span>Home</span></button></a>
		<a href="profile.php"><button class="MenuItem"><span>Profile</span></button></a>
		<a href="attendance.php"><button class="MenuItem"><span>Attendance</span></button></a>
		<a href="notifications.php"><button class="MenuItem" id="menu-notification"><span>Notifications</span></button></a>
		<a href="community.php"><button class="MenuItem"><span>Community</span></button></a>
		<a href="support.php"><button class="MenuItem"><span>Support</span></button></a>
		<!--<a href="about-us.php"><button class="MenuItem"><span>About Us</span></button></a>-->
		<a href="logout.php"><button class="MenuItem"><span>Logout</span></button></a>
		<div style="clear:both;"></div>
	</ul>
	</header><hr/>
');
?>
<script src="js/jquery-3.2.1.min.js"></script>
<script>
	$(document).ready(function(){
		getCount(<?php echo($userId.', '.$type); ?>);
		refresh();
	});
	
	function refresh(){
		setTimeout(function(){
			getCount(<?php echo($userId.', '.$type); ?>);
			refresh();
		}, 3000);
	}
	
	function getCount(){
		$.ajax({
			type:"POST",
			url:"get-notification-count.php",
			cache:false,
			success:function(response){
				document.getElementById("menu-notification").innerHTML = response;
			}
		});
	}
</script>