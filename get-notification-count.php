<?php //PAGE-5 //AJAX Called
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus==0) die(header('Location:index.php'));

$uid = $_SESSION['userId'];
$type = $loginStatus;

//fetch result

if($type==1){
	try{
		$query = "select count(nid) as total from teacher_notifications where teacher_id = :user_id and read_code=0";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':user_id', $uid, PDO::PARAM_INT);
		if(!$stmt->execute()) die('<span>Notifications</span>');
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$count = $result['total'];
		if($count==0) die('<span>Notifications</span>');
		die('<span class="ActiveMenu" style="color:#1151ff">Notifications('.$count.')</span>');
	}catch(PDOException $ex){
		die('<span>Notifications</span>');
	}
}else{
	try{
		$query = "select count(nid) as total from student_notifications where student_id = :user_id and read_code=0";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':user_id', $uid, PDO::PARAM_INT);
		if(!$stmt->execute()) die('<span>Notifications</span>');
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$count = $result['total'];
		if($count==0) die('<span>Notifications</span>');
		die('<span class="ActiveMenu" style="color:#1151ff">Notifications('.$count.')</span>');
	}catch(PDOException $ex){
		die('<span>Notifications</span>');
	}
}

?>