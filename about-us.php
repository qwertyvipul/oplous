<!doctype html>
<html lang="en">
<head>
	<title>About Us</title>
	<?php require_once('meta-tags.html'); ?>
</head>
<?php 
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus==0) die(header('Location:index.php'));
?>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once("menu-header.php"); ?>
		<section class="TVS">
			Thapar Hackers Club is the best thing that has happened to this college.
		</section>
		<?php $_SESSION['common-footer']=1; require_once("common-footer.php"); ?>
	</section>
</body>
</html>