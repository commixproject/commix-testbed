<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">Every render mints a fresh <code>csrf_token</code> into the session and writes it into the form as a hidden field. A POST whose token does not match the one currently in the session is refused before <code>$addr</code> reaches the shell.</p>
      <p class="lesson">The token is not a fix for the injection - the bug underneath is the same unquoted concatenation as everywhere else. What it changes is what a scan sees: replay a captured token and every payload comes back refused, which is indistinguishable from a parameter that simply is not injectable. The token has to be re-read from the page before each request, which is what makes this worth its own scenario.</p>
      <p class="reach">Reach it with <code>--csrf-token=&quot;csrf_token&quot;</code>, so the current token is fetched again before every request.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>session_start();
// A token that is only good once: the next render mints a new one, so a replayed value is stale.
$expected = isset($_SESSION[&#x27;csrf_token&#x27;]) ? $_SESSION[&#x27;csrf_token&#x27;] : &#x27;&#x27;;
$sent     = isset($_POST[&#x27;csrf_token&#x27;]) ? $_POST[&#x27;csrf_token&#x27;] : &#x27;&#x27;;
$addr     = isset($_POST[&#x27;addr&#x27;]) ? $_POST[&#x27;addr&#x27;] : &#x27;&#x27;;
if ($_SERVER[&#x27;REQUEST_METHOD&#x27;] === &#x27;POST&#x27;) {
  if ($sent === &#x27;&#x27; || $sent !== $expected) {
    echo &#x27;Invalid CSRF token.&#x27;;
  } else {
    echo exec(&quot;/bin/ping -c 4 &quot;.$addr);
  }
}
$_SESSION[&#x27;csrf_token&#x27;] = bin2hex(openssl_random_pseudo_bytes(16));</code></pre>
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
$__expected = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';
$__sent     = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
$__addr     = isset($_POST['addr']) ? $_POST['addr'] : '';
$__result   = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($__sent === '' || $__sent !== $__expected) {
    $__result = 'Invalid CSRF token.';
  } else {
    $__result = exec("/bin/ping -c 4 ".$__addr);
  }
}
// Rotated after the check, so the token in the form below is never the one just used.
$__token = bin2hex(md5(uniqid(mt_rand(), true)));
$_SESSION['csrf_token'] = $__token;
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Anti-CSRF token &middot; commix-testbed</title>
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
    <h1>Anti-CSRF token</h1>
    <span class="badges"><span class="type t-classic">Classic</span><span class="lang">PHP</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="csrf.php" method="POST">
                Ping address: <input type="text" name="addr">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($__token); ?>">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php echo $__result; ?></b>

