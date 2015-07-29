<html>
    <head>
	 <title>Command injection test environment</title>
    </head>
<body>

<?php
$user_agent = $_SERVER['HTTP_USER_AGENT'];
echo exec("echo '".$user_agent."'");
?>

</body>
</html> 
