<html>
    <head>
	    <title>Debug Page</title>
    </head>
    <body>
	    <form action="classic.php" method="get">
	    Ping address: <input type="text" name="addr">
	    <input type="submit">
	    </form>

    </body>
</html>

<?php
    # Execute command!
    echo exec("/bin/ping -c 4 ".$_GET["addr"]);
?>
