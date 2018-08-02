<?php //PAGE-32 //AJAX-called
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=1) die(header('Location:index.php'));

//required variables
if(!isset($_GET['aid']) or !isset($_GET['roll']) or !isset($_GET['status'])) die('Error 32_101: Updation Failed!');
$aid = intval($_GET['aid']);
$roll = intval($_GET['roll']);
$status = intval($_GET['status']);

if($status==0){
	$newStatus=1;
}else if($status==1){
	$newStatus=0;
}else{
	die('Error 32_102: Updation Failed!');
}

//verifying permission
$query = "select ai.class_id as class_id, 
concat(si.subject_code,ci.class_type) as class,
ci.teacher_id as user_id, ai.count as count
from class_info ci
inner join atd_info ai
on (ai.class_id = ci.class_id and ai.atd_id = :aid)
inner join subject_info si
on(ci.subject_id = si.subject_id)";
try{
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':aid', $aid, PDO::PARAM_INT);
	if(!$stmt->execute() or $stmt->rowCount()!=1) die('Error 32_103: Unknown Error');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$userId = $result['user_id'];
	if($userId!=$_SESSION['userId']) die('Error 32_104: Permission Denied!');	
	$cid = $result['class_id'];
	$class = $result['class'];
	$count = $result['count'];
}catch(PDOException $ex){
	die('Error 32_105: Unknown Error!');	
}

//verifying request
$query = "select*from atd_log where atd_id = :atd_id and student_id = :student_id and status = :status";
try{
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':atd_id', $aid, PDO::PARAM_INT);
	$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
	$stmt->bindParam(':status', $status, PDO::PARAM_INT);
	if(!$stmt->execute() or $stmt->rowCount()!=1) die('Error 32_106: Unknown Error!');	
}catch(PDOException $ex){
	die('Error 32_107: Unknown Error!');
}

//processing request
try{
	$pdo->beginTransaction();
	$query = "update atd_log
	set status = :status
	where atd_id = :atd_id and student_id = :student_id;";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':status', $newStatus, PDO::PARAM_INT);
	$stmt->bindParam(':atd_id', $aid, PDO::PARAM_INT);
	$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
	if(!$stmt->execute()) die('Error 32_108: Unknown Error!');
	
	$query = "select total, present, absent from class_record where class_id = :class_id and student_id = :student_id";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
	if(!$stmt->execute()) die('Error 32_109: Unknown Error!');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$total = $result['total'];
	$present = $result['present'];
	$absent = $result['absent'];
	
	if($newStatus==0){
		$present-=$count;
		$absent+=$count;
	}else if($newStatus==1){
		$absent-=$count;
		$present+=$count;
	}else{
		die('Error 32_110: Unknown Error!');
	}
	
	$query = "update class_record
	set present = :present,
	absent = :absent
	where class_id = :class_id and student_id = :student_id;";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':present', $present, PDO::PARAM_INT);
	$stmt->bindParam(':absent', $absent, PDO::PARAM_INT);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
	if(!$stmt->execute()) die('Error 32_111: Unknown Error!');
	
	$prev='';
	$result='';
	if($newStatus==1){
		$prev = 'Absent';
		$curr = 'Present';
	}else{
		$prev = 'Present';
		$curr = 'Absent';
	}
	
	$data = 'Your attendance was changed from <b>'.$prev.'</b> to <b>'.$curr.'</b> in <b>'.$class.'</b>.';
	$query = "insert into student_notifications(student_id, summary, date_time)
	values(:user_id, :summary, now())";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':user_id', $roll, PDO::PARAM_INT);
	$stmt->bindParam(':summary', $data);
	if(!$stmt->execute()) die('<b>Error 32_112:</b> Some unknown error occured!');
	$pdo->commit();
	echo('success');
}catch(PDOException $ex){
	$pdo->rollBack();
	die('<b>Error 32_113:</b> Some unknown error occured!');
}

?>