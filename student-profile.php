<!doctype html>
<html lang="en">
<head>
	<title>Profile</title>
	<?php require_once('meta-tags.html'); ?>
</head>
<?php //PAGE-19
$_SESSION['checkRequire']=1;
verifyRequire('student-profile');
$userId = $_SESSION['userId'];

//fetching general detials
try{
	$query = "select name,
	email_id from students where student_id = :student_id";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':student_id', $userId, PDO::PARAM_INT);
	if(!$stmt->execute() or $stmt->rowCount()!=1) die('<b>Error 19_101 :</b>Some Unknown Error Occured!');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$name = $result['name'];
	$email = $result['email_id'];
}catch(PDOException $ex){
	die('<b>Error 19_102 :</b>Some Unknown Error Occured!');
}

//fetching all the classes student attends
$list1="";
try{
	$query = "select distinct(br.batch_id) as batch_id, 
	bi.batch_code as batch_code,
	br.serial_no as serial_no
	from batch_record br
	inner join batch_info bi
	on(br.batch_id = bi.batch_id and br.student_id = :student_id and br.status = 1)";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':student_id', $userId, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 19_103 :</b>Some Unknown Error Occured!');
	$result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$list1.='<div class="TVS-summary"><ul style="list-style:none; text-align:left;">';
	foreach($result1 as $row1){
		$bid = $row1['batch_id'];
		$batch = $row1['batch_code'];
		$serial = $row1['serial_no'];
		$list1.='
			<li><a href="batch-kids.php?bid='.$bid.'">'.$batch.'</a> (Serial = '.$serial.')</li>
		';
	}
	$list1.='</ul></div>';
}catch(PDOException $ex){
	die('<b>Error 19_104 :</b>Some Unknown Error Occured!');
}
?>
<body>
<section class="MainContent" id="mainContent">
	<?php $_SESSION['menu-header']=1; require_once("menu-header.php");?>
	<section class="StudentProfile">
		<section class="GD TVS" style="text-align:left; ">
			<h3 class="TVS-title" style="text-align:left;">General Details</h3>
			<div class="TVS-summary">
				<p><b>Name :</b> <?php echo($name); ?></p>
				<p><b>User Id :</b> <?php echo($userId); ?></p>
				<p><b>Email Id :</b> <?php echo($email); ?></p>
				<a href="change-password.php"><button>Change Password</button></a>
			</div>
		</section><hr/>
		<div class="CD">
			<h3 class="TVS-title" style="text-align:left;">Class Details</h3>
			<?php echo($list1); ?>
		</div>
	</section>
	<?php $_SESSION['common-footer']=1; require_once('common-footer.php'); ?>
</section>


</body>
</html>
