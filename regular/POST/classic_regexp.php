<html>
    <head>
     <title>Command injection test environment</title>
    </head>
    <body>
        <form action="classic_regexp.php" method="POST">
        Enter your name: <input type="text" name="addr">
        <input type="submit">
        </form>
    </body>
</html>

<?php
    $addr = $_POST['addr'];
    if(isset($addr)){
        # Inspired from pentesterlab.com - 'Web for Pentester' course.
        # https://pentesterlab.com/exercises/web_for_pentester
        if (!(preg_match('/^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$/m',$addr))){
            die("Invalid IP address format.");
        }else{
            # Execute command!
            echo exec("/bin/ping -c 4 ".$addr);
        }
    }
?>