<?php //PAGE-22
require_once('php/checkRequire.php');
verifyRequire('teacher-attendance');
$userId = intval($_SESSION['userId']);
$option1="";
$option2="";
$option3="";
$classOption1="";
$classOption2="";
$classOption3="";
$totalOption1="";
$totalOption2="";
$totalOption3="";
$notifyOption1="";
try{
	$query = "select distinct(bi.year)
	from batch_info bi
	inner join class_info ci
	on (ci.batch_id = bi.batch_id and ci.teacher_id=:userId)";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':userId', $userId);
	if(!$stmt->execute()) die('<b>Error 22_101:</b> Some unknown error occured.');
	$result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$inCode1=0;
	foreach($result1 as $row1){
		$year = intval($row1['year']);
		$value1=$year;
		$code1="O";
		$classCode1="C";
		$totalCode1="T";
		$notifyCode1="N";
		$inCode1++;
		$code1=$code1.$inCode1;
		$classCode1=$classCode1.$inCode1;
		$option1 = $option1.'<option value="'.$value1.'" id="'.$code1.'">'.$value1.'</option>';
		$classOption1 = $classOption1.'<option value="'.$value1.'" id="'.$classCode1.'">'.$value1.'</option>';
		$totalOption1 = $totalOption1.'<option value="'.$value1.'" id="'.$totalCode1.'">'.$value1.'</option>';
		$notifyOption1 = $notifyOption1.'<option value="'.$value1.'" id="'.$notifyCode1.'">'.$value1.'</option>';
		
		$query="select distinct(ci.batch_id) as batch_id
		from class_info ci
		inner join batch_info bi
		on(bi.year=:year and ci.batch_id = bi.batch_id and ci.teacher_id=:userId)";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':year', $year);
		$stmt->bindParam(':userId', $userId);
		if(!$stmt->execute()) die('<b>Error 22_102:</b> Some unknown error occured.');
		$result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$inCode2=0;
		foreach($result2 as $row2){
			$batchId = intval($row2['batch_id']);
			$value2=$batchId;
			$query="select batch_code from batch_info where batch_id = :value2";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':value2', $value2);
			if(!$stmt->execute()) die('<b>Error 22_103:</b> Some unknown error occured.');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$batchCode = $result['batch_code'];
			$code2 = $code1."_";
			$classCode2 = $classCode1."_";
			$totalCode2 = $totalCode1."_";
			$inCode2++;
			$code2 = $code2.$inCode2;
			$classCode2 = $classCode2.$inCode2;
			$totalCode2 = $totalCode2.$inCode2;
			$option2 = $option2.'<option value="'.$value2.'" class="'.$code1.' Child1" id="'.$code2.'" style="display:none">'.$batchCode.'</option>';
			$classOption2 = $classOption2.'<option value="'.$value2.'" class="'.$classCode1.' Child3" id="'.$classCode2.'" style="display:none">'.$batchCode.'</option>';
			$totalOption2 = $totalOption2.'<option value="'.$value2.'" class="'.$totalCode1.' Child5" id="'.$totalCode2.'" style="display:none">'.$batchCode.'</option>';
			
			$query = "select ci.class_id, concat(si.subject_code, ci.class_type) as class
			from class_info ci
			inner join batch_info bi
			on (ci.teacher_id=:userId and bi.year=:year and ci.batch_id=:batchId and ci.batch_id = bi.batch_id)
			inner join subject_info si
			on (ci.subject_id = si.subject_id)";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':userId', $userId);
			$stmt->bindParam(':year', $year);
			$stmt->bindParam(':batchId', $batchId);
			if(!$stmt->execute()) die('<b>Error 22_104:</b> Some unknown error occured.');
			$result3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach($result3 as $row3){
				$class = $row3['class'];
				$classId = intval($row3['class_id']);
				$value4 = $classId;
				$option3 = $option3.'<option value="'.$value4.'" class="'.$code2.' Child2" style="display:none">'.$class.'</option>';
				$classOption3 = $classOption3.'<option value="'.$value4.'" class="'.$classCode2.' Child4" style="display:none">'.$class.'</option>';
			}
			
			$query = "select distinct(si.subject_id) as subject_id
			from class_info ci
			inner join batch_info bi
			on (ci.teacher_id=:userId and bi.year=:year and ci.batch_id=:batchId and ci.batch_id = bi.batch_id)
			inner join subject_info si
			on (ci.subject_id = si.subject_id)";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':userId', $userId);
			$stmt->bindParam(':year', $year);
			$stmt->bindParam(':batchId', $batchId);
			if(!$stmt->execute()) die('<b>Error 22_105:</b> Some unknown error occured.');
			$result4 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach($result4 as $row4){
				$subid = $row4['subject_id'];
				$value4 = $subid;
				
				$query = "select subject_code from subject_info where subject_id = :subject_id";
				$stmt = $pdo->prepare($query);
				$stmt->bindParam(':subject_id', $subid, PDO::PARAM_INT);
				if(!$stmt->execute()) die('<b>Error 22_105:</b> Some unknown error occured.');
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$subcode = $result['subject_code'];
				$totalOption3 = $totalOption3.'<option value="'.$value4.'" class="'.$totalCode2.' Child6" style="display:none">'.$subcode.'</option>';
			}
		}
	}
}catch(PDOException $ex){
	die('<b>Error 22_106:</b> Some unknown error occured!');
}
?>
<!doctype html>
<html lang="en">
<head>
	<title>Mark Attendance</title>
	<?php require_once('meta-tags.html'); ?>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/dropCheck.js"></script>
