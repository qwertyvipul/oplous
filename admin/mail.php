<?php
$to = '"Vipul Sharma" <<a href="mailto:vipuls526@gmail.com">vipuls526@gmail.com</a>>';
$subject = 'PHP mail tester';
$message = 'This message was sent via PHP!' . PHP_EOL .
		   'Some other message text.' . PHP_EOL . PHP_EOL .
		   '-- viking' . PHP_EOL;
$headers = 'From: "From Name" <<a href="mailto:connect.thapar@gmail.com">connect.thapar@gmail.com</a>>' . PHP_EOL .
		   'Reply-To: <a href="mailto:connect.thapar@gmail.com">connect.thapar@gmail.com</a>' . PHP_EOL .
		   'Cc: "CC Name" <<a href="mailto:connect.thapar@gmail.com">connect.thapar@gmail.coma>>' . PHP_EOL .
		   'X-Mailer: PHP/' . phpversion();
		  
if (mail($to, $subject, $message, $headers)) {
  echo 'mail() Success!';
}
else {
  echo 'mail() Failed!';
}
?>