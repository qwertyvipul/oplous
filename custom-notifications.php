<!doctype html>
<html lang="en">
<head>
	<title>Send Custom Notifications</title>
	<?php require_once('meta-tags.html'); ?>
	
<style>
.CND{text-align:left; }
</style>
</head>
<?php //PAGE-56
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=1) die(header('Location:login.php'));

//required variables
if(!isset($_GET['year']) or !is_numeric($_GET['year']) or !isset($_GET['notify']) or $_GET['notify']!='notify') die('<b>Error 56_101:</b> Page Breakdown!');
$year = $_GET['year'];

//list all the different batche teacher teach in the year

?>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once("menu-header.php");?>
		<section class="TVS">
			<h3 class="TVS-title">Custom Notifications</h3>
			<div class="CND">
				<p>Select all the batches you want to notify</p>
				<form>
					<p class="Cred-box"><input type="checkbox" name="memory" value="1"> Remember Me</input></p>
					<p class="Cred-box"><input type="checkbox" name="memory" value="1"> Remember Me</input></p>
					<p class="Cred-box"><input type="checkbox" name="memory" value="1"> Remember Me</input></p>
					<p class="Cred-box"><input type="checkbox" name="memory" value="1"> Remember Me</input></p>
					<div class="SS-ic"><h4>Subject : </h4><pre id="remainSubject">60 characters remaining.</pre><input class="SS-input" type="text" id="supportTitle" name="supportTitle" value="" style="width:100%; " placeholder="Subject" required/></div>
					<div class="SS-ic"><h4>Description : </h4><pre id="remainDescription">255 characters remaining.</pre><textarea class="SS-input" type="text" id="supportInfo" name="supportInfo" value="" style="width:100%; " placeholder="Description" required/></textarea></div>
				</form>
			</div>
		</section>
		<?php $_SESSION['common-footer']=1; require_once('common-footer.php'); ?>
	</section>
</body>
</html>