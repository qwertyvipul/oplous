<!doctype html>
<html lang="en">
<head>
	<title>Mark Attendance</title>
	<?php require_once('meta-tags.html'); ?>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/markAllCheck.js"></script>
<style>
.RC-table{text-align:left; padding:0px; }
.RC-table tr td{padding:5px; }
</style>
</head>
<?php //PAGE-13
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=1) die(header('Location:login.php'));

//required variables
if(!isset($_GET['rollCall']) or $_GET['rollCall']!='rollCall' or !isset($_GET['year']) or !isset($_GET['bid']) or !isset($_GET['cid'])) die('<b>Error 13_101:</b> Page Breakdown!');
$year = $_GET['year'];
$bid = $_GET['bid'];
$cid = $_GET['cid'];

//check variable datatype
if(!is_numeric($bid) or !is_numeric($cid))  die('<b>Error 13_102:</b> Page Breakdown!');

//verifying permission
try{
	$query = "select teacher_id from class_info where class_id = :class_id";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	if(!$stmt->execute())  die('<b>Error 13_103:</b> Some unknown error occured!');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$userId = $result['teacher_id'];
	if($userId != $_SESSION['userId']) die('<b>Error 13_104:</b> Access Denied!');
}catch(PODException $ex){
	die('<b>Error 13_105:</b> Some unknown error occured!');
}

//fetching list
$list = "";
try{
	$query = "select br.serial_no as serial_no,
	br.student_id as student_id
	from batch_record br
	inner join class_info ci
	on(ci.batch_id = br.batch_id and ci.class_id = :class_id)
	order by serial_no;";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 13_106:</b> Some unknown error occured!');
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$color1="#e9f3ff";
	$color2="#aed4ff";
	$num=0;
	foreach($result as $row){
		$num++;
		if($num%2==0){
			$color=$color2;
		}else{
			$color=$color1;
		}
		$serial = $row['serial_no'];
		$roll = $row['student_id'];
		
		$query = "select s.name as name, cr.status as cstatus
		from students s
		inner join class_record cr
		on (cr.student_id = s.student_id and cr.class_id=:class_id and cr.student_id = :student_id);";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
		$stmt->bindParam(':student_id', $roll, PDO::PARAM_INT);
		if(!$stmt->execute()) die('<b>Error 13_107:</b> Some unknown error occured!');
		$subResult = $stmt->fetch(PDO::FETCH_ASSOC);
		$name = $subResult['name'];
		$cstatus = $subResult['cstatus'];
		
		if($cstatus==1){
			$list.='
				<tr style="background:'.$color.'">
					<td>
					<table>
						<tr><td>'.$roll.'</td></tr>
						<tr><td>'.$name.'</td></tr>
					</table>
					</td>
					<td>
					<table>
						<tr><td>'.$serial.'</td></tr>
						<tr><td>P : <input type="radio" name="'.$roll.'" value="1" checked> | A : <input type="radio" name="'.$roll.'" value="0"></td></tr>
					</table>
					</td>
				</tr>
			';
		}else{
			$list.='
				<tr style="background:'.$color.'">
					<td>
					<table>
						<tr><td>'.$roll.'</td></tr>
						<tr><td>'.$name.'</td></tr>
					</table>
					</td>
					<td>
					<table>
						<tr><td>'.$serial.'</td></tr>
						<tr><td>NA</td></tr>
					</table>
					</td>
				</tr>
			';
		}
	}
}catch(PDOException $ex){
	die('<b>Error 13_108:</b> Some unknown error occured!');
}
?>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once("menu-header.php");?>
		<section class="TVS">
			<form class="RC-form" id="rc-form" method="post" action="submit-attendance.php?cid=<?php echo($cid);?>">
				<div class="RC-info">
					<div class="RC-countDiv">
						<text>Count : </text>
						<select name="markCount">
							<option value="1">1</option>
							<option value="2">2</option>
						</select>
					</div>
					<div class="RC-markAll">
						<text>Mark All : </text>
						<select id="markAllCheck">
							<option value="present">Present</option>
							<option value="absent">Absent</option>
						</select>
					</div>
					<div style="clear:both"></div>
				</div>
				<div class="ID">
					<table class="TVS-table RC-table">
						<?php echo($list);?>
					</table>
					<button type="submit" id="rollCallSubmit">SUBMIT ATTENDANCE</button>
				</div>
			</form>
		</section>
		<?php $_SESSION['common-footer']=1; require_once('common-footer.php'); ?>
	</section>
</body>
</html>