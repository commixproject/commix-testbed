<html>
    <head>
	 <title>Command injection test environment</title>
    </head>
<body>

<?php
$cookie_name = "addr";
$cookie_value = $_SERVER['REMOTE_ADDR'];
setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day

if(!isset($_COOKIE[$cookie_name])) {
    echo "Cookie named '" . $cookie_name . "' is not set!";
} else {
    echo exec("ping -c 3 " . $_COOKIE[$cookie_name]);
}
?>

</body>
</html> 
