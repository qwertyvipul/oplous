<!doctype html>
<html lang="en">
<head>
	<title>View Attendance</title>
	<?php require_once('meta-tags.html'); ?>
</head><?php //PAGE-30
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=1) die(header('Location:index.php'));

//required variables
if(!isset($_GET['cid'])) die('<b>Error 30_101:</b> Page Breakdown!');
$cid = intval($_GET['cid']);

//verifying permissions
$query = "select ci.teacher_id as user_id, bi.year as year, bi.batch_code as batch, concat(si.subject_code, ci.class_type) as class,
ci.class_name as class_name
from class_info ci
inner join batch_info bi
on(ci.batch_id = bi.batch_id and ci.class_id = :class_id)
inner join subject_info si
on(si.subject_id = ci.subject_id)";
try{
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	if(!$stmt->execute() or $stmt->rowCount()!=1) die('<b>Error 30_102:</b> Unknown Error!');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$userId = $result['user_id'];
	if($userId != $_SESSION['userId']) die('<b>Error 30_103:</b> Access Denied!');
	$year = $result['year'];
	$batch = $result['batch'];
	$class = $result['class'];
	$className = $result['class_name'];
	
}catch(PDOException $ex){
	die('<b>Error 30_104:</b> Unknown Error!');
}

//fetching list
try{
	$query = "select atd_id, count, date_time from atd_info where class_id = :class_id order by atd_id desc;";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 30_105:</b> Unknown Error!');
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$color1="#e9f3ff";
	$color2="#aed4ff";
	$color='';
	$numcount=0;
	$list="";
	foreach($result as $row){
		$numcount++;
		if($numcount%2==0){
			$color=$color2;
		}else{
			$color=$color1;
		}
		$aid = $row['atd_id'];
		$count = $row['count'];
		$timeStamp = $row['date_time'];
		$netTime = date_create_from_format('Y-m-d H:i:s', $timeStamp);
		$time = date_format($netTime, 'g:i A');
		$date = date_format($netTime, 'F d, Y');
		
		$query = "select count(student_id) as total from atd_log where atd_id = :atd_id";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':atd_id', $aid, PDO::PARAM_INT);
		if(!$stmt->execute()) die('<b>Error 30_106:</b> Unknown Error!');
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$total = $result['total'];
		
		$query = "select count(student_id) as present from atd_log where atd_id = :atd_id and status=1";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':atd_id', $aid, PDO::PARAM_INT);
		if(!$stmt->execute()) die('<b>Error 30_107:</b> Unknown Error!');
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$present = $result['present'];
		
		$query = "select count(student_id) as absent from atd_log where atd_id = :atd_id and status=0";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':atd_id', $aid, PDO::PARAM_INT);
		if(!$stmt->execute()) die('<b>Error 30_108:</b> Unknown Error!');
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$absent = $result['absent'];
		
		$list = $list.'
			<div class="AA ID" style="background:'.$color.'">
				<ul class="ID-list">
					<li class="ID-item">'.$date.'</li>
					<li class="ID-item">Sunday ('.$time.')</li>
					<li class="ID-item">Present : '.$present.'/'.$total.'</li>
					<li class="ID-item"><pre>Count = '.$count.' <a href="view-summary.php?aid='.$aid.'"><button class="ID-item-button">View</button></a></pre></li>
				</ul>
			</div>
		';
	}
}catch(PDOException $ex){
	die('<b>Error 30_109:</b> Unknown Error!');
}
?>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once('menu-header.php');?>
		<section class="TVS">
			<div class="TVS-div">
				<h3 class="TVS-title">Advance Analysis</h3>
				<div class="TVS-summary">
					<p class="AA-year"><b>Year :</b> <?php echo($year); ?></p>
					<p class="AA-batch"><b>Batch :</b> <?php echo($batch); ?></p>
					<p class="AA-class"><b>Class :</b> <?php echo($class); ?></p>
				</div><hr/>
				<div class="TVS-content">
					<?php echo($list); ?>
				</div>
			</div>
		</section>
	<?php $_SESSION['common-footer']=1; require_once('common-footer.php'); ?>
	</section>
</body>
</html>