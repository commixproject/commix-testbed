<html>
    <head>
	      <title>Debug Page</title>
    </head>
    <body>
        <form action="eval.php" method="get">
        Ping address: <input type="text" name="name">
        <input type="submit">
        </form>
    </body>
</html>

<?php
if (isset($_GET["name"])){
  # Execute command!
  eval("echo \"Hello, ".$_GET['name']."!\";");
}
?>