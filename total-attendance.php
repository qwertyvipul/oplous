<?php  //PAGE-24
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=1) die(header('Location:index.php'));

//required variables
if(!isset($_GET['year']) or !isset($_GET['bid']) or !isset($_GET['sid'])) die('<b>Error 24_101:</b>Page Breakdown!');
if(!is_numeric($_GET['year']) or !is_numeric($_GET['bid']) or !is_numeric($_GET['sid'])) die('<b>Error 24_102:</b>Page Breakdown!');
$year = intval($_GET['year']);
$bid = intval($_GET['bid']);
$sid = intval($_GET['sid']);
$userId = $_SESSION['userId'];

//verify view permissions
try{
	$query = "select*from class_info where subject_id = :subject_id and batch_id = :batch_id and teacher_id = :teacher_id";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':subject_id', $sid, PDO::PARAM_INT);
	$stmt->bindParam(':batch_id', $bid, PDO::PARAM_INT);
	$stmt->bindParam(':teacher_id', $userId, PDO::PARAM_INT);
	if(!$stmt->execute() or $stmt->rowCount()<1) die('<b>Error 24_103:</b>Access Denied!');
}catch(PDOException $ex){
	die('<b>Error 24_104:</b>Unknown Error!');
}

//fetching list
$query1 = "select parent_id from batch_info where batch_id = :batch_id";
$query2 = "select serial_no, student_id, status from batch_record where batch_id = :batch_id";
try{
	$stmt1 = $pdo->prepare($query1);
	$stmt2 = $pdo->prepare($query2);
	$stmt1->bindParam(':batch_id', $bid, PDO::PARAM_INT);
	$stmt2->bindParam(':batch_id', $bid, PDO::PARAM_INT);
	if(!$stmt1->execute()) die('<b>Error 24_105:</b>Unknown Error!');
	if(!$stmt2->execute()) die('<b>Error 24_106:</b>Unknown Error!');
	$result = $stmt1->fetch(PDO::FETCH_ASSOC);
	$parent = $result['parent_id'];
	$result1 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
	$list="";
	$color1="#e9f3ff";
	$color2="#aed4ff";
	$color='';
	$bond='';
	$previous=-1;
	$numcount=0;
	foreach($result1 as $row1){
		$numcount++;
		if($numcount%2==0){
			$color=$color2;
		}else{
			$color=$color1;
		}
		$roll = $row1['student_id'];
		$bstatus = $row1['status'];
		$serial = $row1['serial_no'];
		$total=0;
		$present=0;
		$absent=0;
		
		if(is_null($parent)){
			$parent = $bid;
		}
		
		//select all classes for this course from parent
		$query = "select class_id from class_info where batch_id = :batch_id and subject_id = :subject_id";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':batch_id', $parent, PDO::PARAM_INT);
		$stmt->bindParam(':subject_id', $sid, PDO::PARAM_INT);
		if(!$stmt->execute()) die('<b>Error 24_107:</b>Unknown Error!');
		$result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($result2 as $row2){
			$cid = $row2['class_id'];
			$query = "select cr.total as total, cr.present as present, cr.absent as absent, cr.status as cstatus
			from class_record cr where cr.class_id = :class_id and cr.student_id = :student_id";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
			$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
			if(!$stmt->execute()) die('<b>Error 24_108:</b>Unknown Error!');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$cstatus = $result['cstatus'];
			if($cstatus==0){
				continue;
			}
			$total+=$result['total'];
			$present+=$result['present'];
			$absent+=$result['absent'];
		}
		
		//select all classes from the child
		$query = "select ci.class_id as class_id
		from class_info ci
		inner join batch_info bi
		on(ci.batch_id = bi.batch_id and bi.parent_id = :parent_id and ci.subject_id = :subject_id)";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':parent_id', $parent, PDO::PARAM_INT);
		$stmt->bindParam(':subject_id', $sid, PDO::PARAM_INT);
		if(!$stmt->execute()) die('<b>Error 24_109:</b>Unknown Error!');
		$result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($result2 as $row2){
			$cid = $row2['class_id'];
			$query = "select cr.total as total, cr.present as present, cr.absent as absent, cr.status as cstatus
			from class_record cr where cr.class_id = :class_id and cr.student_id = :student_id";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
			$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
			if(!$stmt->execute()) die('<b>Error 24_110:</b>Unknown Error!');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$cstatus = $result['cstatus'];
			if($cstatus==0){
				continue;
			}
			$total+=$result['total'];
			$present+=$result['present'];
			$absent+=$result['absent'];
		}
		
		$query = "select name from students where student_id = :student_id";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
		if(!$stmt->execute()) die('<b>Error 24_111:</b>Unknown Error!');
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$name = $result['name'];
		
		if($total==0){
			$percent = 'NA';
			if($numcount%2==0){
				$color='#eeeeee';
			}else{
				$color='#dddddd';
			}
			$bond='';
			$list = $list.'
				<div class="TA ID" style="background:'.$color.'; '.$bond.'">
					<ul class="ID-list">
						<li class="ID-item TA-roll">('.$serial.') '.$roll.'</li>
						<li class="ID-item VA-name">'.$name.'</li>
						<li class="ID-item VA-present">Present : '.$present.'/'.$total.'</li>
						<li class="ID-item VA-absent">Absent : '.$absent.'/'.$total.'</li>
						<li class="ID-item VA-percent">NA</li>
					</ul>
				</div>
			';
			continue;
		}else{
			$percent = round(($present/$total)*100, 1);
			if($percent<75){
				if($numcount-$previous==1){
					$bond='border:5px solid #ff1510; border-top:none;';
				}else{
					$bond='border:5px solid #ff1510;';
				}
				$previous=$numcount;
			}else{
				$bond='';
			}
		}
		
		$list = $list.'
		<div class="TA ID" style="background:'.$color.'; '.$bond.'">
			<ul class="ID-list">
				<li class="ID-item TA-roll">('.$serial.') '.$roll.'</li>
				<li class="ID-item VA-name">'.$name.'</li>
				<li class="ID-item VA-present">Present : '.$present.'/'.$total.'</li>
				<li class="ID-item VA-absent">Absent : '.$absent.'/'.$total.'</li>
				<li class="ID-item VA-percent">'.$percent.'%</li>
			</ul>
		</div>
		';
	}
	
}catch(PDOException $ex){
	die('<b>Error 24_112:</b>Some unknown error occured!');
}
?>
<!doctype html>
<html lang="en">
<head>
	<title>Total Attendance</title>
	<?php require_once('meta-tags.html'); ?>
	
<style>

</style>
</head>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once('menu-header.php');?>
		<section class="TA TVS">
			<div class="TA-div TVS-div">
				<h3 class="TA-title TVS-title">Total Attendance</h3>
				<div class="TA-summary TVS-summary">
					<p class="TA-year TVS-info">Year : 1</p>
					<p class="TA-batch TVS-info">Batch : Group-4</p>
					<p class="VA-subject TVS-info">Course : UCB008</p>
				</div><hr/>
				<!--<div><ul style="list-style:none"><li>101610096</li><li>Vipul Sharma</li><li>Present : 12/23</li><li>Absent : 11/23</li><li>63% <button>Track</button></li></ul></div><hr/>-->
				<div class="TA-content TVS-content">
					<?php echo($list); ?>
				</div>
			</div>
		</section>
	<?php $_SESSION['common-footer']=1; require_once('common-footer.php'); ?>
	</section>
</body>
</html>