<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1"> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Commix testbed - Command injection test environment</title>
    <!-- Bootstrap Core CSS -->
    <link href="../../../css/bootstrap.min.css" rel="stylesheet">
    <!-- font-awesome Core CSS -->
    <link href="../../../css/font-awesome.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../../../css/heroic-features.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/bootstrap-submenu.css">
    <script src="../../../js/bootstrap-submenu.js" defer></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
               <a class="navbar-brand">
                    <img style="max-width:43px; margin-top: -11px;"
                         src="../../../img/logo.png">
                </a>
                <a class="navbar-brand" href="../../../index.php">commix-testbed (v0.1)</a>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
    <!-- Page Content -->
    <div class="container">
        <!-- Jumbotron Header -->
        <header class="jumbotron hero-spacer">
            <h2><b>Commix testbed - A command injection test environment!</b></h2>
            <p>A collection of web pages, vulnerable to <b><a href="https://www.owasp.org/index.php/Command_Injection">command injection flaws</a></b>, used to test <b><a href="https://github.com/stasinopoulos/commix">commix</a></b>'s vulnerability <b>detection</b> and <b>exploitation</b> features.</p>
            <p>
            <a href="https://twitter.com/commixproject" class="twitter-follow-button" data-show-count="false">Follow @commixproject</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
            <iframe src="https://ghbtns.com/github-btn.html?user=stasinopoulos&repo=commix&type=star&count=true" frameborder="0" scrolling="0" width="170px" height="20px"></iframe>
        </header>
        <!-- Title -->
        <div class="row">
            <div class="text-center">
                <h3><a href="../../../">Back</a></b> to command injection scenarios categories</h3>
            </div>
        </div>
        <!-- /.row -->
        <!-- Page Features -->
        <div class="container">
          <div class="row">
            <!-- Adapted from the SecureState 2015 High School CTF  http://www.mountunion.edu/information-security-challenge-slated-for-saturday  -->
            <!-- Originally hosted at http://warroom.securestate.com -->
            <!-- Original source: https://gist.githubusercontent.com/WJDigby/e44b585c6b4e1c11c067/raw/ce5bcdc393ed293eaf2d1514d90c3045a2892dc7/injection.php -->
            <div class="jumbotron hero-spacer">
            <p><form action="classic_hash.php" method="GET">
            String to Hash:<input type="text" name="string" value="hash me babe!">
            <input value="Submit!" type="submit">
            </form>
            <br>
            <b>
            <b>MD5 Hash:  </b>
            <?php
              $string = $_GET['string'];
              echo shell_exec('echo '.$string.' | md5sum');
            ?>
            </b>
          </div>
        </div>
        <!-- /.row -->
        <hr>
        <!-- Footer -->
        <footer>
            <div class="row text-center">
                <div class="col-lg-12">
                    <p>Copyright &copy; Anastasios Stasinopoulos</p>
                    <a href="https://twitter.com/ancst" class="twitter-follow-button" data-show-count="false">Follow @ancst</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                </div>
            </div>
        </footer>
    </div>
    <!-- /.container -->
    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
</body>
</html>