<?php //PAGE-49
?>
<!doctype html>
<html lang="en">
<head>
	<title>Mark Attendance</title>
	<?php require_once('meta-tags.html'); ?>
</head>
<?php //PAGE-//
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus==0) die(header('Location:login.php'));
?>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once("menu-header.php");?>
		<section class="TVS">
			<h3 class="TVS-title">About Oplous</h3>
			<div class="Oppodiv">
				<p>Oplous is one of the Thapar Hackers Club flagship project.</p>
			</div>
		</section>
		<?php $_SESSION['common-footer']=1; require_once('common-footer.php'); ?>
	</section>
</body>
</html>