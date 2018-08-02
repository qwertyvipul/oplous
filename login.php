<!doctype html>
<html>
<head>
	<title>Login to Oplous</title>
	<?php require_once('meta-tags.html'); ?>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/dropCheck.js"></script>
	<script src="js/cent-align.js"></script>
	
<style>
.FP{background:#ff4a44; color:#ffffff;}
.SP{background:#3bdd00; color:#ffffff;}
.Cred-box{margin:0 auto; text-align:left; max-width:90%; padding:10px 0px; color:#1181ff; font-weight:bold; }
</style>
</head>
<?php //PAGE-8
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=0) die(header('Location:index.php'));

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
	<section class="Cent LoginSection">
	<div class="Cent-div LoginDiv" id="cent-div loginDiv">
		<h3 class="Cent-title LD-title">Log In to Oplous</h3><hr/>
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
		<form id="loginForm" action="php/_login.php" method="post">
			<select class="Cred" id="aTDrop" name="accountType">
				<option value="0">Select Account Type</option>
				<option value="1">Teacher</option>
				<option value="2">Student</option>
			</select><p/>
			<input class="Cred" type="email" name="emailId" value="" placeholder="Email Id" required/><p/>
			<input class="Cred" type="password" name="password" value="" placeholder="Password" required/><p/>
			<p class="Cred-box"><input type="checkbox" name="memory" value="1"> Remember Me</input></p>
			<button class="Cred Cred-button" id="lButton" type="submit" name="login" value="login">LOG IN</button><p/>
		</form>
	</div>
	<!--<a href="find-account.php"><button class="FP Cred Cred-button">FORGOT PASSWORD?</button></a><br/>-->
	<!--<a href="select-account.php"><button class="SP Cred Cred-button">SIGN UP</button></a><br/>-->
	</section>
</body>
</html>