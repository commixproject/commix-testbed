<html>
    <head>
	 <title>Command injection test environment</title>
    </head>
<body>

<?php
$server_name = $_SERVER["SERVER_NAME"];
$referer = $_SERVER['HTTP_REFERER'];
exec("echo '".$referer."' | grep '".$server_name."'", $output, $return);
if (!$return) {
   echo "Welcome to ".$server_name."!";
}else{
   echo "Hey, what are you trying to do?!";
}
?>
</body>
</html>
