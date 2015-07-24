<html>
    <head>
	 <title>Command injection test environment</title>
    </head>
    <body>
        <form action="eval.php" method="GET">
        Enter your name: <input type="text" name="user">
        <input type="submit">
        </form>
    </body>
</html>

<?php
if (isset($_GET["user"])){
  # Execute command!
  eval("echo \"Hello, ".$_GET['user']."!\";");
}
?>
