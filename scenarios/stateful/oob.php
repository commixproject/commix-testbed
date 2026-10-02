<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The command is detached and its output thrown away - <code>exec(&quot;(/bin/ping -c 4 &quot;.$addr.&quot;) &gt; /dev/null 2&gt;&amp;1 &amp;&quot;)</code>. The page answers <code>Queued.</code> immediately, whatever was sent.</p>
      <p class="lesson">Every signal a scan normally reads is gone at once. The body is a constant, so there is nothing to compare; the process is backgrounded, so a <code>sleep</code> in the payload delays nothing the response waits for. The command still runs - it just cannot say so through this connection. What is left is what the command can reach on its own: a DNS lookup or an HTTP request out of the target is the only evidence that anything executed.</p>
      <p class="reach">Results-based and time-based both fail here by construction. Reach it with <code>--oob</code>. The file-based technique can still work while the web root is writable and guessable - which is exactly the assumption out-of-band removes.</p>
      <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>// Detached and silenced: the response is identical whatever was sent, and never waits for it.
$addr = isset($_GET[&#x27;addr&#x27;]) ? $_GET[&#x27;addr&#x27;] : &#x27;&#x27;;
exec(&quot;(/bin/ping -c 4 &quot;.$addr.&quot;) &gt; /dev/null 2&gt;&amp;1 &amp;&quot;);
echo &#x27;Queued.&#x27;;</code></pre>
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
  var lines = out.textContent.split(/\\r?\\n/);
  var filled = lines.filter(function (l) { return l.trim(); });
  var common = filled.reduce(function (n, l) { return Math.min(n, l.match(/^[ \\t]*/)[0].length); }, 1e9);
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
$__addr = isset($_GET['addr']) ? $_GET['addr'] : '';
$__result = '';
if (isset($_GET['addr'])) {
  exec("(/bin/ping -c 4 ".$__addr.") > /dev/null 2>&1 &");
  $__result = 'Queued.';
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Out-of-band only &middot; commix-testbed</title>
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
    <h1>Out-of-band only</h1>
    <span class="badges"><span class="type t-blind">Blind</span><span class="lang">PHP</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="oob.php" method="GET">
                Ping address: <input type="text" name="addr">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php echo $__result; ?></b>

