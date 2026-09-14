<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The cookie-borne command runs with nothing reflected back.</p>
      <p class="lesson">A cookie-borne command with nothing reflected back.</p>
      <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>$cookie_name = &quot;addr&quot;;
$cookie_value = (isset($_SERVER[&#x27;REMOTE_ADDR&#x27;]) ? $_SERVER[&#x27;REMOTE_ADDR&#x27;] : &#x27;&#x27;);
setcookie($cookie_name, $cookie_value, time() + (86400 * 30), &quot;/&quot;); // 86400 = 1 day
if(!isset($_COOKIE[$cookie_name])) {
    echo &quot;Cookie named &#x27;&quot; .$cookie_name. &quot;&#x27; is not set!&quot;;
} else {
  if( stristr(php_uname(&#x27;s&#x27;), &#x27;Windows NT&#x27;)){
    # Windows-based command execution.
    exec(&quot;ping &quot; . (isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : &#x27;&#x27;), $output, $return);
  } else {
    # Unix-based command execution.
    exec(&quot;/bin/ping -c 4 &quot; . (isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : &#x27;&#x27;), $output, $return);
  }
  if (!$return) {
      echo &quot;Hey &quot;.$cookie_value.&quot;, you are alive!&quot;;
  } else {
      echo &quot;Oops &quot;.$cookie_value.&quot;, you are dead beef :/&quot;;
  }
}</code></pre>
      </details>
    </aside>
  </div>

  <footer>
    <p>Made in Greece with <span class="heart">&hearts;</span> by
      <a href="https://github.com/stasinopoulos">Anastasios Stasinopoulos</a> &middot;
      part of the <a href="https://commixproject.com">Commix Project</a>.</p>
    <p class="copy">commix-testbed is GPLv3 licensed &copy; 2015-2026.</p>
  </footer>
</div>

<script>
// The page prints its result into the last <b>: empty means nothing was submitted yet, a refusal
// is shown in red rather than green, and the shared indentation of the markup is taken off so the
// result starts hard left.
(function () {
  var out = document.querySelector(".live > b:last-of-type");
  if (!out) return;
  var lines = out.textContent.split(/\r?\n/);
  var filled = lines.filter(function (l) { return l.trim(); });
  var common = filled.reduce(function (n, l) { return Math.min(n, l.match(/^[ \t]*/)[0].length); }, 1e9);
  if (filled.length) {
    out.textContent = lines.map(function (l) { return l.slice(common); }).join(String.fromCharCode(10)).trim();
  }
  var text = out.textContent.trim();
  if (!text) {
    // A failing command writes to stderr, which exec() does not return - so an empty result after
    // a submission means it ran and printed nothing, not that nothing happened.
    var submitted = location.search.length > 1 || document.referrer.indexOf(location.pathname) !== -1;
    if (!submitted) { out.remove(); return; }
    out.textContent = "(no output - the command printed nothing to stdout)";
    out.classList.add("silent");
    return;
  }
  if (/invalid|hack attempt|not allowed|no white spaces|is not set|denied|only$/i.test(text)) {
    out.classList.add("refused");
  }
})();
</script>
</body>
</html>
TESTBED_TAIL;
register_shutdown_function(function () use ($__tail) { echo $__tail; });
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cookie header - Nothing returned &middot; commix-testbed</title>
<meta name="robots" content="noindex">
<link rel="shortcut icon" href="../../favicon.ico">
<link href="https://fonts.googleapis.com/css?family=Exo+2:400,500,600,700" rel="stylesheet">
<link rel="stylesheet" href="../../css/testbed.css">
</head>
<body>

<nav class="nav">
  <div class="wrap">
    <a class="brand" href="../../index.php">
      <img src="../../img/testbed_header_logo.png" alt="commix-testbed"></a>
    <a class="back" href="../../index.php">&larr; All scenarios</a>
  </div>
</nav>

<div class="wrap page">

  <header class="sc-head">
    <p class="crumb">Cookie header</p>
    <h1>Cookie header - Nothing returned</h1>
    <span class="badges"><span class="type t-blind">Blind</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <div class="howto"><p>The value comes from a <b>cookie</b>, which the page sets for you on the first visit. Edit that cookie in your browser's developer tools - or send your own <code>Cookie</code> header - and reload.</p></div><br>
                <b><?php
                $cookie_name = "addr";
                $cookie_value = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
                setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day

                if(!isset($_COOKIE[$cookie_name])) {
                    echo "Cookie named '" .$cookie_name. "' is not set!";
                } else {
                  if( stristr(php_uname('s'), 'Windows NT')){ 
                    # Windows-based command execution.
                    exec("ping " . (isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : ''), $output, $return); 
                  } else {
                    # Unix-based command execution.
                    exec("/bin/ping -c 4 " . (isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : ''), $output, $return);
                  }
                  if (!$return) {
                      echo "Hey ".$cookie_value.", you are alive!";
                  } else {
                      echo "Oops ".$cookie_value.", you are dead beef :/";
                  }
                }
                ?></b>
