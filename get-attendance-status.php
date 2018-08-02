<?php //PAGE_3 //AJAX CALLED
@session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['error-code']=1; require_once('error-code.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=1) error('3_101');

//check required variables
if(!isset($_POST['aid']) or !isset($_POST['roll'])) error('3_102');
$aid = $_POST['aid'];
$roll = $_POST['roll'];

//verify permissions
$query = "select ci.teacher_id as teacher_id
from class_info ci
inner join atd_info ai
on(ai.class_id = ci.class_id and ai.atd_id = :atd_id)";
try{
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':atd_id', $aid, PDO::PARAM_INT);
	if(!$stmt->execute()) error('3_104');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$userId = $result['teacher_id'];
	if($userId!=$_SESSION['userId']) error('3_105');
}catch(PDOException $ex){
	error('3_103');
}

//fetching status
$query = "select status from atd_log where atd_id = :atd_id and student_id = :student_id;";
try{
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':atd_id', $aid, PDO::PARAM_INT);
	$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$status = $result['status'];
	if($status==1){
		$response='P';
		echo($response);
		die();
	}else if($status==0){
		$response='A';
		echo($response);
		die();
	}else{
		error('3_106');
	}
}catch(PDOException $ex){
	error('3_107');
}
?>