<?php //PAGE-26
die(header('Location:index.php'));

//--------------------------------------------------------------------------------//
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=0) die(header('Location:index.php'));

//for find account
if(isset($_GET['find']) and $_GET['find']==1){
	if(!isset($_SESSION['email']) or !isset($_SESSION['otp']) or !isset($_SESSION['accountType'])) die('<b>Error 26_101: </b>Unknown Error!');
	$email = $_SESSION['email'];
	$get = 'find=1';
	
}else if(isset($_GET['sign']) and $_GET['sign']==1){
	if(!isset($_SESSION['email']) or !isset($_SESSION['accountType']) or !isset($_SESSION['password']) or !isset($_SESSION['fname']) or !isset($_SESSION['lname']) or !isset($_SESSION['otp'])) die('<b>Error 26_102: </b>Unknown Error!');
	$email = $_SESSION['email'];
	$get = 'sign=1';
}else{
	die('<b>Error 26_103: </b>Unknown Error!');
}

if(isset($_SESSION['HM']) and $_SESSION['HM']==1){
	$hm = 1;
	$hmTitle = $_SESSION['HM-title'];
	$hmMessage = $_SESSION['HM-message'];
	$_SESSION['HM']=0;
	unset($_SESSION['HM']);
}
?>
<!doctype html>
<html>
<head>
	<title>Verify OTP</title>
	<?php require_once('meta-tags.html'); ?>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/cent-align.js"></script>
</head>
<body>
	<section class="Cent">
	<div class="Cent-div" id="otpDiv">
		<h3 class="Cent-title">Verify OTP</h3><hr/>
		<?php
			if(isset($hm) and $hm==1){
				echo('<div class="HM">
					<h4 class="HM-title"><pre>'.$hmTitle.'</pre></h4>
					<pre class="HM-message">'.$hmMessage.'</pre>
				</div>
				');
			}
		?>
		<form id="verifyForm" action="php/_verifyOtp.php?<?php echo($get); ?>" method="post">
			<input class="Cred" type="text" name="otp" value="" placeholder="Enter OTP" required/><p/>
			<button class="Cred Cred-button" id="otpButton" type="submit" name="verify" value="verify">Verify OTP</button><p/>
		</form>
	</div>
	</section>
</body>
</html>