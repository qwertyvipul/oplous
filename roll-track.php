<!doctype html>
<html lang="en">
<head>
	<title>Track Student</title>
	<?php require_once('meta-tags.html'); ?>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/changeStatus.js"></script>
</head><?php //PAGE-54
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus!=2) die(header('Location:index.php'));
$userId = $_SESSION['userId'];

//required variables
if(!isset($_GET['cid']) or !is_numeric($_GET['cid'])) die('<b>Error 54_101:</b> Page Breakdown!');
$cid = intval($_GET['cid']);

//verifying viewing permission
try{
	$query = "select * from class_record where student_id = :student_id and class_id = :class_id and status = 1";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	$stmt->bindParam(':student_id', $userId, PDO::PARAM_INT);
	if(!$stmt->execute() or $stmt->rowCount()!=1) die('<b>Error 54_102: </b>Unknown Error');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	if($userId!=$result['student_id']) die('<b>Error 54_103: </b>Access Denied');
	
	//general details of the class
	$query = "select concat(si.subject_code, ci.class_type) as class_code,
	ci.class_name as class_name,
	si.subject_name as subject_name,
	t.name as teacher_name
	from class_info ci inner join subject_info si
	on(ci.subject_id = si.subject_id and ci.class_id = :class_id)
	inner join teachers t
	on(ci.teacher_id = t.teacher_id)";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
	if(!$stmt->execute() or $stmt->rowCount()!=1) die('<b>Error 54_104: </b>Unknown Error');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$ccode = $result['class_code'];
	$cname = $result['class_name'];
	$subname = $result['subject_name'];
	$teacher = $result['teacher_name'];
}catch(PDOException $ex){
	die('<b>Error 54_105: </b>Some unknown error occured!');
}
?>
<body>
<script>
$(document).ready(function(){
	var flag=0;
	$('#trackLoadMore').html('Loading...');
	$.ajax({
		type: "GET",
		url:"get-roll-track.php",
		data:'offset='+flag+'&cid='+<?php echo($cid);?>,
		cache:false,
		success:function(data){
			$('#trackLoadMore').html('Load More');
			$('#rollTrack').append(data);
			flag+=20;
		}
	});
	
	$('#trackLoadMore').click(function(){
		$('#trackLoadMore').html('Loading...');
		$.ajax({
			type: "GET",
			url:"get-roll-track.php",
			data:'offset='+flag+'&cid='+<?php echo($cid);?>,
			cache:false,
			success:function(data){
				if(data==''){
					$('#trackLoadMore').css('display', 'none');
				}else{
					$('#trackLoadMore').html('Load More');
					$('#rollTrack').append(data);
					flag+=20;
				}
			}
		});
	});
});

</script>

	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once('menu-header.php');?>
		<section class="TVS TS">
			<div class="TVS-div">
				<h3 class="TVS-title">Track Attendance</h3>
				<div class="TVS-summary" id="ts-summary">
					<p class="TVS-info TS-serial"><b>Class :</b> <?php echo($ccode.' ('.$cname.')'); ?></p>
					<p class="TVS-info TS-roll"><b>Subject :</b> <?php echo($subname); ?></p>
					<p class="TVS-info TS-name"><b>Teacher :</b> <?php echo($teacher); ?></p>
				</div><hr/>
				<div class="TVS-content" id="rollTrack"></div>
			</div>
		</section>
	<button class="NextLink" id="trackLoadMore">Load More</button>
	<?php $_SESSION['common-footer']=1; require_once('common-footer.php'); ?>
	</section>
</body>
</html>