<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The session counts requests. After five without a visit to <a href="keepalive.php">the keepalive page</a>, everything is answered with <code>Session locked.</code> and <code>$addr</code> is never used.</p>
      <p class="lesson">Detection is not one request, it is dozens, so a limit an ordinary user would never reach is hit almost immediately. Everything after the fifth request is refused for a reason that has nothing to do with the payload - so a scan that ignores the limit concludes the parameter is clean on the strength of replies the server was not really answering.</p>
      <p class="reach">Reach it with <code>--safe-url</code> pointing at <code>keepalive.php</code> and <code>--safe-freq=3</code>, so the counter is cleared before it runs out.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>session_start();
$hits = isset($_SESSION[&#x27;hits&#x27;]) ? $_SESSION[&#x27;hits&#x27;] : 0;
$_SESSION[&#x27;hits&#x27;] = ++$hits;
if ($hits &gt; 5) {
  echo &#x27;Session locked.&#x27;;
} else {
  $addr = isset($_GET[&#x27;addr&#x27;]) ? $_GET[&#x27;addr&#x27;] : &#x27;&#x27;;
  echo exec(&quot;/bin/ping -c 4 &quot;.$addr);
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
  if (/invalid|hack attempt|not allowed|no white spaces|is not set|denied|only$|required|locked|duplicate|unavailable|expired|stored/i.test(text)) {
    out.classList.add("refused");
  }
})();
</script>
</body>
</html>
TESTBED_TAIL;
register_shutdown_function(function () use ($__tail) { echo $__tail; });
session_start();
$__hits = isset($_SESSION['hits']) ? $_SESSION['hits'] : 0;
$_SESSION['hits'] = ++$__hits;
$__result = '';
if (isset($_GET['addr'])) {
  if ($__hits > 5) {
    $__result = 'Session locked.';
  } else {
    $__addr = isset($_GET['addr']) ? $_GET['addr'] : '';
    $__result = exec("/bin/ping -c 4 ".$__addr);
  }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Rate-limited endpoint &middot; commix-testbed</title>
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
    <p class="crumb">Stateful &amp; multi-step</p>
    <h1>Rate-limited endpoint</h1>
    <span class="badges"><span class="type t-classic">Classic</span><span class="lang">PHP</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="lockout.php" method="GET">
                Ping address: <input type="text" name="addr">
                <input value="Submit!" type="submit">
                </form>
                <p>Requests used: <?php echo (int)$__hits; ?> of 5 &middot;
                   <a href="keepalive.php">reset the counter</a>.</p>
                <br>
                <b><?php echo $__result; ?></b>

