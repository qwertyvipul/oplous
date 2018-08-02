<?php //PAGE-47
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus==0) die(header('Location:login.php'));
$userId = $_SESSION['userId'];
$type = $loginStatus;

function changeKill($title, $message){
	$_SESSION['HM']=1;
	$_SESSION['HM-title']=$title;
	$_SESSION['HM-message']=$message;
	die(header('Location:../change-password.php'));
}

function goodKill($title, $message){
	$_SESSION['SM']=1;
	$_SESSION['SM-title']=$title;
	$_SESSION['SM-message']=$message;
	die(header('Location:../change-password.php'));
}

//required variables
if(!isset($_POST['opass']) or !isset($_POST['npass']) or !isset($_POST['cpass'])) changeKill('Error', 'Please try again!');

$opass = $_POST['opass'];
$npass = $_POST['npass'];
$cpass = $_POST['cpass'];

$_SESSION['cryptPassword'] = 1; require_once('cryptPassword.php');
$ohash = generatePassHash($opass);

try{
	if($type==1){
		$query = "select password from teachers where teacher_id = :teacher_id";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam('teacher_id', $userId, PDO::PARAM_INT);
		if(!$stmt->execute() or $stmt->rowCount()!=1) changeKill('Error 47_101', 'Some unknown error occured!');
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$password = $result['password'];
		if($ohash != $password) changeKill('Failed', 'Wrong old password!');
		if(strlen($npass)<8 or strlen($npass)>20) changeKill('Failed', 'Password must be between 8-20 characters!');
		if($npass != $cpass)  changeKill('Failed', 'Passwords do not match!');
		
		$newhash = generatePassHash($npass);
		$query = "update teachers set password = :password where teacher_id = :teacher_id";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam('password', $newhash);
		$stmt->bindParam('teacher_id', $userId);
		if(!$stmt->execute())changeKill('Error 47_102', 'Some unknown error occured!');
		goodKill('O Yeah!', 'Your Password was changed successfully!');
	}else if($type==2){
		$query = "select password from students where student_id = :student_id";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam('student_id', $userId, PDO::PARAM_INT);
		if(!$stmt->execute() or $stmt->rowCount()!=1) changeKill('Error 47_103', 'Some unknown error occured!');
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$password = $result['password'];
		if($ohash != $password) changeKill('Failed', 'Wrong old password!');
		if(strlen($npass)<8 or strlen($npass)>20) changeKill('Failed', 'Password must be between 8-20 characters!');
		if($npass != $cpass)  changeKill('Failed', 'Passwords do not match!');
		
		$newhash = generatePassHash($npass);
		$query = "update students set password = :password where student_id = :student_id";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam('password', $newhash);
		$stmt->bindParam('student_id', $userId);
		if(!$stmt->execute())changeKill('Error 47_104', 'Some unknown error occured!');
		goodKill('O Yeah!', 'Your Password was changed successfully!');
	}else{
		changeKill('Error 47_105', 'Some unknown error occured!');
	}
}catch(PDOException $ex){
	die($ex);
	changeKill('Error 47_106', 'Some unknown error occured!');
}


?>