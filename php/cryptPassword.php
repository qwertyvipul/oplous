<?php
require_once(dirname(__FILE__)."/checkRequire.php");
verifyRequire('cryptPassword');

function generatePassHash($newPassword){
	$salt = substr(str_replace('+','.',base64_encode(md5(mt_rand(), true))),0,16); //generate salt
	$rounds = 10000;
	$passhash = crypt($newPassword, sprintf('$6$%d$%s$', $rounds, $salt));
	return $passhash;
}

function comparePassHash($newPassword, $storedHash){
	$parts = explode('$', $storedHash);
	$newHash = crypt($newPassword, sprintf('$%s$%s$%s$', $parts[1], $parts[2], $parts[3]));
	if($newHash === $storedHash){
		return 1;
	}else{
		return 0;
	}
}
?>