<?php //PAGE-48
session_start();
$_SESSION['_pdoConnect']=1; require_once('connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus==0) die(header('Location:index.php'));
$userId = $_SESSION['userId'];
$type = $loginStatus;

function supportKill($title, $message){
	$_SESSION['HM']=1;
	$_SESSION['HM-title']=$title;
	$_SESSION['HM-message']=$message;
	die(header('Location:../support.php'));
}

function goodKill($title, $message){
	$_SESSION['SM']=1;
	$_SESSION['SM-title']=$title;
	$_SESSION['SM-message']=$message;
	die(header('Location:../support.php'));
}

//required variables
if(!isset($_POST['support']) or $_POST['support']!='1' or !isset($_POST['supportEmail']) or !isset($_POST['supportTitle']) or !isset($_POST['supportInfo'])) supportKill('Error 48_101', 'Page breakdown occured!');

//verifying the data
$email = $_POST['supportEmail'];
$title = $_POST['supportTitle'];
$info = $_POST['supportInfo'];

if(!filter_var($email, FILTER_VALIDATE_EMAIL))  supportKill('Invalid Email', 'Please try again with a valid email address.');
if(strlen($title)>60) supportKill('Title too big, You exceeded the 60 characters limit!');
if(strlen($info)>255) supportKill('Description too big', 'You exceeded the 255 characters limit!');

if($type==1){
	try{
		$query = "select * from teacher_support where teacher_id = :teacher_id and support_status=0";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':teacher_id', $userId, PDO::PARAM_INT);
		if(!$stmt->execute()) supportKill('Error 48_102', 'Some unknown error occured!');
		if($stmt->rowCount()>2) supportKill('Failed to submit', 'Sorry for the inconvenience, but currently we are experiencing heavy load. One user can only have maximum 3 request pending at a time. We are working on all your requests and will sort it out soon!');
		
		$query = "insert into teacher_support (teacher_id, email_id, support_title, support_info, date_time)
		values(:teacher_id, :email_id, :support_title, :support_info, now())";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':teacher_id', $userId, PDO::PARAM_INT);
		$stmt->bindParam(':email_id', $email);
		$stmt->bindParam(':support_title', $title);
		$stmt->bindParam(':support_info', $info);
		if(!$stmt->execute()) supportKill('Error 48_103', 'Some unknown error occured!');
		goodKill('Thank you for getting in touch', 'We have registered you complaint and will get back to you shortly.');
	}catch(PDOException $ex){
		supportKill('Error 48_104', 'Some unknown error occured!');
	}
}else if($type==2){
	try{
		$query = "select * from student_support where student_id = :student_id and support_status = 0";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':student_id', $userId, PDO::PARAM_INT);
		if(!$stmt->execute()) supportKill('Error 48_105', 'Some unknown error occured!');
		if($stmt->rowCount()>2) supportKill('Failed to submit', 'Sorry for the inconvenience, but currently we are experiencing a heavy load. One user can only have maximum 3 request pending at a time. We are working on all your requests and will sort it out soon!');
		
		$query = "insert into student_support (student_id, email_id, support_title, support_info, date_time)
		values(:student_id, :email_id, :support_title, :support_info, now())";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':student_id', $userId, PDO::PARAM_INT);
		$stmt->bindParam(':email_id', $email);
		$stmt->bindParam(':support_title', $title);
		$stmt->bindParam(':support_info', $info);
		if(!$stmt->execute()) supportKill('Error 48_106', 'Some unknown error occured!');
		goodKill('Thank you for getting in touch', 'We have registered you complaint and will get back to you shortly.');
	}catch(PDOException $ex){
		supportKill('Error 48_107', 'Some unknown error occured!');
	}
}
	

?>