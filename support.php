<!doctype html>
<html lang="en">
<head>
	<title>Oplous Support</title>
	<?php require_once('meta-tags.html'); ?>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/remaining-chars.js"></script>
</head>
<?php //PAGE-6
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus==0) die(header('Location:index.php'));
$userId = $_SESSION['userId'];
$type = $loginStatus;

//get the email id of the student or teacher
try{
	if($type==1){
		$query = "select email_id from teachers where teacher_id = :teacher_id";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':teacher_id', $userId, PDO::PARAM_INT);
		if(!$stmt->execute() or $stmt->rowCount()!=1) die('<b>Error 6_101:</b> Some unknown error occured');
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$email = $result['email_id'];
	}else if($type==2){
		$query = "select email_id from students where student_id = :student_id";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':student_id', $userId, PDO::PARAM_INT);
		if(!$stmt->execute() or $stmt->rowCount()!=1) die('<b>Error 6_102:</b> Some unknown error occured');
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$email = $result['email_id'];
	}
}catch(PDOException $ex){
	die('<b>Error 6_103:</b> Some unknown error occured');
}

//display functions working
if(isset($_SESSION['HM']) and $_SESSION['HM']==1){
	$hm = 1;
	$hmTitle = $_SESSION['HM-title'];
	$hmMessage = $_SESSION['HM-message'];
	unset($_SESSION['HM']);
}
if(isset($_SESSION['SM']) and $_SESSION['SM']==1){
	$sm = 1;
	$smTitle = $_SESSION['SM-title'];
	$smMessage = $_SESSION['SM-message'];
	unset($_SESSION['SM']);
}

//fetching all active requests
$list1 = '';
if($type==1){
	try{
		$query = "select support_id, email_id, support_title, support_info, support_response, date_time
		from teacher_support where teacher_id = :teacher_id and support_status = 0 order by date_time";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':teacher_id', $userId, PDO::PARAM_INT);
		if(!$stmt->execute()) die('<b>Error 6_104:</b> Some unknown error occured');
		if($stmt->rowCount()==0){
			$list1.='<p style="padding:5px; ">No active request exists for your account!</p>';
		}else{
			$result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			//color code
			$color1="#e9f3ff";
			$color2="#aed4ff";
			$color='';
			$numcount=0;
			foreach($result1 as $row1){
				$numcount++;
				if($numcount%2==0){
					$color=$color2;
				}else{
					$color=$color1;
				}
				$spid = $row1['support_id'];
				$spmail = $row1['email_id'];
				$sptitle = $row1['support_title'];
				$spinfo = $row1['support_info'];
				$spresponse = $row1['support_response'];
				$timeStamp = $row1['date_time'];
				$netTime = date_create_from_format('Y-m-d H:i:s', $timeStamp);
				$time = date_format($netTime, 'g:i A');
				$date = date_format($netTime, 'F d, Y');
				
				$list1.='
						<div class="ID" style="background:'.$color.'">
							<ul class="ID-list">
								<li class="ID-item">DATE : '.$date.' at '.$time.'</li>
								<li class="ID-item">Email Id : '.$spmail.'</li>
								<li class="ID-item">Response : '.$spresponse.'</li>
								<details class="ID-item">
									<summary>Title : <b>'.$sptitle.'</b></summary>
									<p>'.$spinfo.'</p>
								</details>
							</ul>
						</div><hr/>
					';
			}
		}
		
	}catch(PDOException $ex){
		die('<b>Error 6_105:</b> Some unknown error occured!');
	}
}else if($type==2){
	try{
		$query = "select support_id, email_id, support_title, support_info, support_response, date_time
		from student_support where student_id = :student_id and support_status = 0 order by date_time";
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':student_id', $userId, PDO::PARAM_INT);
		if(!$stmt->execute()) die('<b>Error 6_106:</b> Some unknown error occured');
		if($stmt->rowCount()==0){
			$list1.='<p style="padding:5px; ">No active request exists for your account!</p>';
		}else{
			$result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			//color code
			$color1="#e9f3ff";
			$color2="#aed4ff";
			$color='';
			$numcount=0;
			foreach($result1 as $row1){
				$numcount++;
				if($numcount%2==0){
					$color=$color2;
				}else{
					$color=$color1;
				}
				$spid = $row1['support_id'];
				$spmail = $row1['email_id'];
				$sptitle = $row1['support_title'];
				$spinfo = $row1['support_info'];
				$spresponse = $row1['support_response'];
				$timeStamp = $row1['date_time'];
				$netTime = date_create_from_format('Y-m-d H:i:s', $timeStamp);
				$time = date_format($netTime, 'g:i A');
				$date = date_format($netTime, 'F d, Y');
				
				$list1.='
						<div class="ID" style="background:'.$color.'">
							<ul class="ID-list">
								<li class="ID-item">DATE : '.$date.' at '.$time.'</li>
								<li class="ID-item">Email Id : '.$spmail.'</li>
								<li class="ID-item">Response : '.$spresponse.'</li>
								<details class="ID-item">
									<summary>Title : <b>'.$sptitle.'</b></summary>
									<p>'.$spinfo.'</p>
								</details>
							</ul>
						</div><hr/>
					';
			}
		}
		
	}catch(PDOException $ex){
		die('<b>Error 6_107:</b> Some unknown error occured!');
	}
}
?>
<body>
	<section class="MainContent">
		<?php $_SESSION['menu-header']=1; require_once("menu-header.php"); ?>
		<section class="TVS">
			<h3 class="TVS-title">Always in your Support</h3>
			<section class="SS">
				<h3 class="SS-title">Fill up the Form</h3>
				<p class="SS-text">Let us know if you are facing any problem with the site.</p><hr/>
				<div class="SS-div">
					<?php
						if(isset($hm) and $hm==1){
							echo('<div class="HM">
								<h4 class="HM-title"><pre>'.$hmTitle.'</pre></h4>
								<p class="HM-message">'.$hmMessage.'</p>
							</div>
							');
						}
						if(isset($sm) and $sm==1){
							echo('<div class="SM">
								<h4 class="SM-title"><pre>'.$smTitle.'</pre></h4>
								<p class="SM-message">'.$smMessage.'</p>
							</div>
							');
						}
					?>
					<form id="supportForm" action="php/_support.php" method="post">
						<div class="SS-ic"><h4>Email ID : </h4><pre>We will get in touch with you via email.</pre><input class="SS-input" type="text" id="supportEmail" name="supportEmail" value="<?php echo($email); ?>" style="width:100%; " placeholder="Email ID" required/></div>
						<div class="SS-ic"><h4>Subject : </h4><pre id="remainSubject">60 characters remaining.</pre><input class="SS-input" type="text" id="supportTitle" name="supportTitle" value="" style="width:100%; " placeholder="Subject" required/></div>
						<div class="SS-ic"><h4>Description : </h4><pre id="remainDescription">255 characters remaining.</pre><textarea class="SS-input" type="text" id="supportInfo" name="supportInfo" value="" style="width:100%; " placeholder="Description" required/></textarea></div>
						<button class="SS-input SS-button" type="submit" name="support" value="1">SUBMIT</button><p/>
					</form>
				</div>
			</section>
		</section>
		<section class="TVS">
			<h3 class="TVS-title">All your active requests</h3>
			<div class="TVS-content">
				<?php echo($list1); ?>
			</div>
		</section>
		<?php $_SESSION['common-footer']=1; require_once("common-footer.php"); ?>
	</section>
</body>
</html>