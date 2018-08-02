<!doctype html>
<html lang="en">
<head>
	<title>Total Attendance</title>
	<?php require_once('meta-tags.html'); ?>
	
</head>
<?php //PAGE-53

session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);

if($loginStatus!=2) die(header('Location:login.php'));
$userId = $_SESSION['userId'];

//fetching list-1
$list1="";
try{
	//finding all the batches student attends
	$query = "select batch_id from batch_record
	where student_id = :student_id and status = 1 order by batch_id";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':student_id', $userId, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 53_101:</b> UH-OH, Something went wrong on our end!');
	$result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($result1 as $row1){
		$bid = $row1['batch_id'];
		
		//finding distinct subject student studies in a batch
		$query = "select distinct(ci.subject_id) as subject_id
		from class_info ci inner join class_record cr
		on(ci.class_id = cr.class_id and cr.student_id = :student_id and cr.status = 1 and ci.batch_id = :batch_id)";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':student_id', $userId, PDO::PARAM_INT);
		$stmt->bindParam(':batch_id', $bid, PDO::PARAM_INT);
		if(!$stmt->execute()) die('<b>Error 53_102:</b> UH-OH, Something went wrong on our end!');
		$result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$color="";
		$color1="#e9f3ff"; //odd
		$color2="#aed4ff"; //even
		$divcount=0;
		$prevdiv=-1;
		foreach($result2 as $row2){
			$divcount++; 
			$bond="";
			$color = ($divcount%2==0)?$color2:$color1;
			$subid = $row2['subject_id'];
			
			//the details corresponding to the subject id
			$query = "select subject_code, subject_name from subject_info where subject_id = :subject_id";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':subject_id', $subid, PDO::PARAM_INT);
			if(!$stmt->execute()) die('<b>Error 53_103:</b> UH-OH, Something went wrong on our end!');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$subcode = $result['subject_code'];
			$subname = $result['subject_name'];
			
			
			//for the given batch and subject find all the classes
			$query = "select cr.total as total, cr.present as present, cr.absent as absent
			from class_record cr inner join class_info ci 
			on(cr.class_id = ci.class_id and cr.student_id = :student_id and cr.status = 1 and ci.batch_id = :batch_id)
			inner join subject_info si 
			on(ci.subject_id = si.subject_id and ci.subject_id = :subject_id);";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':student_id', $userId, PDO::PARAM_INT);
			$stmt->bindParam(':batch_id', $bid, PDO::PARAM_INT);
			$stmt->bindParam(':subject_id', $subid, PDO::PARAM_INT);
			if(!$stmt->execute()) die('<b>Error 53_104:</b> UH-OH, Something went wrong on our end!');
			
			$total = 0;
			$present = 0;
			$absent = 0;
			$result3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach($result3 as $row3){
				$total+=$row3['total'];
				$present+=$row3['present'];
				$absent+=$row3['absent'];
			}
			
			
			if($total==0){
				$persent = 'NA';
				$color = ($divcount%2==0)?'#dddddd':'#bbbbbb';
				$list1.='
					<div class="SA ID" style="background:'.$color.'">
						<ul class="ID-list">
							<li class="ID-item">'.$subcode.'</li>
							<li class="ID-item">'.$subname.'</li>
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
							<li class="ID-item">'.$subcode.'</li>
							<li class="ID-item">'.$subname.'</li>
							<li class="ID-item">Present : '.$present.'/'.$total.'</li>
							<li class="ID-item">Absent : '.$absent.'/'.$total.'</li>
							<li class="ID-item">Percent : '.$percent.'%</li>
						</ul>
					</div>
				';
			
			}
		}
	}
}catch(PDOException $ex){
	die($ex);
	die('<b>Error 53_105:</b> UH-OH, Something went wrong on our end!');
}
?>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once("menu-header.php");?>
		<section class="MA TVS">
			<div class="MA-div TVS-div">
				<h3 class="MA-title TVS-title">Total Attendance</h3>
				<div class="MA-content TVS-content">
					<?php echo($list1); ?>
				</div>
			</div>
		</section>
		<a href="attendance.php"><button class="NextLink">Class Attendance</button></a>
		<?php $_SESSION['common-footer']=1; require_once('common-footer.php'); ?>
	</section>
</body>
</html>