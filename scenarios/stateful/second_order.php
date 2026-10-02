<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The submit page stores <code>addr</code> and answers <code>Stored.</code> - it never runs the value and never echoes it back. The command runs later, when <a href="second_order_log.php">the log viewer</a> renders what was stored.</p>
      <p class="lesson">Injection and execution do not have to happen in the same request. Everything a scanner normally reads - the response body, its length, how long it took - is identical whether the payload was dangerous or harmless, because this page genuinely does nothing with it. The only evidence is on another page, which is why a second-order bug survives scanners that test one request at a time.</p>
      <p class="reach">Reach it with <code>--second-url</code> pointing at <code>second_order_log.php</code>, so the response to look at is fetched from there.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>// Stored now, executed later - this request produces no observable difference.
$store = sys_get_temp_dir().&#x27;/testbed_second_order.txt&#x27;;
$addr  = isset($_POST[&#x27;addr&#x27;]) ? $_POST[&#x27;addr&#x27;] : &#x27;&#x27;;
if ($addr !== &#x27;&#x27;) {
  file_put_contents($store, $addr);
  echo &#x27;Stored.&#x27;;
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
$__store = sys_get_temp_dir().'/testbed_second_order.txt';
$__addr  = isset($_POST['addr']) ? $_POST['addr'] : '';
$__result = '';
if ($__addr !== '') {
  @file_put_contents($__store, $__addr);
  $__result = 'Stored.';
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Second-order storage &middot; commix-testbed</title>
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
    <h1>Second-order storage</h1>
    <span class="badges"><span class="type t-classic">Classic</span><span class="lang">PHP</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="second_order.php" method="POST">
                Ping address: <input type="text" name="addr">
                <input value="Submit!" type="submit">
                </form>
                <p>Stored addresses are resolved by the <a href="second_order_log.php">log viewer</a>.</p>
                <br>
                <b><?php echo $__result; ?></b>