</head>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once("menu-header.php");?>
		<div class="MF MV">
			<form id="markForm" action="roll-call.php" method="get">
				<h3>MARK ATTENDANCE</h3>
				<select id="mf_year" class="MV-cred" name="year">
					<option value="0">Select Year</option>
					<?php echo($option1);?> <!--<option value="1" id="O1">1</option><option value="2" id="O2">2</option><option value="3" id="O3">3</option><option value="4" id="O4">4</option>-->
				</select>
				<select id="mf_batch" class="MV-cred" name="bid" disabled>
					<option value="0">Select Batch</option>
					<?php echo($option2);?> <!--<option value="1" class="O1 Child1" id="O1_1" style="display:none">Batch 1</option><option value="2" class="O1 Child1" id="O1_2" style="display:none">Batch 2</option><option value="2" class="O2 Child1" id="O2_1" style="display:none">Batch 3</option> <option value="2" class="O2 Child1" id="O2_2" style="display:none">Batch 4</option>-->
				</select>
				<select id="mf_class" class="MV-cred" name="cid" disabled>
					<option value="0">Select Class</option>
					<?php echo($option3);?> <!--<option value="1" class="O1_1 Child2" style="display:none">Subject 1 Tutorial</option><option value="2" class="O2_1 Child2" style="display:none">Subject 2 Practical</option>-->
				</select>
				<button class="MV-cred MV-cred-button" id="mf-button" type="submit" name="rollCall" value="rollCall" disabled>MARK ATTENDANCE</button><p/>
			</form>
		</div>
		<div class="VF MV">
			<form id="classView" action="view-attendance.php" method="get">
				<h3>VIEW CLASS ATTENDANCE</h3>
				<select id="cv_year" class="MV-cred" name="year">
					<option value="0">Select Year</option>
					<?php echo($classOption1);?>
				</select>
				<select id="cv_batch" class="MV-cred" name="bid" disabled>
					<option value="0">Select Batch</option>
					<?php echo($classOption2);?>
				</select>
				<select id="cv_class" class="MV-cred" name="cid" disabled>
					<option value="0">Select Class</option>
					<?php echo($classOption3);?>
				</select>
				<button class="MV-cred MV-cred-button" id="classViewButton" type="submit" name="classView" value="classView" disabled>VIEW ATTENDANCE</button><p/>
			</form>
		</div>
		<div class="TF MV">
			<form id="totalView" action="total-attendance.php" method="get">
				<h3>VIEW TOTAL ATTENDANCE</h3>
				<select id="tv_year" class="MV-cred" name="year">
					<option value="0">Select Year</option>
					<?php echo($totalOption1);?>
				</select>
				<select id="tv_batch" class="MV-cred" name="bid" disabled>
					<option value="0">Select Batch</option>
					<?php echo($totalOption2);?>
				</select>
				<select id="tv_subject" class="MV-cred" name="sid" disabled>
					<option value="0">Select Subject</option>
					<?php echo($totalOption3);?>
				</select>
				<button class="MV-cred MV-cred-button" id="totalViewButton" type="submit" name="totalView" value="totalView" disabled>TOTAL ATTENDANCE</button><p/>
			</form>
		</div><hr/>
		<section class="TVS">
			<h3 class="TVS-title">Beyond Attendance</h3>
			<div class="NF MV">
				<form id="notify" action="custom-notifications.php" method="get">
					<h3>SEND CUSTOM NOTIFICATIONS</h3>
					<select id="notify_year" class="MV-cred" name="year">
						<option value="0">Select Year</option>
						<?php echo($notifyOption1);?>
					</select>
					<button class="MV-cred MV-cred-button" id="notifyButton" type="submit" name="notify" value="notify" disabled>SEND NOTIFICATIONS</button><p/>
				</form>
			</div>
		</section>
	<?php $_SESSION['common-footer']=1; require_once('common-footer.php'); ?>
	</section>
</body>
</html>