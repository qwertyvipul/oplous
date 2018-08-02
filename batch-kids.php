<!doctype html>
<html lang="en">
<head>
	<title>Profile</title>
	<?php require_once('meta-tags.html'); ?>
<style>
.BD{text-align:left;}
.BD-title{padding:15px 10px; }
.BD-div{padding:15px 10px; }
.BD-roll{padding:5px; }
</style>	
</head>
<?php //PAGE-45

session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus==0) die(header('Location:index.php'));

//required variables
if(!isset($_GET['bid'])) die('<b>Error 45_101:</b> Some unknown error occured!');
if(!is_numeric($_GET['bid']))  die('<b>Error 45_102:</b> Some unknown error occured!');

$bid = intval($_GET['bid']);

//fetching general details
try{
	$query = "select batch_code, year from batch_info where batch_id = :batch_id";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam('batch_id', $bid, PDO::PARAM_INT);
	if(!$stmt->execute() or $stmt->rowCount()!=1) die('<b>Error 45_103:</b> Some unknown error occured!');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$bcode = $result['batch_code'];
	$year = $result['year'];
	
	$query = "select batch_id, batch_code from batch_info where parent_id = :parent_id";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam('parent_id', $bid, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 45_104:</b> Some unknown error occured!');
	if($stmt->rowCount()==0){
		$sublist = 'None';
	}else{
		$sublist='( ';
		$result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$comma = '';
		foreach($result1 as $row1){
			$cbid = $row1['batch_id'];
			$cbcode = $row1['batch_code'];
			$cbcode = '<a href="./batch-kids.php?bid='.$cbid.'">'.$cbcode.'</a>';
			$sublist.=$comma.$cbcode;
			$comma = ', ';
		}
		$sublist.=' )';
	}
	
	
	//fetching list of students
	$list2='';
	$query = "select br.student_id as student_id,
	s.name,
	br.serial_no as serial_no,
	br.status as status
	from batch_record br
	inner join students s
	on(br.student_id = s.student_id and br.batch_id = :batch_id) order by serial_no";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':batch_id', $bid, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 45_105 :</b>Some Unknown Error Occured!');
	$result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$color="";
	$color1="#e9f3ff"; //odd
	$color2="#aed4ff"; //even
	$count=0;
	foreach($result2 as $row2){
		$count++;
		if($count%2==0){
			$color = $color2;
		}else{
			$color = $color1;
		}
		$studentId = $row2['student_id'];
		$studentName = $row2['name'];
		$serial = $row2['serial_no'];
		$bstatus = $row2['status'];
		
		if($bstatus==0){
			if($count%2==0){
				$color = '#bbbbbb';
			}else{
				$color = '#dddddd';
			}
			
			$list2.='<div class="BD-div" style="background:'.$color.'">
			<p class="BD-item BD-roll">('.$serial.') '.$studentId.'</p>
			<p class="BD-item BD-name">'.$studentName.'</p></div>';
			continue;
		}
		
		$list2.='<div class="BD-div" style="background:'.$color.'"><p>('.$serial.') '.$studentId.'</p>
			<p>'.$studentName.'</p></div>';
	}
	
}catch(PDOException $ex){
	die('<b>Error 45_106:</b> Some unknown error occured!');
}
?>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once("menu-header.php");?>
		<section class="StudentProfile">
			<section class="TVS" style="text-align:left; ">
				<h3 class="TVS-title" style="text-align:left;">Batch Details</h3>
				<div class="TVS-summary">
					<p><b>Name :</b> <?php echo($bcode); ?></p>
					<p><b>Year :</b> <?php echo($year); ?></p>
					<p><b>GR/CR :</b> NA </p>
					<p><b>Sub Group(s):</b> <?php echo($sublist); ?></p>
				</div>
			</section><hr/>
			<div class="BD">
				<h3 class="TVS-title" style="text-align:left;">All Students</h3>
				<?php echo($list2); ?>
			</div>
		</section>
		<?php $_SESSION['common-footer']=1; require_once('common-footer.php'); ?>
	</section>
</body>
</html>
