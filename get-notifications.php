<?php //PAGE-52
session_start();
//check login
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
$_SESSION['checkLogin']=1; require_once('php/checkLogin.php');
$loginStatus = getStatus($pdo);
if($loginStatus==0) die(header('Location:login.php'));
$userId = $_SESSION['userId'];
$type = $loginStatus;

//required variables
if(!isset($_GET['offset']) or !is_numeric($_GET['offset'])) die('<b>Error 11_101:</b> Page Breakdown!');
$offset = $_GET['offset'];

//fetching list
$list="";
$prefix="";

if($type==1){
	$prefix='teacher';
}else if($type==2){
	$prefix='student';
}else{
	die('<b>Error 11_102:</b> Page Breakdown!');
}
try{
	$pdo->beginTransaction();
	$query = "select nid, summary, description, date_time, read_code from ".$prefix."_notifications where ".$prefix."_id = :user_id order by nid desc limit :limit, 20";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
	$stmt->bindParam(':limit', $offset, PDO::PARAM_INT);
	if(!$stmt->execute()) die('<b>Error 11_103:</b> UH-OH!, some error occured from our end.');
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$divcount=0;
	$color='';
	foreach($result as $row){
		$moreinfo='';
		$divcount++;
		$color = ($divcount%2==0)?'#ebebff':'';
		$nid = $row['nid'];
		$data = $row['summary'];
		$detail = $row['description'];
		$date_time = $row['date_time'];
		$readCode = $row['read_code'];
		$netTime = date_create_from_format('Y-m-d H:i:s', $date_time);
		$time = date_format($netTime, 'g:i A');
		$date = date_format($netTime, 'F d, Y');
		
		if(!is_null($detail)){
			$moreinfo='<details class="NReadMore"><summary>Read More</summary><pre class="Npre" style="text-align:left; white-space:pre-line; ">'.$detail.'</pre></details>';
		}
		
		if($readCode == 0){
			$border ='border:5px solid #1181ff;';
			$list.='
			<div class="NS-div" style="background:'.$color.'; '.$border.' ">
				<p class="NS-info NS-text">'.$data.'</p>'.$moreinfo.'
				<pre class="NS-info NS-date">'.$date.'</pre><pre class="NS-info NS-time">'.$time.'</pre>
				<div style="clear:both"></div>
			</div><hr/>';
		
			$query = "update ".$prefix."_notifications set read_code = 1 where nid = :nid";
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':nid', $nid, PDO::PARAM_INT);
			if(!$stmt->execute()) die('<b>Error 11_104:</b> UH-OH!, some error occured from our end.');
			continue;
		}
		$list.='
		<div class="NS-div" style="background:'.$color.'">
			<p class="NS-info NS-text">'.$data.'</p>'.$moreinfo.'
			<pre class="NS-info NS-date">'.$date.'</pre><pre class="NS-info NS-time">'.$time.'</pre>
			<div style="clear:both"></div>
		</div><hr/>';
	}
	$pdo->commit();
}catch(PDOException $ex){
	$pdo->rollBack();
	die($ex);
	die('<b>Error 11_105:</b> UH-OH!, some error occured from our end.');
}
echo($list);
?>