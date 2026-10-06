<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The <code>Cookie</code> header is not read by PHP. It is split on <code>|</code> instead of <code>;</code>, so <code>addr=127.0.0.1|lang=en</code> is two cookies rather than one value containing a pipe.</p>
      <p class="lesson">The same disagreement as a hand-rolled query string, moved into a header. A tool splitting cookies on <code>;</code> sees one long value and tests it whole, while the application reads two - so the cookie that reaches the shell is never tested by itself.</p>
      <p class="reach">Reach it with <code>--cookie-del=&quot;|&quot;</code> at <code>--level=2</code>, so the header is split the way the application splits it - and <code>--skip-urlencode</code>, since a cookie value is encoded by default and the separator has to arrive as itself.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>// Parsed by hand, on &#x27;|&#x27; rather than &#x27;;&#x27;.
$raw = isset($_SERVER[&#x27;HTTP_COOKIE&#x27;]) ? $_SERVER[&#x27;HTTP_COOKIE&#x27;] : &#x27;&#x27;;
$jar = array();
foreach (explode(&#x27;|&#x27;, $raw) as $pair) {
  $kv = explode(&#x27;=&#x27;, $pair, 2);
  if (count($kv) === 2) { $jar[trim($kv[0])] = $kv[1]; }
}
$addr = isset($jar[&#x27;addr&#x27;]) ? $jar[&#x27;addr&#x27;] : &#x27;&#x27;;
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
$__raw = isset($_SERVER['HTTP_COOKIE']) ? $_SERVER['HTTP_COOKIE'] : '';
$__jar = array();
foreach (explode('|', $__raw) as $__pair) {
  $__kv = explode('=', $__pair, 2);
  if (count($__kv) === 2) { $__jar[trim($__kv[0])] = $__kv[1]; }
}
$__addr = isset($__jar['addr']) ? $__jar['addr'] : '';
$__result = $__addr === '' ? '' : shell_exec("/bin/ping -c 4 ".$__addr);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Non-standard cookie delimiter &middot; commix-testbed</title>
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
    <h1>Non-standard cookie delimiter</h1>
    <span class="badges"><span class="type t-classic">Classic</span><span class="lang">PHP</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <p>Send it a cookie of <code>addr=127.0.0.1|lang=en</code> - the two are split
                   on <code>|</code>.</p>
                <p>Parsed: addr=<code><?php echo htmlspecialchars($__addr); ?></code></p>
                <br>
                <b><?php echo $__result; ?></b>
