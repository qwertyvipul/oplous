<!doctype html>
<html lang="en">
<head>
	<title>Teacher</title>
	<?php require_once('meta-tags.html'); ?>

<style>
.CD{text-align:left; }
.CD-1-title{}
.CD-2-item{margin-left:10%; list-style:none; }
.CD-3-item{margin-left:10%; list-style:none; }
</style>
</head>
<?php //PAGE-23
$_SESSION['checkRequire']=1; require_once('php/checkRequire.php');
verifyRequire('teacher-profile');
$userId = $_SESSION['userId'];

//fetching general detials
try{
	$query = "select name,
	email_id from teachers where teacher_id = :teacher_id";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':teacher_id', $userId, PDO::PARAM_INT);
	if(!$stmt->execute() or $stmt->rowCount()!=1) die('<b>Error 23_101:</b>Unknown Error!');
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$name = $result['name'];
	$email = $result['email_id'];
}catch(PDOException $ex){
	die('<b>Error 23_102:</b>Unknown Error!');
}

//fetching class details
$list1="";
$list2="";
try{
	//all the years teacher teach
	$query = "select distinct(bi.year) as year
	from batch_info bi
	inner join class_info ci
	on(ci.batch_id = bi.batch_id and ci.teacher_id = :teacher_id)
	order by bi.year";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':teacher_id', $userId, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 23_103:</b>Unknown Error!');
	$result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($result1 as $row1){
		$year = $row1['year'];
		
		//open-1-1
		$list1.='<div class="CD-1 TVS-summary">
				<h3 class="CD-1-title">Year : '.$year.'</h3>';
				
		//open-2-1
		$list2.='<div class="CD-1 TVS-summary">
				<h3 class="CD-1-title">Year : '.$year.'</h3>';
		
		
		//all the batch teacher teaches in a specific year
		$query = "select distinct(bi.batch_id) as batch_id
		from batch_info bi
		inner join class_info ci
		on(ci.batch_id = bi.batch_id
		and bi.year = :year
		and ci.teacher_id = :teacher_id)";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':year', $year, PDO::PARAM_INT);
		$stmt->bindParam(':teacher_id', $userId, PDO::PARAM_INT);
		if(!$stmt->execute()) die('<b>Error 23_104:</b>Unknown Error!');
		$result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($result2 as $row2){
			$bid = $row2['batch_id'];
			
			//select batch_name
			$query = "select batch_code from batch_info where batch_id = :batch_id";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':batch_id', $bid, PDO::PARAM_INT);
			if(!$stmt->execute()) die('<b>Error 23_105:</b>Unknown Error!');
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$batchCode = $result['batch_code'];
			
			//open-1-2
			$list1.='<div class="CD-2">
					<h3 class="CD-2-title"><a href="batch-kids.php?bid='.$bid.'">'.$batchCode.'</a></h3>';
					
			//open-2-2
			$list2.='<div class="CD-2">
					<h3 class="CD-2-title"><a href="batch-kids.php?bid='.$bid.'">'.$batchCode.'</a></h3>';
			
			//all the classes teacher teach in a batch
			$query = "select distinct(ci.class_id) as class_id
			from class_info ci
			inner join batch_info bi
			on(ci.batch_id = bi.batch_id and ci.batch_id = :batch_id and ci.teacher_id = :teacher_id and bi.year = :year)";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':batch_id', $bid, PDO::PARAM_INT);
			$stmt->bindParam(':teacher_id', $userId, PDO::PARAM_INT);
			$stmt->bindParam(':year', $year, PDO::PARAM_INT);
			if(!$stmt->execute())die('<b>Error 23_106:</b>Unknown Error!');
			$result3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			//open-1-3
			$list1.='<ul class="CD-2-list">';
			foreach($result3 as $row3){
				$cid = $row3['class_id'];
				
				//select the name of the class
				$query = "select concat(si.subject_code,ci.class_type) as class
				from class_info ci
				inner join subject_info si
				on(ci.subject_id = si.subject_id and ci.class_id = :class_id)";
				$stmt = $pdo->prepare($query);
				$stmt->bindParam(':class_id', $cid, PDO::PARAM_INT);
				if(!$stmt->execute()) die('<b>Error 23_107:</b>Unknown Error!');
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$classCode = $result['class'];
				$list1.='<li class="CD-2-item">'.$classCode.'</li>';
			}
			
			//close-1-3 //close-1-2
			$list1.='</ul></div>';
			
			//all the subjects teacher teach in a batch
			$query = "select distinct(ci.subject_id) as subject_id
			from class_info ci
			inner join batch_info bi
			on(ci.batch_id = bi.batch_id and ci.batch_id = :batch_id and ci.teacher_id = :teacher_id and bi.year = :year)";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':batch_id', $bid, PDO::PARAM_INT);
			$stmt->bindParam(':teacher_id', $userId, PDO::PARAM_INT);
			$stmt->bindParam(':year', $year, PDO::PARAM_INT);
			if(!$stmt->execute()) die('<b>Error 23_108:</b>Unknown Error!');
			$result3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach($result3 as $row3){
				$subid = $row3['subject_id'];
				
				//check the parent_id of the batch
				$query = "select bi.parent_id as parent_id
				from batch_info bi
				inner join class_info ci
				on(ci.batch_id = bi.batch_id and ci.batch_id = :batch_id and ci.teacher_id = :teacher_id and bi.year = :year and ci.subject_id = :subject_id)";
				$stmt = $pdo->prepare($query);
				$stmt->bindParam(':batch_id', $bid, PDO::PARAM_INT);
				$stmt->bindParam(':teacher_id', $userId, PDO::PARAM_INT);
				$stmt->bindParam(':year', $year, PDO::PARAM_INT);
				$stmt->bindParam(':subject_id', $subid, PDO::PARAM_INT);
				if(!$stmt->execute()) die('<b>Error 23_109:</b>Unknown Error!');
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$parent = $result['parent_id'];
				
				if(is_null($parent)){
					//select all the class details for this parent batch and subject_id
					$query = "select si.subject_code as subject_code, 
					si.subject_name as subject_name, 
					t.teacher_id as teacher_id,
					t.name as name,
					ci.class_type as class_type
					from class_info ci
					inner join subject_info si
					on(ci.subject_id = si.subject_id and ci.subject_id = :subject_id and ci.batch_id = :batch_id)
					inner join teachers t
					on(ci.teacher_id = t.teacher_id)";
					$stmt = $pdo->prepare($query);
					$stmt->bindParam(':subject_id', $subid, PDO::PARAM_INT);
					$stmt->bindParam(':batch_id', $bid, PDO::PARAM_INT);
					if(!$stmt->execute()) die('<b>Error 23_110:</b>Unknown Error!');
					$result4 = $stmt->fetchAll(PDO::FETCH_ASSOC);
					
					//open-2-3
					$list2.='<ul class="CD-2-list">';
					foreach($result4 as $row4){
						$subcode = $row4['subject_code'];
						$subname = $row4['subject_name'];
						$tid = $row4['teacher_id'];
						$teacherName = $row4['name'];
						$classType = $row4['class_type'];
						$list2.='<li class="CD-2-item">'.$subcode.$classType.' - '.$teacherName.'</li>';
					}
					
					//select all the child batch for this parent batch
					$query = "select batch_id, batch_code from batch_info where parent_id = :parent_id";
					$stmt = $pdo->prepare($query);
					$stmt->bindParam(':parent_id', $bid, PDO::PARAM_INT);
					if(!$stmt->execute()) die('<b>Error 23_111:</b>Unknown Error!');
					$result4 = $stmt->fetchAll(PDO::FETCH_ASSOC);
					foreach($result4 as $row4){
						$child = $row4['batch_id'];
						$childCode = $row4['batch_code'];
						
						//open-2-4 //open-2-5
						$list2.='<details class="CD-2-item CD-3">
								<summary class="CD-3-title"><a href="batch-kids.php?bid='.$child.'"><b>'.$childCode.'</b></a></summary>
								<ul class="CD-3-list">';
						
						//select all the class details for this parent batch and subject_id
						$query = "select si.subject_code as subject_code, 
						si.subject_name as subject_name, 
						t.teacher_id as teacher_id,
						t.name as name,
						ci.class_type as class_type
						from class_info ci
						inner join subject_info si
						on(ci.subject_id = si.subject_id and ci.subject_id = :subject_id and ci.batch_id = :batch_id)
						inner join teachers t
						on(ci.teacher_id = t.teacher_id)";
						$stmt = $pdo->prepare($query);
						$stmt->bindParam(':subject_id', $subid, PDO::PARAM_INT);
						$stmt->bindParam(':batch_id', $child, PDO::PARAM_INT);
						if(!$stmt->execute()) die('<b>Error 23_112:</b>Unknown Error!');
						$result4 = $stmt->fetchAll(PDO::FETCH_ASSOC);
						foreach($result4 as $row4){
							$subcode = $row4['subject_code'];
							$subname = $row4['subject_name'];
							$tid = $row4['teacher_id'];
							$teacherName = $row4['name'];
							$classType = $row4['class_type'];
							$list2.='<li class="CD-3-item">'.$subcode.$classType.' - '.$teacherName.'</li>';
						}
						
						//close-2-5 //close-2-4
						$list2.='</ul></details>';
					}
					//close-2-3 //close-2-2
					$list2.='</ul>';
				}else{
					//select all the class details for this parent batch and subject_id
					$query = "select si.subject_code as subject_code,
					si.subject_name as subject_name, 
					t.teacher_id as teacher_id,
					t.name as name,
					ci.class_type as class_type
					from class_info ci
					inner join subject_info si
					on(ci.subject_id = si.subject_id and ci.subject_id = :subject_id and ci.batch_id = :batch_id)
					inner join teachers t
					on(ci.teacher_id = t.teacher_id)";
					$stmt = $pdo->prepare($query);
					$stmt->bindParam(':subject_id', $subid, PDO::PARAM_INT);
					$stmt->bindParam(':batch_id', $parent, PDO::PARAM_INT);
					if(!$stmt->execute()) die('<b>Error 23_113:</b>Unknown Error!');
					$result4 = $stmt->fetchAll(PDO::FETCH_ASSOC);
					
					//open-2-3
					$list2.='<ul class="CD-2-list">';
					foreach($result4 as $row4){
						$subcode = $row4['subject_code'];
						$subname = $row4['subject_name'];
						$tid = $row4['teacher_id'];
						$teacherName = $row4['name'];
						$classType = $row4['class_type'];
						$list2.='<li class="CD-2-item">'.$subcode.$classType.' - '.$teacherName.'</li>';
					}
					
					//select all the class for this child batch
					$query = "select si.subject_code as subject_code,
					si.subject_name as subject_name, 
					t.teacher_id as teacher_id,
					t.name as name,
					ci.class_type as class_type
					from class_info ci
					inner join subject_info si
					on(ci.subject_id = si.subject_id and ci.subject_id = :subject_id and ci.batch_id = :batch_id)
					inner join teachers t
					on(ci.teacher_id = t.teacher_id)";
					$stmt = $pdo->prepare($query);
					$stmt->bindParam(':subject_id', $subid, PDO::PARAM_INT);
					$stmt->bindParam(':batch_id', $bid, PDO::PARAM_INT);
					if(!$stmt->execute()) die('<b>Error 23_114:</b>Unknown Error!');
					$result4 = $stmt->fetchAll(PDO::FETCH_ASSOC);
					foreach($result4 as $row4){
						$subcode = $row4['subject_code'];
						$subname = $row4['subject_name'];
						$tid = $row4['teacher_id'];
						$teacherName = $row4['name'];
						$classType = $row4['class_type'];
						$list2.='<li class="CD-2-item">'.$subcode.$classType.' - '.$teacherName.'</li>';
					}
					$list2.='</ul>';
					
				}
				
			}
			//close2-2
			$list2.='</div>';
		}
		//close-1-1
		$list1.='</div><hr/>';
		
		//close-2-1
		$list2.='</div><hr/>';
	}
}catch(PDOException $ex){
	die('<b>Error 23_115:</b>Unknown Error!');
}
?>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once("menu-header.php");?>
		<section class="Profile-content">
			<section class="GD TVS" style="text-align:left; ">
				<h3 class="TVS-title" style="text-align:left;">General Details</h3>
				<div class="TVS-summary">
					<p><b>Name :</b> <?php echo($name); ?></p>
					<?php //echo($userId); ?></p>
					<p><b>Email Id :</b> <?php echo($email); ?></p>
					<a href="change-password.php"><button>Change Password</button></a>
				</div>
			</section><hr/>
			<div class="CD">
				<h3 class="CD-title TVS-title" style="text-align:left;">Classes you teach</h3>
				<?php echo($list1); ?>
			</div>
			<div class="CD">
				<h3 class="CD-title TVS-title" style="text-align:left;">All you need to know</h3>
				<?php echo($list2); ?>
		</section>
		<?php $_SESSION['common-footer']=1; require_once('common-footer.php'); ?>
	</section>
</body>
</html>