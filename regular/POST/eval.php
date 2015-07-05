<html>
    <head>
	 <title>Command injection test environment</title>
    </head>
    <body>
        <form action="eval.php" method="POST">
        Enter your name: <input type="text" name="name">
        <input type="submit">
        </form>
    </body>
</html>

<?php
if (isset($_POST["name"])){
  # Execute command!
  eval("echo \"Hello, ".$_POST['name']."!\";");
}
?>
