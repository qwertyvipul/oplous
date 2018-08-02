<?php //PAGE-42
session_start();
$_SESSION['_pdoConnect']=1; require_once('connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=0) die(header('Location:index.php'));

function loginKill($title, $message){
	$_SESSION['HM']=1;
	$_SESSION['HM-title']=$title;
	$_SESSION['HM-message']=$message;
	die(header('Location:../login.php'));
}

if(!isset($_POST["login"]) or $_POST["login"]!="login" or !isset($_POST['accountType']) or !isset($_POST['emailId']) or !isset($_POST['password'])) loginKill('Error 42_101', 'Attempt Failed. Please try again.');

$accountType=$_POST["accountType"];
$email=strtolower($_POST["emailId"]);
$password=$_POST["password"];

if(!filter_var($email, FILTER_VALIDATE_EMAIL))  loginKill('Invalid Email', 'Please try again with a valid email address.');
if(!is_numeric($accountType)) loginKill('Attempt Failed!', 'Please try again.');

$_SESSION['cryptPassword'] = 1; require_once('cryptPassword.php');
$passHash = generatePassHash($password);
$password = $passHash;

if($accountType==1){
	try{
		$query = "select teacher_id from teachers where email_id = :email_id and password = :password";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':email_id', $email);
		$stmt->bindParam(':password', $password);
		if(!$stmt->execute()) loginKill('Error 42_102', 'Some unknown error occured!');
	}catch(PDOException $ex){
		loginKill('Error 42_103', 'Some unknown error occured!');
	}
}else if($accountType==2){
	try{
		$query = "select student_id from students where email_id = :email_id and password = :password";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':email_id', $email);
		$stmt->bindParam(':password', $password);
		if(!$stmt->execute()) loginKill('Error 42_104', 'Some unknown error occured!');
	}catch(PDOException $ex){
		loginKill('Error 42_105', 'Some unknown error occured!');
	}
}else{
	loginKill('Error 42_106', 'Some unknown error occured!');
}

$rowCount = $stmt->rowCount();
if($rowCount==0){
	loginKill('Login Failed', 'Please try again with correct credentials.');
}else if($rowCount==1){ //on successful login
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	if($accountType==1){
		$userId = $result['teacher_id'];
	}else if($accountType==2){
		$userId = $result['student_id'];
	}
	session_unset();
	session_destroy();
	session_start();
	
	//set cookies if memory
	if(isset($_POST['memory']) and $_POST['memory']=1){
		$csalt = true; $salt=bin2hex(openssl_random_pseudo_bytes(16, $csalt));
		$ctoken = true; $token=bin2hex(openssl_random_pseudo_bytes(64, $ctoken));
		setcookie('oplous_x' , $salt, time()+60*60*24*7, '/', NULL, NULL, TRUE);
		setcookie('oplous_y' , $token, time()+60*60*24*7, '/', NULL, NULL, TRUE);
		$token .= $salt;
		$token=openssl_digest($token, 'sha512');
		$query="insert into tokens(account_type, user_id, password, token, date_time) 
		values(:account_type, :user_id, :password, :token, now())";
		try{
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':account_type', $accountType, PDO::PARAM_INT);
			$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
			$stmt->bindParam(':password', $password);
			$stmt->bindParam(':token', $token);
			if(!$stmt->execute()) loginKill('Error 42_107', 'Some unknown error occured!');
		}catch(PDOException $ex){
			loginKill('Error 42_108', 'Some unknown error occured!');
		}
	}
	$_SESSION['userId']=$userId;
	$_SESSION['\''.$userId.'\'']=$accountType;
	die(header("Location:index.php"));
}else{
	loginKill('Error 42_109', 'Some unknown error occured!');;
}
?>