<?php //PAGE-51
?>
<!doctype html>
<html lang="en">
<head>
	<title>Oplous Community</title>
	<?php require_once('meta-tags.html'); ?>
</head>
<?php //PAGE-6
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus==0) die(header('Location:index.php'));
$userId = $_SESSION['userId'];
$type = $loginStatus;
?>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once("menu-header.php"); ?>
		<section class="TVS">
			<h3 class="TVS-title">Oplous Community</h3>

		</section>
		<?php $_SESSION['common-footer']=1; require_once("common-footer.php"); ?>
	</section>
</body>
</html>