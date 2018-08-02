<?php //PAGE-25
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=1) die(header('Location:index.php'));

//required variables
if(!isset($_GET['cid']) or !isset($_GET['roll'])) die('<b>Error 25_101:</b> Page Breakdown!');
if(!is_numeric($_GET['cid']) or !is_numeric($_GET['roll'])) die('<b>Error 25_102:</b> Page Breakdown!');
$cid = intval($_GET['cid']);
$roll = intval($_GET['roll']);

//verifying viewing permission
$query = "select ci.teacher_id as user_id from class_info ci
inner join class_record cr
on(cr.class_id = ci.class_id and ci.class_id = :class_id and cr.student_id = :student_id)";
try{
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
	if(!$stmt->execute() or $stmt->rowCount()!=1) die('<b>Error 25_103: </b>Unknown Error');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$userId = $result['user_id'];
	if($userId!=$_SESSION['userId']) die('<b>Error 25_104: </b>Access Denied');
	
	//fetching list
	$query = "select s.name as name,
	br.serial_no as serial_no
	from students s
	inner join batch_record br
	on(br.student_id = s.student_id and br.student_id = :student_id)
	inner join class_info ci
	on(br.batch_id = ci.batch_id and ci.class_id = :class_id)";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	if(!$stmt->execute() or $stmt->rowCount()!=1) die('<b>Error 25_105: </b>Unknown Error');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$name = $result['name'];
	$serial = $result['serial_no'];
	
	$query = "select ai.atd_id as atd_id, 
	ai.date_time as time_stamp, 
	ai.count as count, 
	al.status as status
	from atd_info ai
	inner join atd_log al
	on(ai.atd_id = al.atd_id and ai.class_id = :class_id and al.student_id = :student_id);";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 25_106: </b>Unknown Error');
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$list="";
	$color1="#e9f3ff";
	$color2="#aed4ff";
	$color='';
	$numcount=0;
	foreach($result as $row){
		$numcount++;
		if($numcount%2==0){
			$color = $color2;
		}else{
			$color = $color1;
		}
		$aid = $row['atd_id'];
		$timeStamp = $row['time_stamp'];
		$count = $row['count'];
		$status = $row['status'];
		$netTime = date_create_from_format('Y-m-d H:i:s', $timeStamp);
		$time = date_format($netTime, 'g:i A');
		$date = date_format($netTime, 'd M, Y');
		if($status==1){
			$astatus = 'P';
		}else if($status==0){
			$astatus = 'A';
		}else{
			$astatus = 'NA';
			if($numcount%2==0){
				$color='#dddddd';
			}else{
				$color='#eeeeee';
			}
			$list = $list.'
				<div class="TS ID" style="background:'.$color.'">
					<ul class="ID-list">
						<li class="ID-item TS-date">'.$date.'</li>
						<li class="ID-item TS-time">'.$time.'</li>
						<li class="ID-item">('.$count.') <span id="'.$roll.'-'.$aid.'-status">'.$astatus.'</span></li>
					</ul>
				</div>
			';
			continue;
		}
		$list = $list.'
			<div class="TS ID" style="background:'.$color.'">
				<ul class="ID-list">
					<li class="ID-item TS-date">'.$date.'</li>
					<li class="ID-item TS-time">'.$time.'</li>
					<li class="ID-item"><pre>Count = '.$count.' (<span id="'.$roll.'-'.$aid.'-status">'.$astatus.'</span>) <a id="'.$roll.'-'.$aid.'-button"><button class="ID-item-button" onclick="changeStatus('.$aid.', '.$roll.', '.$status.')">Change</button></a></pre></li>
				</ul>
			</div>
		';
	}
	
}catch(PDOException $ex){
	die('<b>Error 25_107: </b>Some unknown error occured!');
}
?>
<!doctype html>
<html lang="en">
<head>
	<title>Track Student</title>
	<?php require_once('meta-tags.html'); ?>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/changeStatus.js"></script>
</head>
<body>

	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once('menu-header.php');?>
		<section class="TVS TS">
			<div class="TVS-div">
				<h3 class="TVS-title">Track Attendance</h3>
				<div class="TVS-summary" id="ts-summary">
					<p class="TVS-info TS-serial"><b>Serial :</b> <?php echo($serial); ?></p>
					<p class="TVS-info TS-roll"><b>Roll :</b> <?php echo($roll); ?></p>
					<p class="TVS-info TS-name"><b>Name :</b> <?php echo($name); ?></p>
					<div class="TVS-info TS-calculations" id="ts-calculations">
						<script>newDetails(<?php echo($aid.', '.$roll); ?>)</script>
						<!--<p class="TS-total">Total : 23</p>
						<p class="TS-present">Present : 12</p>
						<p class="TS-absent">Absent : 11</p>
						<p class="TS-percent">Percent : 63</p>-->
					</div>
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