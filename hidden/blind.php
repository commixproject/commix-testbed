<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">A blind page - the command runs, nothing about the result comes back. What is different is where it lives: the URL <code>/hidden/blind.php</code> is an Apache <code>Alias</code> to <code>/srv/hidden</code>, which is nowhere under the document root the rest of the site is served from.</p>
      <p class="lesson">The file-based technique writes its output into the web root and reads it back over HTTP, which requires knowing where on disk the URL space actually begins. That is normally guessable from the URL - and here the guess is wrong, because one path segment is an alias to somewhere else entirely. Aliases, user directories and per-vhost roots are ordinary, so the mapping is worth treating as something to be told rather than inferred.</p>
      <p class="reach">Reach it with <code>--technique=f --web-root=/srv/hidden</code>. Guessing the root from the URL lands in the wrong directory and the written file is never served back.</p>
      <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>// Served from /srv/hidden via an Apache Alias, not from the site document root.
$addr = isset($_GET[&#x27;addr&#x27;]) ? $_GET[&#x27;addr&#x27;] : &#x27;&#x27;;
exec(&quot;/bin/ping -c 4 &quot;.$addr);
echo &#x27;Done.&#x27;;</code></pre>
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
  exec("/bin/ping -c 4 ".$__addr);
  $__result = 'Done.';
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Aliased document root &middot; commix-testbed</title>
<meta name="robots" content="noindex">
<link rel="shortcut icon" href="/favicon.ico">
<link href="https://fonts.googleapis.com/css?family=Exo+2:400,500,600,700" rel="stylesheet">
<link rel="stylesheet" href="/css/testbed.css">
</head>
<body>

<nav class="nav">
  <div class="wrap">
    <a class="brand" href="/index.php">
      <img src="/img/testbed_header_logo.png" alt="commix-testbed"></a>
    <a class="back" href="/index.php">&larr; All scenarios</a>
  </div>
</nav>

<div class="wrap page">

  <header class="sc-head">
    <p class="crumb">Stateful &amp; multi-step</p>
    <h1>Aliased document root</h1>
    <span class="badges"><span class="type t-blind">Blind</span><span class="lang">PHP</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="blind.php" method="GET">
                Ping address: <input type="text" name="addr">
                <input value="Submit!" type="submit">
                </form>
                <p>This page is served from <code>/srv/hidden</code>, aliased to <code>/hidden/</code>.</p>
                <br>
                <b><?php echo $__result; ?></b>

