<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The command runs and its output is thrown away. All the page says is whether it succeeded - <code>Host is up</code> or <code>Host is down</code>, taken from the exit status.</p>
      <p class="lesson">Blind, but not silent. Nothing of the output comes back, so there is nothing to read a result out of - yet the page still answers one question about what happened, and a question with a yes and a no is all a search needs. Chain a test onto the command and its exit status becomes the answer: <code>[ 119 -le $ordinal ]</code> reads a byte without the target ever printing one.</p>
      <p class="reach">Reach it with <code>--string</code>, which says what a true answer looks like. Anchor it to the element the result is printed into rather than to the words alone - this page explains itself, so the words of the answer appear in the prose beside it, and a pattern matching only those matches every response. The delay-based technique reaches it as well, and pays a sleep for every question instead.</p>
      <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>$addr = isset($_GET[&#x27;addr&#x27;]) ? $_GET[&#x27;addr&#x27;] : &#x27;&#x27;;
// The output is never printed - only whether the command succeeded.
exec(&quot;/bin/ping -c 1 &quot;.$addr.&quot; 2&gt;/dev/null&quot;, $output, $rc);
echo $rc === 0 ? &quot;Host is up&quot; : &quot;Host is down&quot;;</code></pre>
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
    var submitted = location.search.length > 1 || document.referrer.indexOf(location.pathname) !== -1;
    if (!submitted) { out.remove(); return; }
    out.textContent = "(no output - the command printed nothing to stdout)";
    out.classList.add("silent");
    return;
  }
  if (/is down$/i.test(text)) {
    out.classList.add("refused");
  }
})();
</script>
</body>
</html>
TESTBED_TAIL;
register_shutdown_function(function () use ($__tail) { echo $__tail; });
$__addr = isset($_GET['addr']) ? $_GET['addr'] : '';
$__result = '';
if ($__addr !== '') {
  exec("/bin/ping -c 1 ".$__addr." 2>/dev/null", $__output, $__rc);
  $__result = $__rc === 0 ? "Host is up" : "Host is down";
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Blind, but the page says whether it worked &middot; commix-testbed</title>
<meta name="robots" content="noindex">
<link rel="shortcut icon" href="../../../favicon.ico">
<link href="https://fonts.googleapis.com/css?family=Exo+2:400,500,600,700" rel="stylesheet">
<link rel="stylesheet" href="../../../css/testbed.css">
</head>
<body>

<nav class="nav">
  <div class="wrap">
    <a class="brand" href="../../../index.php">
      <img src="../../../img/testbed_header_logo.png" alt="commix-testbed"></a>
    <a class="back" href="../../../index.php">&larr; All scenarios</a>
  </div>
</nav>

<div class="wrap page">

  <header class="sc-head">
    <p class="crumb">Regular parameters</p>
    <h1>Blind, but the page says whether it worked</h1>
    <span class="badges"><span class="type t-blind">Blind</span><span class="lang">PHP</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="blind_exit_status.php" method="GET">
              Ping address: <input type="text" name="addr">
              <input value="Submit!" type="submit">
              </form>
                <br>
                <b><?php echo $__result; ?></b>
