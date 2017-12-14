<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Commix testbed - Command injection test environment</title>
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- font-awesome Core CSS -->
    <link href="css/font-awesome.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/heroic-features.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap-submenu.css">
    <script src="js/bootstrap-submenu.js" defer></script>
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
                         src="../commix-testbed/img/logo.png">
                </a>
                <a class="navbar-brand" href="index.php">commix-testbed (v0.1)</a>

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
            <p>A collection of web pages, vulnerable to <b><a href="https://www.owasp.org/index.php/Command_Injection">command injection flaws</a></b>, used to test <b><a href="https://github.com/commixproject/commix">commix</a></b>'s vulnerability <b>detection</b> and <b>exploitation</b> features.</p>
            <p>
            <a href="https://twitter.com/commixproject" class="twitter-follow-button" data-show-count="false">Follow @commixproject</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
            <iframe src="https://ghbtns.com/github-btn.html?user=commixproject&repo=commix&type=star&count=true" frameborder="0" scrolling="0" width="170px" height="20px"></iframe>
        <div class="text-center"><!--You can add col-lg-12 if you want -->
        </div>
        </header>
        <!-- Title -->
        <div class="row">
            <div class="col-lg-12 text-center">
                <h3>Command injection scenarios categories</h3>
                <br><br>

            </div>
        </div>
        <!-- /.row -->
        <!-- Page Features -->
        <div class="container">
          <div class="row ">
              <!-- User-Agent HTTP Header -->
              <div class="col-md-3 col-sm-6 hero-feature">
                  <div class="panel panel-info">
                      <div class="panel-heading">
                        <h4>1. Regular (GET / POST)  </h4>
                      </div>
                      <ul class="list-group">
                          <li class="list-group-item">Classic regular example<br>(<a href="scenarios/regular/GET/classic.php">GET</a> | <a href="scenarios/regular/POST/classic.php"> POST</a>)</li>
                          <li class="list-group-item">Classic (Base64) regular example<br>(<a href="scenarios/regular/GET/classic_b64.php">GET</a> | <a href="scenarios/regular/POST/classic_b64.php"> POST</a>)</li>
                          <li class="list-group-item">Classic (Hex) regular example<br>(<a href="scenarios/regular/GET/classic_hex.php">GET</a> | <a href="scenarios/regular/POST/classic_hex.php"> POST</a>)</li>
                          <li class="list-group-item">Classic single-quote example<br>(<a href="scenarios/regular/GET/classic_quote.php">GET</a> | <a href="scenarios/regular/POST/classic_quote.php"> POST</a>)</li>       
                          <li class="list-group-item">Classic double-quote example<br>(<a href="scenarios/regular/GET/classic_double_quote.php">GET</a> | <a href="scenarios/regular/POST/classic_double_quote.php"> POST</a>)</li> 
                          <li class="list-group-item">Classic non-space example<br>(<a href="scenarios/regular/GET/classic_non_space.php">GET</a> | <a href="scenarios/regular/POST/classic_non_space.php"> POST</a>)</li> 
                          <li class="list-group-item">Classic blacklisting example<br>(<a href="scenarios/regular/GET/classic_blacklisting.php">GET</a> | <a href="scenarios/regular/POST/classic_blacklisting.php"> POST</a>)</li>
                          <li class="list-group-item">Classic hashing example<br>(<a href="scenarios/regular/GET/classic_hash.php">GET</a> | <a href="scenarios/regular/POST/classic_hash.php"> POST</a>)</li>
                          <li class="list-group-item">Classic example & Basic HTTP Authentication<br>(<a href="scenarios/regular/GET/classic_basic_auth.php">GET</a> | <a href="scenarios/regular/POST/classic_basic_auth.php"> POST</a>)</li>
                          <li class="list-group-item">Classic example & Digest HTTP Authentication<br>(<a href="scenarios/regular/GET/classic_digest_auth.php">GET</a> | <a href="scenarios/regular/POST/classic_digest_auth.php"> POST</a>)</li>
                          <li class="list-group-item">Blind regular example<br>(<a href="scenarios/regular/GET/blind.php">GET</a> | <a href="scenarios/regular/POST/blind.php"> POST</a>)</li>
                          <li class="list-group-item">Double Blind regular example<br>(<a href="scenarios/regular/GET/double_blind.php">GET</a> | <a href="scenarios/regular/POST/double_blind.php"> POST</a>)</li>
                          <li class="list-group-item">Eval regular example<br>(<a href="scenarios/regular/GET/eval.php">GET</a> | <a href="scenarios/regular/POST/eval.php"> POST </a>)</li>
                          <li class="list-group-item">Eval (Base64) regular example<br>(<a href="scenarios/regular/GET/eval_b64.php">GET</a> | <a href="scenarios/regular/POST/eval_b64.php"> POST </a>)</li>
                          <li class="list-group-item">Classic (JSON) regular example<br>(<a href="scenarios/regular/POST/classic_json.php">POST</a>)</li>
                          <li class="list-group-item">Blind (JSON) regular example<br>(<a href="scenarios/regular/POST/blind_json.php">POST</a>)</li>
                          <li class="list-group-item">Eval (JSON) regular example<br>(<a href="scenarios/regular/POST/eval_json.php">POST</a>)</li>
                          <li class="list-group-item">Preg_match() regular example<br>(<a href="scenarios/regular/GET/preg_match.php">GET</a> | <a href="scenarios/regular/POST/preg_match.php"> POST</a>)</li>
                          <li class="list-group-item">Preg_match() blind example<br>(<a href="scenarios/regular/GET/preg_match_blind.php">GET</a> | <a href="scenarios/regular/POST/preg_match_blind.php"> POST </a>)</li>
                          <li class="list-group-item">Preg_Replace() regular example<br>(<a href="scenarios/regular/GET/preg_replace.php?replace=/Hello/&with=Bye">GET</a>)</li>
                          <li class="list-group-item">Str_Replace() regular example<br>(<a href="scenarios/regular/GET/str_replace.php">GET</a> | <a href="scenarios/regular/POST/str_replace.php"> POST </a>)</li>
                          <li class="list-group-item">Create_Function() regular example<br>(<a href="scenarios/regular/GET/create_function.php">GET</a> | <a href="scenarios/regular/POST/create_function.php"> POST </a>)</li>
                      </ul>

                  </div>
              </div>
              <!-- Shity filters -->
              <div class="col-md-3 col-sm-6 hero-feature">
                  <div class="panel panel-info">
                      <div class="panel-heading">
                        <h4>2. Regex Filters </h4>
                      </div>
                        <ul class="list-group">
                            <li class="list-group-item"><a href="scenarios/filters/lax_domain_name.php">Regex for domain name validation</a></li>
                            <li class="list-group-item"><a href="scenarios/filters/nested_quotes.php">Nested quotes</a></li>
                            <li class="list-group-item"><a href="scenarios/filters/no_colon_no_pipe_no_ampersand_no_dollar.php">Regex filter for colon/pipe/ampersand/dollar</a></li>
                            <li class="list-group-item"><a href="scenarios/filters/no_space.php">Regex filter for spaces</a></li>
                            <li class="list-group-item"><a href="scenarios/filters/no_space_no_colon_no_pipe_no_ampersand.php">Regex filter for space/colon/pipe/ampersand</a></li>
                            <li class="list-group-item"><a href="scenarios/filters/no_space_no_colon_no_pipe_no_ampersand_no_dollar.php">Regex filter for space/colon/pipe/ampersand/dollar</a></li> 
                            <li class="list-group-item"><a href="scenarios/filters/no_white_chars.php">Regex filter for white chars</a></li> 
                            <li class="list-group-item"><a href="scenarios/filters/simple_stop_alphanum.php">Alphanum for input end</a></li> 
                            <li class="list-group-item"><a href="scenarios/filters/no_white_chars_stop_alnum.php">Alphanum for input end (filter for white chars)</a></li>
                            <li class="list-group-item"><a href="scenarios/filters/simple_start_alphanum.php">Alphanum for input start</a></li>
                            <li class="list-group-item"><a href="scenarios/filters/no_white_chars_start_alphanum.php">Alphanum for input start (filter for white chars)</a></li> 
                        </ul>
                  </div>
              </div>
              <!-- User-Agent HTTP Header -->
              <div class="col-md-3 col-sm-6 hero-feature">
                  <div class="panel panel-info">
                      <div class="panel-heading">
                        <h4>3. User-Agent HTTP Header </h4>
                      </div>
                        <ul class="list-group">
                            <li class="list-group-item"><a href="scenarios/user-agent/ua(classic).php">Classic user-agent-based example</a></li>
                            <li class="list-group-item"><a href="scenarios/user-agent/ua(blind).php">Blind user-agent-based example</a></li>        
                            <li class="list-group-item"><a href="scenarios/user-agent/ua(eval).php">Eval user-agent-based example</a></li>
                        </ul>
                  </div>
              </div>
              <!-- Cookie Header HTTP Header -->
              <div class="col-md-3 col-sm-6 hero-feature">
                  <div class="panel panel-info">
                        <div class="panel-heading">
                          <h4>4. Cookie HTTP Header</h4>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item"><a href="scenarios/cookie/cookie(classic).php">Classic cookie-based example</a></li>
                            <li class="list-group-item"><a href="scenarios/cookie/cookie(b64).php">Classic cookie-based (Base64) example</a></li>
                            <li class="list-group-item"><a href="scenarios/cookie/cookie(blind).php">Blind cookie-based example</a></li>        
                            <li class="list-group-item"><a href="scenarios/cookie/cookie(eval).php">Eval cookie-based example</a></li>
                        </ul>
                  </div>
              </div>
              <!-- Referer HTTP Header -->
              <div class="col-md-3 col-sm-6 hero-feature">
                  <div class="panel panel-info">
                        <div class="panel-heading">
                          <h4>5. Referer HTTP Header</h4>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item"><a href="scenarios/referer/referer(classic).php">Classic referer-based example</a></li>
                            <li class="list-group-item"><a href="scenarios/referer/referer(blind).php">Blind referer-based example</a></li>       
                            <li class="list-group-item"><a href="scenarios/referer/referer(eval).php">Eval referer-based example</a></li>
                        </ul>
                   </div>
              </div>
          </div>
        </div>
        <!-- /.row -->
        <hr>
        <!-- Footer -->
        <footer>
            <div class="row text-center">
                <div class="col-lg-12">
                    <p>Made in Greece with <b>&hearts;</b> by <a href="https://github.com/stasinopoulos"> Anastasios Stasinopoulos</a>.</p>
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
