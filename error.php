<?php //PAGE-34
$error='';
$message='';
if(isset($_GET['err'])){
	$error = $_GET['error'];
}
die('<b>Error '.$error.'</b> '.$message.'');
?>