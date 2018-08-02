<?php //PAGE-20
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=1) die(header('Location:login.php')); //login checked //access checked

//required variables //markCount and classId
if(!isset($_POST['markCount']) or !isset($_GET['cid'])) die('<b>Error 20_101:</b> Access Denied!');
$markCount = intval($_POST['markCount']);
if(!is_numeric($_GET['cid'])) die('<b>Error 20_102:</b> Access Denied!');
$cid = intval($_GET['cid']);

//verifying permissions
try{
	$query = "select ci.teacher_id as teacher_id, t.name as name,
	concat(si.subject_code, ci.class_type) as class
	from class_info ci
	inner join subject_info si
	on(ci.subject_id = si.subject_id and ci.class_id = :class_id)
	inner join teachers t on(ci.teacher_id = t.teacher_id)";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	if(!$stmt->execute())  die('<b>Error 20_103:</b> Access Denied!');
	$rowCount = $stmt->rowCount();
	if($rowCount!=1) die('<b>Error 20_103:</b> Some unknown error occured!');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$userId = $result['teacher_id'];
	$name = $result['name'];
	$class = $result['class'];
	if($userId != $_SESSION['userId']) die('<b>Error 20_104:</b> Access Denied!');
}catch(PODException $ex){
	die('<b>Error 20_105:</b> Some unknown error occured!');
}

//fetching batch details

//marking attendance
try{
	$pdo->beginTransaction();
	
	//updating atd_info
	$query = "insert into atd_info(class_id, count, date_time)
	values(:class_id, :count, now());";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	$stmt->bindParam(':count', $markCount, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 20_106:</b> Some unknown error occured!');
	$aid = $pdo->lastInsertId();
	
	//selecting all students from class_record
	$query = "select student_id, status as cstatus from class_record where class_id=:class_id;";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 20_107:</b> Some unknown error occured!');
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$total=0;
	$present=0;
	$absent=0;
	foreach($result as $row){
		$roll = $row['student_id'];
		$cstatus = $row['cstatus'];
		if($cstatus==1){
			if(!isset($_POST[strval($roll)]) or ($_POST[strval($roll)]!='1' and $_POST[strval($roll)]!='0')) die('<b>Error 20_107:</b> Some unknown error occured!');
			$status = intval($_POST[strval($roll)]);
			
			//inserting the status in the attendance log
			$query = "insert into atd_log(atd_id, student_id, status)
			values (:atd_id, :student_id, :status);";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':atd_id', $aid, PDO::PARAM_INT);
			$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
			$stmt->bindParam(':status', $status, PDO::PARAM_INT);
			if(!$stmt->execute()) die('<b>Error 20_108:</b> Some unknown error occured!');
			
			//generating notification
			$total+=1;
			if($status==1){
				$present+=1;
				$result = 'Present';
			}else{
				$absent+=1;
				$result = 'Absent';
			}
			
			$data = 'You were marked <b>'.$result.'</b> in <b>'.$class.'</b>.';
			$sdetail = '
				Marked by: '.$name.'
				Attendance Id: '.$aid.'
				Count: '.$markCount.'
				Status: '.$result.'
				';
				
			$query = "insert into student_notifications(student_id, summary, description, date_time)
			values(:user_id, :summary, :description, now())";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':user_id', $roll, PDO::PARAM_INT);
			$stmt->bindParam(':summary', $data);
			$stmt->bindParam(':description', $sdetail);
			if(!$stmt->execute()) die('<b>Error 20_109:</b> Some unknown error occured!');
			
			$query = "select total, present, absent from class_record where class_id = :class_id and student_id = :student_id;";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
			$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
			if(!$stmt->execute()) die('<b>Error 20_110:</b> Some unknown error occured!');
			$sr1 = $stmt->fetch(PDO::FETCH_ASSOC);
			$total = $sr1['total'];
			$present = $sr1['present'];
			$absent = $sr1['absent'];
			
			if($status==0){
				$total+=$markCount;
				$absent+=$markCount;
			}else if($status==1){
				$total+=$markCount;
				$present+=$markCount;
			}
			
			$query = "update class_record
			set total = :total,
			present = :present,
			absent = :absent
			where class_id = :class_id and student_id = :student_id;";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':total', $total, PDO::PARAM_INT);
			$stmt->bindParam(':present', $present, PDO::PARAM_INT);
			$stmt->bindParam(':absent', $absent, PDO::PARAM_INT);
			$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
			$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
			if(!$stmt->execute()) die('<b>Error 20_111:</b> Some unknown error occured!');
		}
	}
	//teacher notification
	$data = 'The attendance for <b>'.$class.'</b> was successfully marked!';
	$tdetail = '
		Attendance Id = '$aid'
		Count = '.$markCount.'
		Present = '.$present.'/'.$total.';
		Absent = '.$absent.'/'.$total.';
		';
	$query = "insert into teacher_notifications(teacher_id, summary, date_time)
	values(:user_id, :summary, now())";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
	$stmt->bindParam(':summary', $data);
	if(!$stmt->execute()) die('<b>Error 20_112:</b> Some unknown error occured!');
	
	$pdo->commit();
	die(header('Location:view-summary.php?aid='.$aid.''));
}catch(PDOException $ex){
	$pdo->rollBack();
	die('<b>Error 20_113:</b> Some unknown error occured!');
}
?>