<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">Only a bare <code>-</code> is rejected. Every separator, every command substitution and every quote still gets through untouched - the filter was written to stop flag injection (<code>--option</code>), not command chaining.</p>
      <p class="lesson">A character blocked because of what it does to <em>one</em> program's own argument parsing (a leading <code>-</code> turning a value into a flag) is still blocked everywhere else it appears in the value - including inside <code>test</code>'s own <code>-eq</code>/<code>-le</code>, which is what a blind technique's own proof-of-execution is built from. The command runs and chains exactly as it would with nothing blocked at all; only the check confirming it did needs a dash-free spelling.</p>
      <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>$addr = isset($_POST[&#x27;addr&#x27;]) ? $_POST[&#x27;addr&#x27;] : &#x27;&#x27;;
if ($addr !== &#x27;&#x27;) {
  if (preg_match(&#x27;/-/&#x27;, $addr)) {
    die(&quot;No flags allowed.&quot;);
  }
  exec(&quot;ping  -c 1 &quot; . $addr, $output, $return);
  echo $return ? &quot;The host seems to be down!&quot; : &quot;The host seems to be up!&quot;;
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
// result starts hard left. This page is blind by design - up/down is the only channel it answers on.
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
    var submitted = location.search.length > 1 || document.referrer.indexOf(location.pathname) !== -1;
    if (!submitted) { out.remove(); return; }
    return;
  }
  if (/no flags allowed/i.test(text)) {
    out.classList.add("refused");
  }
})();
</script>
</body>
</html>
TESTBED_TAIL;
register_shutdown_function(function () use ($__tail) { echo $__tail; });

$__addr = isset($_POST['addr']) ? $_POST['addr'] : '';
$__result = '';
if ($__addr !== '') {
  if (preg_match('/-/', $__addr)) {
    $__result = "No flags allowed.";
  } else {
    exec("ping  -c 1 " . $__addr, $__output, $__return);
    $__result = $__return ? "The host seems to be down!" : "The host seems to be up!";
  }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dash blocked &middot; commix-testbed</title>
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
    <p class="crumb">Weak filters</p>
    <h1>Dash blocked</h1>
    <span class="badges"><span class="type t-blind">Blind</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="no_dash.php" method="POST">
                Ping address: <input type="text" name="addr">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php echo htmlspecialchars($__result, ENT_QUOTES); ?></b>
