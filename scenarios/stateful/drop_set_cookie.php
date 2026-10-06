<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">Every response sets a fresh <code>sid</code>. Send that cookie back and the page refuses you; keep sending the one you started with and it works.</p>
      <p class="lesson">An application can hand out a cookie it will not accept afterwards - a rotating token, a session pinned elsewhere, a value meant for a different host. A client that dutifully stores every <code>Set-Cookie</code> walks itself out of its own session partway through a scan, and everything after that looks like a target that stopped being vulnerable.</p>
      <p class="reach">Reach it with <code>--drop-set-cookie</code>, so the cookie the response offers is ignored and the one supplied on the command line keeps being sent.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>session_start();
$addr = isset($_GET[&#x27;addr&#x27;]) ? $_GET[&#x27;addr&#x27;] : &#x27;&#x27;;
$sid  = isset($_COOKIE[&#x27;sid&#x27;]) ? $_COOKIE[&#x27;sid&#x27;] : &#x27;&#x27;;
// Offers a new one every time, and rejects any that was taken up.
header(&#x27;Set-Cookie: sid=&#x27;.bin2hex(random_bytes(6)));
if ($sid !== &#x27;&#x27; &amp;&amp; $sid !== &#x27;keepme&#x27;) { die(&#x27;session expired&#x27;); }
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
$__addr = isset($_GET['addr']) ? $_GET['addr'] : '';
$__sid  = isset($_COOKIE['sid']) ? $_COOKIE['sid'] : '';
header('Set-Cookie: sid='.substr(md5(uniqid('', true)), 0, 12));
if ($__sid !== '' && $__sid !== 'keepme') {
  $__result = 'session expired';
} else {
  $__result = $__addr === '' ? '' : shell_exec("/bin/ping -c 4 ".$__addr);
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>A Set-Cookie that breaks the next request &middot; commix-testbed</title>
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
    <h1>A Set-Cookie that breaks the next request</h1>
    <span class="badges"><span class="type t-classic">Classic</span><span class="lang">PHP</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <p>Request it as <code>drop_set_cookie.php?addr=127.0.0.1</code> with a cookie of
                   <code>sid=keepme</code>, and ignore the one it offers back.</p>
                <br>
                <b><?php echo $__result; ?></b>
