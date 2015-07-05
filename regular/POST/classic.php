<html>
    <head>
	 <title>Command injection test environment</title>
    </head>
    <body>
        <form action="classic.php" method="POST">
        Ping address: <input type="text" name="addr">
        <input type="submit">
        </form>
    </body>
</html>

<?php
    # Execute command!
    echo exec("/bin/ping -c 4 ".$_POST["addr"]);
?>
