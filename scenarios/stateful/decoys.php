<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">Four fields arrive together. One is always echoed back unchanged whatever is sent, one is always empty, one is ignored outright - and one reaches the shell.</p>
      <p class="lesson">Most of a real form is noise. Testing every field costs requests and, on a target that rate-limits or locks out, costs the run - so knowing which fields cannot be the answer is worth as much as knowing which can.</p>
      <p class="reach">Reach it with <code>--skip-static</code> and <code>--skip-empty</code>, which leave the fields that never change and the ones that arrive empty untested.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>$addr   = isset($_GET[&#x27;addr&#x27;])   ? $_GET[&#x27;addr&#x27;]   : &#x27;&#x27;;   // reaches the shell
$fixed  = isset($_GET[&#x27;fixed&#x27;])  ? $_GET[&#x27;fixed&#x27;]  : &#x27;&#x27;;   // answer never changes
$blank  = isset($_GET[&#x27;blank&#x27;])  ? $_GET[&#x27;blank&#x27;]  : &#x27;&#x27;;   // always empty
echo &quot;status: ok&quot;;                                 // $fixed makes no difference
echo shell_exec(&quot;/bin/ping -c 4 &quot;.$addr);</code></pre>
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
$__addr  = isset($_GET['addr'])  ? $_GET['addr']  : '';
$__fixed = isset($_GET['fixed']) ? $_GET['fixed'] : '';
$__blank = isset($_GET['blank']) ? $_GET['blank'] : '';
$__result = $__addr === '' ? '' : shell_exec("/bin/ping -c 4 ".$__addr);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Decoy parameters around the real one &middot; commix-testbed</title>
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
    <h1>Decoy parameters around the real one</h1>
    <span class="badges"><span class="type t-classic">Classic</span><span class="lang">PHP</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <p>Request it as <code>decoys.php?addr=127.0.0.1&amp;fixed=anything&amp;blank=</code>.</p>
                <p>status: ok <span class="muted">(this line is the same whatever <code>fixed</code> carries)</span></p>
                <br>
                <b><?php echo $__result; ?></b>
