<?php //PAGE-21
require_once('php/checkRequire.php');
verifyRequire('teacher');
?>
<!doctype html>
<html lang="en">
<head>
	<title>Teacher</title>
	<?php require_once('meta-tags.html'); ?>
	
</head>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once("menu-header.php");?>
		<section class="TcHomeContent">
			<img src="assets/images/handsup.jpg" style="width:80%;"/>
			<div class="UpcomingFeatures">
				<h2 class="UFs-title">Upcoming Features</h2>
				<div class="UFeature">
					<h3 class="UF-title">Extra Class</h3>
					<p class="UF-description">
						You will be able to schedule any extra class directly from the app without having to worry about the vacant slots and available classrooms.
					</p>
				</div>
				<div class="UFeature">
					<h3 class="UF-title">Scheduler</h3>
					<p class="UF-description">
						You will be able to track your schedule upto one week and be updated in real time about all your extra classes, meetings, lectures and many more.
					</p>
				</div>
			</div>
		</section>
		<?php $_SESSION['common-footer']=1; require_once('common-footer.php'); ?>
	</section>
</body>
</html>