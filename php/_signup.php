<?php //PAGE-43
die(header('Location:index.php'));

//--------------------------------------------------------------------------------//
session_start();
$_SESSION['_pdoConnect']=1; require_once('connect/_pdoConnect.php'); //Server
$_SESSION['checkLogin']=1; require_once('checkLogin.php'); //Login Check
$loginStatus = getStatus($pdo);
if($loginStatus!=0) die(header('Location:index.php'));

function signKill($type, $title, $message){
	$_SESSION['HM']=1;
	$_SESSION['HM-title']=$title;
	$_SESSION['HM-message']=$message;
	die(header('Location:../signup.php?type='.$type.''));
}
$type = intval($_GET['type']);
if(!isset($_POST["signup"]) or $_POST["signup"]!="signup" or !isset($_GET['type'])) signKill('Attempt Failed', 'Please try again.');
if($_GET['type']==1){
	if(!isset($_POST['emailId']) or !isset($_POST['fName']) or !isset($_POST['lName']) or !isset($_POST['password']) or !isset($_POST['cpassword'])) signKill($type, '', 'Please fill the details.');
	$email = strtolower($_POST["emailId"]);
	$fname = $_POST['fName'];
	$lname = $_POST['lName'];
	$fname = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $fname)));
	$lname = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $lname)));
	$fname = ucwords($_POST['fName']);
	$lname = ucwords($_POST['lName']);
	$password = $_POST["password"];
	$cpassword = $_POST['cpassword'];
	
}else if($_GET['type']==2){
	if(!isset($_POST['emailId']) or !isset($_POST['roll']) or !isset($_POST['fName']) or !isset($_POST['lName']) or !isset($_POST['password']) or !isset($_POST['cpassword'])) signKill($type, '', 'Please fill the details.');
	$email = strtolower($_POST["emailId"]);
	$roll = $_POST['roll'];
	$fname = $_POST['fName'];
	$lname = $_POST['lName'];
	$fname = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $fname)));
	$lname = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $lname)));
	$fname = ucwords(strtolower($fname));
	$lname = ucwords(strtolower($lname));
	$password = $_POST["password"];
	$cpassword = $_POST['cpassword'];
}else{
	signKill($type, 'Attempt Failed', 'Please try again.');
}


//authentication of data
if(!filter_var($email, FILTER_VALIDATE_EMAIL))  signKill($type, 'Sign Up Failed!', 'Please Enter a valid Email Address.');
if(!is_numeric($roll)) signKill($type, 'Sign Up Failed!', 'Please Enter a valid Roll Number.');
if(preg_match("/[^A-Za-z'-]/", $fname, $inv)) signKill($type, 'Invalid First Name!', "{$inv[0]} not allowed in first name");
if(preg_match("/[^A-Za-z'-]/", $lname, $inv)) signKill($type, 'Invalid First Name!', "{$inv[0]} not allowed in first name");
if($password!=$cpassword) signKill($type, 'Sign Up Failed!', 'Passwords do not match, please try again!');
if(strlen($password)<8 or strlen($password)>20) signKill($type, 'Invalid Password', 'Password must be between 8 to 20 characters.');
$_SESSION['cryptPassword'] = 1; require_once('cryptPassword.php');
$passHash = generatePassHash($password);
$password = $passHash;

if($type==1){
	try{
		$query = "select*from teachers where email_id = :email_id";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':email_id', $email);
		if(!$stmt->execute()) signKill($type, '', 'Some unknown error occured, please try again.');
	}catch(PDOException $ex){
		signKill($type, '', 'Some unknown error occured, please try again.');
	}
}else if($type==2){
	try{
		$query = "select*from students where email_id = :email_id";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':email_id', $email);
		if(!$stmt->execute()) signKill($type, '', 'Some unknown error occured, please try again.');
	}catch(PDOException $ex){
		signKill($type, '', 'Some unknown error occured, please try again.');
	}
}else{
	die(header('Location:error.php'));
}

$rowCount = $stmt->rowCount();
if($rowCount>0){
	signKill($type, 'Sign Up Failed', 'This Email Id is already registered!.');
}else if($rowCount==0){ //on successful data
	if($type==2){ //verify if roll is already registered
		try{
			$query = "select*from students where student_id = :student_id";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
			if(!$stmt->execute()) die(header('Location:error.php'));
			$rowCount = $stmt->rowCount();
			if($rowCount!=0) signKill($type, 'Sign Up Failed!', 'This Roll Number is already registered.');
		}catch(PDOException $ex){
			signKill($type, '', 'Some Unknown Error Occured, please try again!');
		}
	}
	session_unset();
	session_destroy();
	session_start();
	if($type==2) $_SESSION['roll']=$roll;
	$_SESSION['accountType'] = $type;
	$_SESSION['email'] = $email;
	$_SESSION['password'] = $password;
	$_SESSION['fname'] = $fname;
	$_SESSION['lname'] = $lname;
	$otp = mt_rand(100000, 999999);
	$_SESSION['otp'] = $otp;
	
	$subject='Verify OTP';
	$message='Hey '.$name.', Your OTP to signing up to Oplous is '.$otp.'. Please make sure to not share this OTP with anyone.';
	mail($email, $subject, $message);
	die(header("Location:../verify-otp.php?sign=1"));
}else{
	signKill($type, '', 'Some Unknown Error Occured, please try again!');
}

?>