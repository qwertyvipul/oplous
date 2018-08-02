<?php //PAGE-18 //AJAX Called
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=1) die(header('Location:index.php'));

//required variables
if(!isset($_POST['aid']) or !isset($_POST['roll']) or !isset($_GET['flow'])) die('Unable to respond, please try again!');
$aid = intval($_POST['aid']);
$roll = intval($_POST['roll']);

//verifying permission
$query = "select ai.class_id as class_id, ci.teacher_id as user_id, ai.count as count
from class_info ci
inner join atd_info ai
on (ai.class_id = ci.class_id and ai.atd_id = :aid)";
try{
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':aid', $aid, PDO::PARAM_INT);
	if(!$stmt->execute() or $stmt->rowCount()!=1) error('18_101');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$userId = $result['user_id'];
	if($userId!=$_SESSION['userId']) die('Permission denied!');	
	$cid = $result['class_id'];
	$count = $result['count'];
}catch(PDOException $ex){
	die('Some unknown error occured, please try again!');
}

if($_GET['flow']==1){
	try{
		$query = "select total, present, absent
		from class_record
		where class_id = :class_id
		and student_id = :student_id";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
		$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
		if(!$stmt->execute() or $stmt->rowCount()!=1) die('Some unknown error occured, please try again!');
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$total = $result['total'];
		$present = $result['present'];
		$absent = $result['absent'];
		
		$percent = ($total==0)?0:round($present/$total*100, 1);
		echo('
			<p class="TVS-info TS-total"><b>Total :</b> '.$total.'</p>
			<p class="TVS-info TS-present"><b>Present :</b> '.$present.'</p>
			<p class="TVS-info TS-absent"><b>Absent :</b> '.$absent.'</p>
			<p class="TVS-info TS-percent"><b>Percent :</b> '.$percent.'</p>
		');
	}catch(PDOException $ex){
		die('Some unknown error occured, please try again!');
	}
	die();
}else if($_GET['flow']==2){
	try{
		$query = "select status
		from atd_log
		where atd_id = :atd_id
		and student_id = :student_id";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':atd_id', $aid, PDO::PARAM_INT);
		$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
		if(!$stmt->execute() or $stmt->rowCount()!=1) die('Some unknown error occured, please try again!');
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$status = $result['status'];
		if($status==0){
			echo('A');
		}else if($status==1){
			echo('P');
		}else{
			die('Some unknown error occured, please try again!');
		}
	}catch(PDOException $ex){
		die('Some unknown error occured, please try again!');
	}
	die();
}
?>