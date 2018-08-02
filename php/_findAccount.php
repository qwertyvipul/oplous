<?php //PAGE-35
die(header('Location:index.php'));

//--------------------------------------------------------------------------------//
session_start();
$_SESSION['_pdoConnect']=1; require_once('connect/_pdoConnect.php'); //Server
$_SESSION['checkLogin']=1; require_once('checkLogin.php'); //Login Check
$loginStatus = getStatus($pdo);
if($loginStatus!=0) die(header('Location:index.php'));

function findKill($title, $message){
	$_SESSION['HM']=1;
	$_SESSION['HM-title']=$title;
	$_SESSION['HM-message']=$message;
	die(header('Location:../find-account.php'));
}

//required variables
if(!isset($_POST['find']) or $_POST['find']!='find' or !isset($_POST['accountType']) or !isset($_POST['emailId'])) findKill('<b>Error 35_101</b>', 'Please, try again');
if(!is_numeric($_POST['accountType'])) findKill('<b>Error 35_102</b>', 'Please, try again');
$email=strtolower($_POST["emailId"]);
if(!filter_var($email, FILTER_VALIDATE_EMAIL))  findKill('Invalid Email!', 'Please enter a valid email address.');
$accountType=intval($_POST["accountType"]);

if($accountType==1){
	try{
		$query = "select first_name from teachers where email_id = :email_id";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':email_id', $email);
		if(!$stmt->execute()) findKill('<b>Error 35_103</b>', 'Some unknown error occured, please try again.');
	}catch(PDOException $ex){
		findKill('<b>Error 35_104</b>', 'Some unknown error occured, please try again.');
	}
}else if($accountType==2){
	try{
		$query = "select first_name from students where email_id = :email_id";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':email_id', $email);
		if(!$stmt->execute()) findKill('<b>Error 35_105</b>', 'Some unknown error occured, please try again.');
	}catch(PDOException $ex){
		findKill('<b>Error 35_106</b>', 'Some unknown error occured, please try again.');
	}
}else{
	findKill('<b>Error 35_107</b>', 'Please try again.');
}

$rowCount = $stmt->rowCount();
if($rowCount==0){
	findKill('Account Not Found', 'Please try again with correct credentials.');
}else if($rowCount==1){ //on finding the account
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$firstName = $result['first_name'];
	session_unset();
	session_destroy();
	session_start();
	$otp = mt_rand(100000, 999999);
	$_SESSION['email'] = $email;
	$_SESSION['accountType'] = $accountType;
	$_SESSION['otp'] = $otp;
	
	//on successful login
	$subject='Verify OTP';
	$message='Hey '.$firstName.', Your OTP to reset your password for Oplous account is '.$otp.'. Please make sure to not share this OTP with anyone.';
	mail($email, $subject, $message);
	die(header("Location:../verify-otp.php?find=1"));
}else{
	findKill('<b>Error 35_108</b>', 'Some unknown error occured, please try again.');
}

?>