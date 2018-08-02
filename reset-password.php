<!doctype html>
<html>
<head>
	<title>Reset Password</title>
	<?php require_once('meta-tags.html'); ?>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/cent-align.js"></script>
</head>
<?php //PAGE-39
die(header('Location:index.php'));

//--------------------------------------------------------------------------------//
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=0) die(header('Location:index.php'));

if(!isset($_SESSION['email']) or !isset($_SESSION['otp']) or !isset($_SESSION['accountType'])) die('<b>Error 39_101: </b>Access denied!');

if(isset($_SESSION['HM']) and $_SESSION['HM']==1){
	$hm = 1;
	$hmTitle = $_SESSION['HM-title'];
	$hmMessage = $_SESSION['HM-message'];
	$_SESSION['HM']=0;
	unset($_SESSION['HM']);
}
?>
<body>
	<section class="Cent">
	<div class="Cent-div" id="resetDiv">
		<h3 class="Cent-title">Reset Password</h3><hr/>
		<?php
			if(isset($hm) and $hm==1){
				echo('
				<div class="HM">
					<p class="HM-title">'.$hmTitle.'</p>
					<p class="HM-message">'.$hmMessage.'</p>
				</div>
				');
			}
		?>
		<form id="resetForm" action="php/_resetPassword.php" method="post">
			<input class="Cred" type="password" name="password" value="" placeholder="New Password" required/><p/>
			<input class="Cred" type="password" name="cpassword" value="" placeholder="Confirm Password" required/><p/>
			<button class="Cred Cred-button" id="resetButton" type="submit" name="reset" value="reset">Reset</button><p/>
		</form>
	</div>
	</section>
</body>
</html>