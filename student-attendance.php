<!doctype html>
<html lang="en">
<head>
	<title>Attendance</title>
	<?php require_once('meta-tags.html'); ?>
	
</head>
<?php //PAGE-17
require_once('php/checkRequire.php');
verifyRequire('student-attendance');
$userId = $_SESSION['userId'];

//fetching list-1
$list1="";
try{
	//finding all the batches student attends
	$query = "select batch_id from batch_record
	where student_id = :student_id and status = 1 order by batch_id";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':student_id', $userId, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 17_101:</b> UH-OH, Something went wrong on our end!');
	$result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($result1 as $row1){
		$bid = $row1['batch_id'];
		
		//finding all the classes student attends in a batch and corresponding attendance
		$query = "select cr.class_id as class_id, concat(si.subject_code, ci.class_type) as class_code,
		si.subject_name as subject_name,
		ci.class_name as class_name, cr.total as total, cr.present as present, cr.absent as absent
		from class_record cr inner join class_info ci 
		on(cr.class_id = ci.class_id and cr.student_id = :student_id and cr.status = 1 and ci.batch_id = :batch_id)
		inner join subject_info si 
		on(ci.subject_id = si.subject_id);";
		
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':student_id', $userId, PDO::PARAM_INT);
		$stmt->bindParam(':batch_id', $bid, PDO::PARAM_INT);
		if(!$stmt->execute()) die('<b>Error 17_102:</b> UH-OH, Something went wrong on our end!');
		
		$result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$color="";
		$color1="#e9f3ff"; //odd
		$color2="#aed4ff"; //even
		$divcount=0;
		$prevdiv=-1;
		foreach($result2 as $row2){
			$bond="";
			$divcount++;
			$color = ($divcount%2==0)?$color2:$color1;
			$cid = $row2['class_id'];
			$ccode = $row2['class_code'];
			$sname = $row2['subject_name'];
			$cname = $row2['class_name'];
			$total = $row2['total'];
			$present = $row2['present'];
			$absent = $row2['absent'];
			
			if($total==0){
				$persent = 'NA';
				$color = ($divcount%2==0)?'#dddddd':'#bbbbbb';
				$list1.='
					<div class="SA ID" style="background:'.$color.'">
						<ul class="ID-list">
							<li class="ID-item">'.$ccode.' ('.$cname.')</li>
							<li class="ID-item">'.$sname.'</li>
							<li class="ID-item">Present : '.$present.'/'.$total.'</li>
							<li class="ID-item">Absent : '.$absent.'/'.$total.'</li>
							<li class="ID-item">Percent : '.$percent.'</li>
						</ul>
					</div>
				';
			}else{
				$percent = round(($present/$total)*100, 2);
				if($percent<75){
					$bond = (($divcount-$prevdiv)==1)?'border:5px solid #ff1510; border-top:none;':'border:5px solid #ff1510';
					$prevdiv = $divcount;
				}
				
				$list1 .= '
					<div class="SA ID" style="background:'.$color.'; '.$bond.';">
						<ul class="ID-list">
							<li class="ID-item">'.$ccode.' ('.$cname.')</li>
							<li class="ID-item">'.$sname.'</li>
							<li class="ID-item">Present : '.$present.'/'.$total.'</li>
							<li class="ID-item">Absent : '.$absent.'/'.$total.'</li>
							<li class="ID-item">Percent : '.$percent.'% <a href="roll-track.php?cid='.$cid.'"><button class="ID-item-button">Track</button></a></li>
						</ul>
					</div>
				';
			}
		}
	}
}catch(PDOException $ex){
	die('<b>Error 17_103:</b> UH-OH, Something went wrong on our end!');
}
?>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once("menu-header.php");?>
		<section class="MA TVS">
			<div class="MA-div TVS-div">
				<h3 class="MA-title TVS-title">Class Attendance</h3>
				<div class="MA-content TVS-content">
					<?php echo($list1); ?>
					<!--<div class="ID"><ul class="ID-list"><li class="ID-item">UCB008 (Lecture)</li><li class="ID-item">Applied Chemistry</li><li class="ID-item">Present : 12/23</li><li class="ID-item">Absent : 11/23</li><li class="ID-item">Percent : 63% <button>Track</button></li></ul></div>-->
				</div>
			</div>
		</section>
		<a href="net-attendance.php"><button class="NextLink">Total Attendance</button></a>
		<?php $_SESSION['common-footer']=1; require_once('common-footer.php'); ?>
	</section>
</body>
</html>