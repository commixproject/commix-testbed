<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The same header injection with no output reflected. Unix-only.</p>
      <p class="lesson">The Referer sink with no output reflected.</p>
      <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>if( stristr(php_uname(&#x27;s&#x27;), &#x27;Windows NT&#x27;)){
  die(&quot;Invalid operating system.&quot;);
} else {
  $server_name = (isset($_SERVER[&quot;SERVER_NAME&quot;]) ? $_SERVER[&quot;SERVER_NAME&quot;] : &#x27;&#x27;);
  $referer = (isset($_SERVER[&#x27;HTTP_REFERER&#x27;]) ? $_SERVER[&#x27;HTTP_REFERER&#x27;] : &#x27;&#x27;);
  exec(&quot;echo &#x27;&quot;.$referer.&quot;&#x27; | grep &#x27;&quot;.$server_name.&quot;&#x27;&quot;, $output, $return);
}
if (!$return) {
    echo &quot;Welcome to &quot;.$server_name.&quot;!&quot;;
}else{
    echo &quot;Hey, what are you trying to do?!&quot;;
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
<title>Referer header - Nothing returned &middot; commix-testbed</title>
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
    <p class="crumb">Referer header</p>
    <h1>Referer header - Nothing returned</h1>
    <span class="badges"><span class="type t-blind">Blind</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <div class="howto"><p>There is nothing to type here: the value is taken from the <b>Referer</b> request header. Change that header and reload.</p><pre><code>GET /scenarios/referer/referer(blind).php
Referer: https://commixproject.com/</code></pre></div><br>
                <b><?php
                if( stristr(php_uname('s'), 'Windows NT')){
                  die("Invalid operating system.");
                } else {
                  $server_name = (isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : '');
                  $referer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
                  exec("echo '".$referer."' | grep '".$server_name."'", $output, $return);
                }
                if (!$return) {
                    echo "Welcome to ".$server_name."!";
                }else{
                    echo "Hey, what are you trying to do?!";
                }
                ?></b>
