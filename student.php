<?php //PAGE-16
//verify require
require_once('php/checkRequire.php');
verifyRequire('student');
?>
<!doctype html>
<html lang="en">
<head>
	<title>Student</title>
	<?php require_once('meta-tags.html'); ?>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/aTCheck.js"></script>
</head>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once("menu-header.php");?>
		<section class="StHomeContent">
			<img src="assets/images/handsup.jpg" style="width:80%;"/>
			<div class="UpcomingFeatures">
				<h2 class="UFs-title">Upcoming Features</h2>
				<!--<div class="UFeature">
					<h3 class="UF-title">Mass Bunk</h3>
					<p class="UF-description">
						Often planning mass bunks sitting in your room is a strategic task. In our next update you will get the feature to plan and vote for mass bunks and inform teachers without actually contacting.
					</p>
				</div>-->
				<div class="UFeature">
					<h3 class="UF-title">Scheduler</h3>
					<p class="UF-description">
						You will be able to track your schedule upto one week and be updated in real time about all your extra classes, lectures, quizzes and many more.
					</p>
				</div>
				<div class="UFeature">
					<h3 class="UF-title">Real Time Chat</h3>
					<p class="UF-description">
						You will be able to chat directly with your teachers in real time and get your doubts and concerns cleared. Informations sharing will become easier than ever.
					</p>
				</div>
			</div>
		</section>
		<?php $_SESSION['common-footer']=1; require_once('common-footer.php'); ?>
	</section>
</body>
</html>