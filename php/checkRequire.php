<?php //PAGE-37
function verifyRequire($keyword){
	if(!isset($_SESSION[$keyword]) or $_SESSION[$keyword]!=1){
		die('<b>Error: 37_101:</b> Unauthorised Access Denied!');
	}else{
		unset($_SESSION[$keyword]);
	}
}
?>