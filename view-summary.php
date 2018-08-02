<?php //PAGE-28
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=1) die(header('Location:index.php'));

//required variables
if(!isset($_GET['aid'])) die('<b>Error 28_101:</b> Page Breakdown!');
$aid = intval($_GET['aid']);
if(!is_numeric($aid)) die('<b>Error 28_102:</b> Unknown Error!');

//verifying permission
try{
	$query = "select ci.teacher_id as teacher_id, 
	ci.class_id as class_id, 
	ci.batch_id as batch_id, 
	ai.date_time as date_time,
	ai.count as count
	from class_info ci
	inner join atd_info ai
	on(ai.class_id = ci.class_id and ai.atd_id = :atd_id)";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':atd_id', $aid, PDO::PARAM_INT);
	if(!$stmt->execute() or $stmt->rowCount()!=1) die('<b>Error 28_103:</b> Unknown Error!');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$userId = $result['teacher_id'];
	if($userId!=$_SESSION['userId']) die('<b>Error 28_104:</b> Access Denied!');
	$cid = $result['class_id'];
	$bid = $result['batch_id'];
	$timeStamp = $result['date_time'];
	$count = $result['count'];
}catch(PDOException $ex){
	die('<b>Error 28_105:</b> Unknown Error!');
}
$netTime = date_create_from_format('Y-m-d H:i:s', $timeStamp);
$time = date_format($netTime, 'g:i A');
$date = date_format($netTime, 'M-d-Y');

//fetching list
$list = "";
try{
	$query = "select serial_no, student_id from batch_record where batch_id = :batch_id order by serial_no";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':batch_id', $bid, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 28_106:</b> Unknown Error!');
	$result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	//color code
	$color1="#e9f3ff";
	$color2="#aed4ff";
	$color='';
	$numcount=0;
	foreach($result1 as $row1){
		$serial = $row1['serial_no'];
		$roll = $row1['student_id'];
		$numcount++;
		if($numcount%2==0){
			$color=$color2;
		}else{
			$color=$color1;
		}
		$query = "select s.name as name,
		cr.total as total,
		cr.present as present,
		cr.absent as absent,
		cr.status as cstatus
		from class_info ci
		inner join class_record cr
		on(ci.class_id = cr.class_id and ci.class_id=:class_id)
		inner join students s
		on(cr.student_id = s.student_id and cr.student_id = :student_id)";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
		$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
		if(!$stmt->execute()) die('<b>Error 28_107:</b> Unknown Error!');
		$result = $stmt->fetch();
		$name = $result['name'];
		$total = $result['total'];
		$present = $result['present'];
		$absent = $result['absent'];
		$cstatus = $result['cstatus'];
		if($cstatus==1){
			$query = "select status from atd_log where atd_id = :atd_id and student_id = :student_id";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':atd_id', $aid, PDO::PARAM_INT);
			$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
			if(!$stmt->execute()) die('<b>Error 28_107:</b> Unknown Error!');
			$sr1 = $stmt->fetch(PDO::FETCH_ASSOC);
			$status = $sr1['status'];
			
			if($status==1){
				$astatus='Present';
			}else if($status==0){
				$astatus='Absent';
			}else{
				die(header('Location:error.php'));
			}
			
			$list.='
				<div class="VS ID" style="background:'.$color.'">
					<ul class="ID-list">
						<li class="ID-item VS-roll">('.$serial.') '.$roll.'</li>
						<li class="ID-item VS-name">'.$name.'</li>
						<li class="ID-item VS-status"><pre><span id="'.$roll.'-status">'.$astatus.'</span>	<button class="ID-item-button" onclick="editStatus('.$aid.', '.$roll.')">Edit</button></pre></li>
					</ul>
				</div>
			';
		}else{
			if($numcount%2==0){
				$color='#dddddd';
			}else{
				$color='#eeeeee';
			}
			$list.='
				<div class="VS ID" style="background:'.$color.'">
					<ul class="ID-list">
						<li class="ID-item VS-roll">('.$serial.') '.$roll.'</li>
						<li class="ID-item VS-name">'.$name.'</li>
						<li class="ID-item VS-status">NA</li>
					</ul>
				</div>
			';
		}
	}
	
}catch(PDOException $ex){
	die('<b>Error 28_108:</b> Unknown Error!');
}

