<?php //PAGE-4 //AJAX Called
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=1) die(header('Location:index.php'));

//required variables
if(!isset($_GET['aid']) or !isset($_GET['roll'])) die('<b>Error 4_101:</b> Page Breakdown!');
if(!is_numeric($_GET['aid']) or !is_numeric($_GET['roll'])) die('<b>Error 4_102:</b> Page Breakdown!');
$aid = $_GET['aid'];
$roll = $_GET['roll'];

//verifying permissions
try{
	$query = "select ci.teacher_id as teacher_id,
	ci.batch_id as batch_id,
	cr.total as total,
	cr.present as present,
	cr.absent as absent,
	al.status as status
	from class_info ci
	inner join atd_info ai
	on(ai.class_id = ci.class_id and ai.atd_id = :atd_id)
	inner join class_record cr
	on(cr.class_id = ci.class_id and cr.student_id = :student_id)
	inner join atd_log al
	on(ai.atd_id = al.atd_id and al.student_id = :astudent_id)";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':atd_id', $aid, PDO::PARAM_INT);
	$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
	$stmt->bindParam(':astudent_id', $roll, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 4_103:</b> Some unknown error occured!');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$userId = $result['teacher_id'];
	if($userId != $_SESSION['userId']) die('<b>Error 4_104:</b> Access Denied!');
	$bid = $result['batch_id'];
	$total = $result['total'];
	$present = $result['present'];
	$absent = $result['absent'];
	$status = $result['status'];
}catch(PDOException $ex){
	die('<b>Error 4_105:</b> Some unknown error occured!');
}

//fetching result
try{
	$query = "select name from students where student_id = :student_id";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 4_106:</b> Some unknown error occured!');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$name = $result['name'];
	
	$query = "select serial_no from batch_record where batch_id = :batch_id and student_id = :student_id";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':batch_id', $bid, PDO::PARAM_INT);
	$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 4_107:</b> Some unknown error occured!');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$serial = $result['serial_no'];
	
	$percent = ($total==0)?0:round($present/$total*100, 1);
	if($status==0){
		$curstatus = 'A';
	}else if($status==1){
		$curstatus = 'P';
	}else{
		die('<b>Error 4_108:</b> Some unknown error occured!');
	}
	
	echo('
		<span>Roll No. : </span>	<span id="vsp-roll">'.$roll.'</span><br/>
		<span>Name : </span>		<span id="vsp-name">'.$name.'</span><br/>
		<span>S.No : </span>		<span id="vsp-serial">'.$serial.'</span><br/>
		<span>Total : </span>		<span id="vsp-total">'.$total.'</span><br/>
		<span>Present : </span>		<span id="vsp-present">'.$present.'</span><br/>
		<span>Absent : </span>		<span id="vsp-absent">'.$absent.'</span><br/>
		<span>Percent : </span>		<span id="vs-percent">'.$percent.'</span><br/>
		<span>Current Status : </span><span id="vsp-status">'.$curstatus.'</span><span> </span><button class="ID-item-button" id="vsp-updateButton" onclick="updateStatus('.$aid.', '.$roll.', '.$status.')"><span>CHANGE</span></button><br/>
	');
}catch(PDOException $ex){
	die('<b>Error 4_109:</b> Some unknown error occured!');
}
?>