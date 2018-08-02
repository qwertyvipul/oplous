<!doctype html>
<html>
<head>
	<title>Find Account</title>
	<?php require_once('meta-tags.html'); ?>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/dropCheck.js"></script>
	<script src="js/cent-align.js"></script>
</head>
<?php //PAGE-2
die(header('Location:login.php'));
//--------------------------------------------------------------------------//
session_start();
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php'); //Server
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php'); //Login Check
$loginStatus = getStatus($pdo);
if($loginStatus!=0) die(header('Location:index.php'));

//optional variables
if(isset($_SESSION['HM']) and $_SESSION['HM']==1){
	$hm = 1;
	$hmTitle = $_SESSION['HM-title'];
	$hmMessage = $_SESSION['HM-message'];
	unset($_SESSION['HM']);
}
?>
<body>
	<section class="Cent">
	<div class="Cent-div" id="findDiv">
		<h3 class="Cent-title">Find Your Account</h3><hr/>
		<?php
			if(isset($hm) and $hm==1){
				echo('<div class="HM">
					<h4 class="HM-title"><pre>'.$hmTitle.'</pre></h4>
					<pre class="HM-message">'.$hmMessage.'</pre>
				</div>
				');
			}
		?>
		<form id="findForm" action="php/_findAccount.php" method="post">
			<select class="Cred" id="aTDrop" name="accountType">
				<option value="0">Select Account Type</option>
				<option value="1">Teacher</option>
				<option value="2">Student</option>
			</select><p/>
			<input class="Cred" type="email" name="emailId" value="" placeholder="Email Id" required/><p/>
			<button class="Cred Cred-button" id="fButton" type="submit" name="find" value="find">Find Account</button><p/>
		</form>
	</div>
	</section>
</body>
</html>