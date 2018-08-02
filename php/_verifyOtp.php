<?php //PAGE-38
die(header('Location:index.php'));

//--------------------------------------------------------------------------------//
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=0) die(header('Location:index.php'));

function findKill($title, $message){
	$_SESSION['HM']=1;
	$_SESSION['HM-title']=$title;
	$_SESSION['HM-message']=$message;
	die(header('Location:../verify-otp.php?find=1'));
}

function signKill($title, $message){
	$_SESSION['HM']=1;
	$_SESSION['HM-title']=$title;
	$_SESSION['HM-message']=$message;
	die(header('Location:../verify-otp.php?sign=1'));
}

function signSuccess($title, $message){
	$_SESSION['SM']=1;
	$_SESSION['SM-title']=$title;
	$_SESSION['SM-message']=$message;
	die(header('Location:../login.php'));
}
if(!isset($_POST['verify']) or $_POST['verify']!='verify' or !isset($_POST['otp'])) die('<b>Error 38_101:</b> Access Denied!');
if(!is_numeric($_POST['otp'])) die('<b>Error 38_102:</b> Page Breakdown');
$otp = $_POST['otp'];

//for find account
if(isset($_GET['find']) and $_GET['find']==1){
	if(!isset($_SESSION['email']) or !isset($_SESSION['otp']) or !isset($_SESSION['accountType'])) findKill('Failed Attempt!', 'Please try again!');
	if($otp!=$_SESSION['otp']) findKill('Incorrect OTP!', 'Please enter correct OTP!');
	die(header('Location:../reset-password.php'));
	
}else if(isset($_GET['sign']) and $_GET['sign']==1){
	if(!isset($_SESSION['email']) or !isset($_SESSION['accountType']) or !isset($_SESSION['password']) or !isset($_SESSION['fname']) or !isset($_SESSION['lname']) or !isset($_SESSION['otp'])) signKill('Failed Attempt!', 'Please try again!');;
	if($otp!=$_SESSION['otp']) signKill('Incorrect OTP!', 'Please enter correct OTP!');
		
	$email = $_SESSION['email'];
	$accountType = $_SESSION['accountType'];
	$password = $_SESSION['password'];
	$fname = $_SESSION['fname'];
	$lname = $_SESSION['lname'];
	if($accountType==1){
		$query = "insert into teachers(first_name, last_name, email_id, password)
		values(:first_name, :last_name, :email_id, :password)";
		try{
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':first_name', $fname);
			$stmt->bindParam(':last_name', $lname);
			$stmt->bindParam(':email_id', $email);
			$stmt->bindParam(':password', $password);
			if(!$stmt->execute()) signKill('Sign Up Failed!', 'Some unknown error occured.');
			signSuccess('Sign Up Successful', 'Login to your account!');
		}catch(PDOException $ex){
			signKill('Error 38_103', 'Some unknown error occured.');
		}
		
	}else if($accountType==2){
		if(!isset($_SESSION['roll'])) die(header('Location:error.php'));
		$roll = intval($_SESSION['roll']);
		$query = "insert into students(student_id, first_name, last_name, email_id, password)
		values(:student_id, :first_name, :last_name, :email_id, :password)";
		try{
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
			$stmt->bindParam(':first_name', $fname);
			$stmt->bindParam(':last_name', $lname);
			$stmt->bindParam(':email_id', $email);
			$stmt->bindParam(':password', $password);
			if(!$stmt->execute()) signKill('Sign Up Failed!', 'Some unknown error occured.');
			signSuccess('Sign Up Successful', 'Login to your account!');
		}catch(PDOException $ex){
			signKill('Error 38_104', 'Some unknown error occured.');
		}
	}else{
		signKill('Fatal Error 38_105', 'Some unknown error occured.');
	}
}else{
	die('<b>Error 38_106:</b> Illegal access denied!');
}
?>