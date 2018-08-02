<?php //PAGE-27
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=1) die(header('Location:index.php'));

if(!isset($_GET['cid'])) die('<b>Error 27_101: </b>Access Denied!');
$cid = intval($_GET['cid']);

//authenticate variables
if(!is_numeric($cid)) die('<b>Error 27_102: </b>Unknown Error!');

//verify permissions
try{
	$query = "select teacher_id from class_info where class_id = :class_id";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	if(!$stmt->execute() or $stmt->rowCount()!=1) die('<b>Error 27_103: </b>Unknown Error!');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$userId = $result['teacher_id'];
	if($userId!=$_SESSION['userId']) die('<b>Error 27_104: </b>Access Denied!');

}catch(PDOException $ex){
	die('<b>Error 27_105: </b>Unknown Error!');
}

//fetching list
$query = "select s.student_id as student_id, 
s.name as name,
br.serial_no as serial_no,
cr.total as total,
cr.present as present,
cr.absent as absent
from batch_record br
inner join class_info ci
on(ci.batch_id = br.batch_id and class_id=:class_id)
inner join class_record cr
on(ci.class_id = cr.class_id and br.student_id = cr.student_id)
inner join students s
on(s.student_id = cr.student_id)";
try{
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 27_106: </b>Unknown Error!');
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$list="";
	$color="";
	$bond="";
	$color1="#e9f3ff"; //odd
	$color2="#aed4ff"; //even
	$count=0;
	$previous=-1;
	foreach($result as $row){
		$count++;
		if($count%2==0){
			$color=$color1;
		}else{
			$color=$color2;
		}
		$serial = $row['serial_no'];
		$roll = $row['student_id'];
		$name = $row['name'];
		$total = $row['total'];
		$present = $row['present'];
		$absent = $row['absent'];
		if($total==0){
			$percent = 'NA';
		}else{
			$percent = ($present/$total)*100;
		}
		if($percent=='NA'){
			if($count%2==0){
				$color="#dddddd";
			}else{
				$color="#bbbbbb";
			}
			$bond="";
			$list.='<div class="VA ID" style="background:'.$color.'; '.$bond.';">
				<ul class="ID-list">
					<li class="ID-item VA-roll">('.$serial.') '.$roll.'</li>
					<li class="ID-item VA-name">'.$name.'</li>
					<li class="ID-item VA-present">Present : '.$present.'/'.$total.'</li>
					<li class="ID-item VA-absent">Absent : '.$absent.'/'.$total.'</li>
					<li class="ID-item VA-percent">NA</li>
				</ul>
			</div>
			';
			continue;
		}else if($percent<75){
			if(($count-$previous)==1){
				$bond='border:5px solid #ff1510; border-top:none;';	
			}else{
				$bond='border:5px solid #ff1510';
			}
			$previous=$count;
		}else{
			$bond='';
		}
		$percent = round($percent, 1);
		$list = $list.'
		<div class="VA ID" style="background:'.$color.'; '.$bond.';">
			<ul class="ID-list">
				<li class="ID-item VA-roll">('.$serial.') '.$roll.'</li>
				<li class="ID-item VA-name">'.$name.'</li>
				<li class="ID-item VA-present">Present : '.$present.'/'.$total.'</li>
				<li class="ID-item VA-absent">Absent : '.$absent.'/'.$total.'</li>
				<li class="ID-item VA-percent"><pre>'.$percent.'% <a href="track-student.php?cid='.$cid.'&roll='.$roll.'"><button class="VA-track ID-item-button">Track</button></a></pre></li>
			</ul>
		</div>
		';
	}
}catch(PDOException $ex){
	die('<b>Error 27_107: </b>Unknown Error!');
}
?>
<!doctype html>
<html lang="en">
<head>
	<title>View Attendance</title>
	<?php require_once('meta-tags.html'); ?>
</head>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once('menu-header.php');?>
		<section class="ViewAttendance TVS">
			<div class="VA-div TVS-div">
				<h3 class="VA-title TVS-title">View Attendance</h3>
				<div class="VA-summary TVS-summary">
					<p class="VA-year TVS-info">Year : 1</p>
					<p class="VA-batch TVS-info">Batch : Group-4</p>
					<p class="VA-year TVS-info">Class : UCB008L</p>
					<a href="advance-analysis.php?cid=<?php echo($cid);?>"><button style=" background:#81ff11; padding:5px; ">Advance Analysis</button></a>
				</div><hr/>
				<!--<div><ul style="list-style:none"><li>101610096</li><li>Vipul Sharma</li><li>Present : 12/23</li><li>Absent : 11/23</li><li>63% <button>Track</button></li></ul></div><hr/>-->
				<div class="VA-content TVS-content">
					<?php echo($list); ?>
				</div>
			</div>
		</section>
		<?php $_SESSION['common-footer']=1; require_once('common-footer.php'); ?>
	</section>
</body>
</html>