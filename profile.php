<?php //PAGE-12
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);

if($loginStatus==0) die(header('Location:login.php'));

//redirect to required page
if($loginStatus==1){
	$_SESSION['teacher-profile']=1; require_once('teacher-profile.php');
	
}else if($loginStatus==2){
	$_SESSION['student-profile']=1; require_once('student-profile.php');
}
?>
