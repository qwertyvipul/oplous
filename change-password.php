<!doctype html>
<html lang="en">
<head>
	<title>Change Password</title>
	<?php require_once('meta-tags.html'); ?>
	<style>
	.Cent{margin-top:20%; }
	</style>
</head>
<?php //PAGE-46
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus==0) die(header('Location:login.php'));

if(isset($_SESSION['HM']) and $_SESSION['HM']==1){
	$hm = 1;
	$hmTitle = $_SESSION['HM-title'];
	$hmMessage = $_SESSION['HM-message'];
	unset($_SESSION['HM']);
}
if(isset($_SESSION['SM']) and $_SESSION['SM']==1){
	$sm = 1;
	$smTitle = $_SESSION['SM-title'];
	$smMessage = $_SESSION['SM-message'];
	unset($_SESSION['SM']);
}
?>
<body>
<section class="MainContent" id="mainContent" style="height:100vh">
	<?php $_SESSION['menu-header']=1; require_once('menu-header.php'); ?>
	<section class="Cent" id="changePass">
		<div class="Cent-div" id="cent-div">
			<h3 class="Cent-title">Change Password</h3><hr/>
			<?php
				if(isset($hm) and $hm==1){
					echo('<div class="HM">
						<h4 class="HM-title"><pre>'.$hmTitle.'</pre></h4>
						<pre class="HM-message">'.$hmMessage.'</pre>
					</div>
					');
				}
				if(isset($sm) and $sm==1){
					echo('<div class="SM">
						<h4 class="SM-title"><pre>'.$smTitle.'</pre></h4>
						<pre class="SM-message">'.$smMessage.'</pre>
					</div>
					');
				}
			?>
			<form id="changeForm" action="php/_changePassword.php" method="post">
				<input class="Cred" type="password" name="opass" value="" placeholder="Old Password" required/><p/>
				<input class="Cred" type="password" name="npass" value="" placeholder="New Password" required/><p/>
				<input class="Cred" type="password" name="cpass" value="" placeholder="Confirm New Password" required/><p/>
				<button class="Cred Cred-button" id="cButton" type="submit" name="change" value="change">Change Password</button><p/>
			</form>
		</div>
	</section>
</section>

</body>
</html>
