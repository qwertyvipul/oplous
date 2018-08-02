<!doctype html>
<html lang="en">
<head>
	<title>Notifications</title>
	<?php require_once('meta-tags.html'); ?>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/notify-load-more.js"></script>
<style>
.NReadMore{text-align:left, padding:10px;}
.Npre{background:#faff5e; }
</style>
</head>
<?php //PAGE-11
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus==0) die(header('Location:login.php'));
?>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once('menu-header.php');?>
		<section class="NS TVS">
			<h3 class="TVS-title">Notifications</h3><hr/>
			<div id="notifications"></div>
		</section>
	<button class="NextLink" id="notifyLoadMore">Load More</button>
	<?php $_SESSION['common-footer']=1; require_once('common-footer.php'); ?>
	</section>
</body>
</html>