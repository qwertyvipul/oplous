<?php //PAGE-9
session_start();
$_SESSION['_pdoConnect']=1; require_once('php/connect/_pdoConnect.php');
session_unset();
session_destroy();

if(isset($_COOKIE['oplous_x']) and isset($_COOKIE['oplous_y'])){
	$salt = $_COOKIE['oplous_x'];
	$token = $_COOKIE['oplous_y'];
	$token.=$salt;
	$token = $token=openssl_digest($token, 'sha512');
	
	$query = "delete from tokens where token = :token";
	try{
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':token', $token);
		$stmt->execute();
	}catch(PDOException $ex){
	}
}

setcookie('oplous_x', '', time()-3600, '/');
setcookie('oplous_y', '', time()-3600, '/');
die(header("Location:login.php"));
?>