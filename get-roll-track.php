<?php //PAGE-55
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=2) die(header('Location:index.php'));
$userId = $_SESSION['userId'];

//required variables
if(!isset($_GET['cid']) or !is_numeric($_GET['cid']) or !isset($_GET['offset']) or !is_numeric($_GET['offset'])) die('<b>Error 55_101:</b> Page Breakdown!');
$cid = intval($_GET['cid']);
$offset = intval($_GET['offset']);

//verifying viewing permission
$list='';
try{
	$query = "select * from class_record where student_id = :student_id and class_id = :class_id and status = 1";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	$stmt->bindParam(':student_id', $userId, PDO::PARAM_INT);
	if(!$stmt->execute() or $stmt->rowCount()!=1) die('<b>Error 55_102: </b>Unknown Error');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	if($userId!=$result['student_id']) die('<b>Error 55_103: </b>Access Denied');
	
	//fetching all the attendance of the student
	$query = "select ai.atd_id as atd_id, 
	ai.date_time as time_stamp, 
	ai.count as count, 
	al.status as status
	from atd_info ai
	inner join atd_log al
	on(ai.atd_id = al.atd_id and ai.class_id = :class_id and al.student_id = :student_id) limit :limit, 20";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	$stmt->bindParam(':student_id', $userId, PDO::PARAM_INT);
	$stmt->bindParam(':limit', $offset, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 55_105: </b>Unknown Error');
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$list="";
	$color1="#e9f3ff";
	$color2="#aed4ff";
	$color='';
	$divcount=0;
	foreach($result as $row){
		$divcount++;
		$color = ($divcount%2==0)?$color2:$color1;
		$aid = $row['atd_id'];
		$timeStamp = $row['time_stamp'];
		$count = $row['count'];
		$status = $row['status'];
		$netTime = date_create_from_format('Y-m-d H:i:s', $timeStamp);
		$time = date_format($netTime, 'g:i A');
		$date = date_format($netTime, 'd M, Y');
		if($status==1){
			$astatus = 'Present';
		}else if($status==0){
			$astatus = 'Absent';
		}else{
			$astatus = 'NA';
			$color = ($divcount%2==0)?'#dddddd':'#eeeeee';
			$list .= '
				<div class="TS ID" style="background:'.$color.'">
					<ul class="ID-list">
						<li class="ID-item TS-date">'.$date.'</li>
						<li class="ID-item TS-time">'.$time.'</li>
						<li class="ID-item">'.$count.' (<span>'.$astatus.'</span>)</li>
					</ul>
				</div>
			';
			continue;
		}
		$list .= '
			<div class="TS ID" style="background:'.$color.'">
				<ul class="ID-list">
					<li class="ID-item TS-date">'.$date.'</li>
					<li class="ID-item TS-time">'.$time.'</li>
					<li class="ID-item"><pre>Count = '.$count.' (<span>'.$astatus.'</span>)</pre></li>
				</ul>
			</div>
		';
	}
	echo($list);
}catch(PDOException $ex){
	die($ex);
	die('<b>Error 55_106: </b>Some unknown error occured!');
}
?>