//fetching general info
try{
	$query = "select bi.year as year, 
	bi.batch_code as batch_code, 
	concat(si.subject_code, ci.class_type) as class_code
	from batch_info bi
	inner join class_info ci
	on(ci.batch_id = ci.batch_id and ci.class_id = :class_id)
	inner join subject_info si
	on(ci.subject_id = si.subject_id)";
	
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 28_109:</b> Unknown Error!');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$year = $result['year'];
	$batch = $result['batch_code'];
	$class = $result['class_code'];
}catch(PDOException $ex){
	die('<b>Error 28_110:</b> Unknown Error!');
}

?>
<!doctype html>
<html lang="en">
<head>
	<title>View Summary</title>
	<?php require_once('meta-tags.html'); ?>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/vs-edit-popup.js"></script>
<style>
.VSP{background:#e6eef2; margin:0 auto; display:none;}
.VSP-head{background:#87b2c2;  padding:5px;}
.VSP-body{ padding:5px; }
.VSP-title{float:left; color:#ffffff; }
.VSP-close{float:right; padding:2px 4px; background:#ff4a44; border:none; color:#ffffff; font-weight:bold; }
.VSP footer{text-align:center; background:#ffffff; }
.VSP footer button{padding:5px; background:#ff4a44; font-weight:bold; color:#ffffff; }
</style>
</head>
<body>


<section class="VSP" id="vs-popup">
	<header class="VSP-head">
		<h3 class="VSP-title">Edit Attedance</h3>
		<button class="VSP-close" onClick="hide()">&times;</button>
		<div style="clear:both"></div>
	</header>
	<div class="VSP-body">
		<h3 class="VSP-body-title">Student Details</h3>
		<div class="VSP-details" id="vsp-details">
			<!--<span>Roll No. : </span>	<span id="vs-popup-roll">101610001</span><br/><span>Name : </span>		<span id="vsep-Name">John Doe</span><br/>span>S.No : </span>		<span id="vsep-Serial">22</span><br/>span>Total : </span>		<span id="vsep-Total">23</span><br/><span>Present : </span>		<span id="vsep-Present">12</span><br/><span>Absent : </span>		<span id="vsep-Absent">11</span><br/><span>Percent : </span>		<span id="vsep-Percent">65</span><br/><span>Current Status : </span><span id="vsep-Status">P</span><span> </span><button id="vsep-UpdateButton" onclick="updateStatus()"><span>CHANGE</span></button><br/>-->
		</div>
	</div>
	<footer><button onClick="hide()">CLOSE</button></footer>
	<div style="clear:both"></div>
</section>
<section class="MainContent" id="mainContent">
	<?php $_SESSION['menu-header']=1; require_once('menu-header.php');?>
	<section class="TVS">
		<div class="TVS-div">
			<h3 class="TVS-title">View Summary</h3>
			<div class="VS-summary TVS-summary">
				<p class="VS-year TVS-info"><b>Year :</b> <?php echo($year); ?></p>
				<p class="VS-batch TVS-info"><b>Batch :</b> <?php echo($batch); ?></p>
				<p class="VS-class TVS-info"><b>Class :</b> <?php echo($class); ?></p>
				<p class="VS-date TVS-info"><b>Date :</b> <?php echo($date); ?></p>
				<p class="VS-time TVS-info"><b>Time :</b> <?php echo($time); ?></p>
				<p class="VS-count TVS-info"><b>Count :</b> <?php echo($count); ?></p>
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