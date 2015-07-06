<html>
    <head>
	 <title>Command injection test environment</title>
    </head>
<body>

<?php
$cookie_name = "user";
$cookie_value = "guest";
setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day

if(!isset($_COOKIE[$cookie_name])) {
    echo "Cookie named '" . $cookie_name . "' is not set!";
} else {
    eval("echo \"Hello, ".$_COOKIE[$cookie_name]."!\";");
}
?>

</body>
</html> 

