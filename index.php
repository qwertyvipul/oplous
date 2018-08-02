<?php //PAGE-7
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);

if($loginStatus==0) die(header('Location:login.php'));
	
if($loginStatus==1){
	$_SESSION['teacher']=1; require_once('teacher.php');
	
}else if($loginStatus==2){
	$_SESSION['student']=1; require_once('student.php');
}
?>