<?php //PAGE-36
require_once(dirname(__FILE__)."/../checkRequire.php");
verifyRequire('_pdoConnect');
try{
	$pdo = new PDO('mysql:host=localhost;dbname=bubblegum;charset=utf8mb4', 'root', '12345678');
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //pdo into exception mode
	$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); //turn off prepare emulation	
}catch(PDOexception $ex){
	die('<b>Error 36_101:</b> Some unknown error occured!');
}
?>