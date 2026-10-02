<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">A plain login form. <code>admin</code> / <code>testbed</code> sets a session flag; nothing here is injectable.</p>
      <p class="lesson">The page exists so that something behind it can be reached. Form-based login is the common case that HTTP Basic and Digest are not: there is no header to replay, the credential is exchanged once for a session cookie, and a scan that never logs in sees only the login page.</p>
      <p class="reach">Not a target by itself - it is the page named by <code>--auth-url</code>, with <code>--auth-data</code> carrying the credentials.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>session_start();
if (isset($_POST[&#x27;username&#x27;]) &amp;&amp; isset($_POST[&#x27;password&#x27;])) {
  if ($_POST[&#x27;username&#x27;] === &#x27;admin&#x27; &amp;&amp; $_POST[&#x27;password&#x27;] === &#x27;testbed&#x27;) {
    $_SESSION[&#x27;auth&#x27;] = true;
    echo &#x27;Logged in.&#x27;;
  } else {
    echo &#x27;Login failed.&#x27;;
  }
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
$__result = '';
if (isset($_POST['username']) && isset($_POST['password'])) {
  if ($_POST['username'] === 'admin' && $_POST['password'] === 'testbed') {
    $_SESSION['auth'] = true;
    $__result = 'Logged in.';
  } else {
    $__result = 'Login failed.';
  }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Session login &middot; commix-testbed</title>
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
    <h1>Session login</h1>
    <span class="badges"><span class="type t-classic">Classic</span><span class="lang">PHP</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="login.php" method="POST">
                Username: <input type="text" name="username">
                Password: <input type="password" name="password">
                <input value="Log in" type="submit">
                </form>
                <p>Once logged in, <a href="session_classic.php?addr=127.0.0.1">the protected page</a> becomes reachable.</p>
                <br>
                <b><?php echo $__result; ?></b>

