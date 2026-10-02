<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">Mints a fresh <code>csrf_token</code> into the session and returns it as JSON. It answers <code>POST</code> only, and takes a <code>scope</code> field naming what the token is for.</p>
      <p class="lesson">The token service, split out from the page that consumes it - the shape a single-page front end usually has. Nothing here is injectable; it exists because the page that is will not hand you a token.</p>
      <p class="reach">Not a target by itself - it is the endpoint named by <code>--csrf-url</code>, with <code>--csrf-method=POST</code> and <code>--csrf-data</code>.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>session_start();
if ($_SERVER[&#x27;REQUEST_METHOD&#x27;] !== &#x27;POST&#x27;) {
  header(&#x27;Content-Type: application/json&#x27;);
  echo &#x27;{&quot;error&quot;:&quot;POST only&quot;}&#x27;;
} else {
  $scope = isset($_POST[&#x27;scope&#x27;]) ? preg_replace(&#x27;/[^a-z]/i&#x27;, &#x27;&#x27;, $_POST[&#x27;scope&#x27;]) : &#x27;none&#x27;;
  $token = md5(uniqid(mt_rand(), true));
  $_SESSION[&#x27;remote_token&#x27;] = $token;
  header(&#x27;Content-Type: application/json&#x27;);
  echo &#x27;{&quot;scope&quot;:&quot;&#x27;.$scope.&#x27;&quot;,&quot;csrf_token&quot;:&quot;&#x27;.$token.&#x27;&quot;}&#x27;;
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
session_start();
$__scope = isset($_POST['scope']) ? preg_replace('/[^a-z]/i', '', $_POST['scope']) : 'none';
$__result = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $__token = md5(uniqid(mt_rand(), true));
  $_SESSION['remote_token'] = $__token;
  $__result = '{"scope":"'.$__scope.'","csrf_token":"'.$__token.'"}';
} else {
  $__result = '{"error":"POST only"}';
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Token endpoint &middot; commix-testbed</title>
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
    <h1>Token endpoint</h1>
    <span class="badges"><span class="type t-classic">Classic</span><span class="lang">PHP</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <p>A <code>POST</code> here returns a fresh token as JSON:</p>
                <br>
                <b><?php echo htmlspecialchars($__result); ?></b>

