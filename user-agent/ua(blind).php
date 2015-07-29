<html>
    <head>
	 <title>Command injection test environment</title>
    </head>
<body>

<?php
$user_agent = $_SERVER['HTTP_USER_AGENT'];
exec("echo '".$user_agent."' | grep Firefox", $output, $return);
if (!$return) {
   echo "Viva La Mozilla Firefox!";
}else{
   echo "Please, download Mozilla Firefox!";
}
?>

</body>
</html> 
