<?php
if(isset($_GET['error'])){
	$errorCode = $_GET['error'];
}else{
	$errorCode="";
}
?>
<b>ERROR <?php echo($errorCode)?>: </b>Some unknown error occured!