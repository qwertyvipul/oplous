<?php //PAGE-40
die(header('Location:index.php'));

//--------------------------------------------------------------------------------//
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('checkLogin.php');
$_SESSION['cryptPassword']=1; require_once('cryptPassword.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=0) die(header('Location:index.php'));

function resetKill($title, $message){
	$_SESSION['HM']=1;
	$_SESSION['HM-title']=$title;
	$_SESSION['HM-message']=$message;
	die(header('Location:../reset-password.php'));
}

function resetSuccess($title, $message){
	$_SESSION['HM']=1;
	$_SESSION['HM-title']=$title;
	$_SESSION['HM-message']=$message;
	die(header('Location:../login.php'));
}

//required variables
if(!isset($_SESSION['email']) or !isset($_SESSION['otp']) or !isset($_SESSION['accountType']) or !isset($_POST['reset']) or $_POST['reset']!='reset' or !isset($_POST['password']) or !isset($_POST['cpassword'])) die('<b>Error 40_101: </b>Access denied!');
if($_POST['password']!=$_POST['cpassword']) resetKill('Reset Failed!', 'Password do not match');

$password = $_POST['password'];
if(strlen($password)<8 or strlen($password)>20) resetKill('Reset Failed!', 'Password must be between 8 to 20 characters');
$hash = generatePassHash($password);
$password = $hash;

$email = $_SESSION['email'];
$type = $_SESSION['accountType'];

if($type==1){
	$query = "update teachers set password = :password where email_id = :email_id";
	try{
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':password', $password);
		$stmt->bindParam(':email_id', $email);
		if(!$stmt->execute()) resetKill('Error 40_102', 'Some unknown error occured.');
		resetSuccess('Hurrah!', 'Password changed successfully');
	}catch(PDOException $ex){
		resetKill('Error 40_103', 'Some unknown error occured.');
	}
	
}else if($type==2){
	$query = "update students set password = :password where email_id = :email_id";
	try{
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':password', $password);
		$stmt->bindParam(':email_id', $email);
		if(!$stmt->execute()) resetKill('Error 40_104', 'Some unknown error occured.');
		resetSuccess('Hurrah!', 'Password changed successfully');
	}catch(PDOException $ex){
		die($ex);
		resetKill('Error 40_105', 'Some unknown error occured.');
	}
	
}else{
	resetKill('Reset Failed!', 'Some unknown error occured');
}
?>