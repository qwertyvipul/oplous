<?php //PAGE-44
require_once(dirname(__FILE__)."/checkRequire.php");
verifyRequire('checkLogin');
function getStatus($pdo){
	if(isset($_SESSION['userId'])){
		$userId = $_SESSION['userId'];
		if(isset($_SESSION['\''.$userId.'\''])){
			if($_SESSION['\''.$userId.'\'']==1){
				return 1;
			}else if($_SESSION['\''.$userId.'\'']==2){
				return 2;
			}
		}
	}
	if(isset($_COOKIE['oplous_x']) and isset($_COOKIE['oplous_y'])){
		$salt = $_COOKIE['oplous_x'];
		$token = $_COOKIE['oplous_y'];
		$token.=$salt;
		$token = $token=openssl_digest($token, 'sha512');
		
		$query = "select token_id, account_type, user_id, password from tokens where token = :token";
		try{
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':token', $token);
			if($stmt->execute() and $stmt->rowCount()==1){
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$tid = $result['token_id'];
				$type = $result['account_type'];
				$userId = $result['user_id'];
				$password = $result['password'];
				$newPassword="0";
				if($type==1){
					$query = "select password from teachers where teacher_id = :teacher_id";
					$stmt = $pdo->prepare($query);
					$stmt->bindParam(':teacher_id', $userId, PDO::PARAM_INT);
					if($stmt->execute()){
						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$newPassword = $result['password'];
					}
				}else if($type==2){
					$query = "select password from students where student_id = :student_id";
					$stmt = $pdo->prepare($query);
					$stmt->bindParam(':student_id', $userId, PDO::PARAM_INT);
					if($stmt->execute()){
						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$newPassword = $result['password'];
					}
				}
				
				if($newPassword == $password){
					$csalt = true; $salt=bin2hex(openssl_random_pseudo_bytes(16, $csalt));
					$ctoken = true; $token=bin2hex(openssl_random_pseudo_bytes(64, $ctoken));
					setcookie('oplous_x' , $salt, time()+60*60*24*7, '/', NULL, NULL, TRUE);
					setcookie('oplous_y' , $token, time()+60*60*24*7, '/', NULL, NULL, TRUE);
					$token .= $salt;
					$token=openssl_digest($token, 'sha512');
					
					//update tokens
					$query = "update tokens set token = :token,
					date_time = now()
					where token_id = :token_id";
					$stmt = $pdo->prepare($query);
					$stmt->bindParam(':token', $token);
					$stmt->bindParam(':token_id', $tid, PDO::PARAM_INT);
					if($stmt->execute()){
						$_SESSION['userId'] = $userId;
						$_SESSION['\''.$userId.'\'']=$type;
						return $type;
					}
				}else{
					$query = "delete from tokens where user_id = :userId and account_type = :account_type";
					$stmt = $pdo->prepare($query);
					$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
					$stmt->bindParam(':account_type', $type, PDo::PARAM_INT);
					$stmt->execute();
				}
			}
		}catch(PDOException $ex){
			die('<b>Error 44_101:</b> Some unknown error occured!');
			return 0;
		}
	}
	return 0;
}
?>