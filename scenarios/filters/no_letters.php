<?php
$addr = isset($_POST['addr']) ? $_POST['addr'] : '';
if ($addr !== '') {
  if (preg_match('/[a-zA-Z]/', $addr)) {
    die("No letters allowed.");
  }
}

ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The value is rejected if it contains a single letter, upper or lower case - every digit, space, separator and quoting character still passes through untouched. A payload built entirely out of commix's own usual words (<code>echo</code>, <code>sleep</code>, a hex tag) is blocked before it ever reaches <code>exec()</code>, which runs it through <code>bash</code> rather than the target's default shell.</p>
      <p class="lesson">ANSI-C quoting spells a word out as <code>$'\NNN\NNN...'</code> - one octal escape per character, each already nothing but digits and a backslash. Nothing here checks what a quoted span decodes to, only whether a letter appears in the request as written, so <code>$'\163\154\145\145\160'</code> reaches the target as <code>sleep</code> without one. A strict POSIX shell without this quoting (e.g. <code>dash</code>) leaves only letter-free filename globbing to fall back on - <code>?</code> matching any one character - which still has to land on a command name no other file of the same length shares.</p>
      <details class="solve">
        <summary>Hint</summary>
        <table class="solve-glossary">
          <tr><td><code>--tamper=ansiquoteoctal</code></td><td>spells command names in ANSI-C octal escapes, for a filter blocking every letter</td></tr>
        </table>
      </details>
      <p class="verdict">Identified by commix as <b><a href="https://github.com/commixproject/commix/wiki/Techniques#1-results-based-classic-command-injection">Classic command injection</a></b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>$addr = isset($_POST[&#x27;addr&#x27;]) ? $_POST[&#x27;addr&#x27;] : &#x27;&#x27;;
if ($addr !== &#x27;&#x27;) {
  if (preg_match(&#x27;/[a-zA-Z]/&#x27;, $addr)) {
    die(&quot;No letters allowed.&quot;);
  }
  echo exec(&quot;/bin/bash -c &quot; . escapeshellarg(&quot;ping -c 1 &quot; . $addr));
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
    var submitted = location.search.length > 1 || document.referrer.indexOf(location.pathname) !== -1;
    if (!submitted) { out.remove(); return; }
    return;
  }
  if (/no letters allowed/i.test(text)) {
    out.classList.add("refused");
  }
})();
</script>
</body>
</html>
TESTBED_TAIL;
register_shutdown_function(function () use ($__tail) { echo $__tail; });

$__result = '';
$__addr = isset($_POST['addr']) ? $_POST['addr'] : '';
if ($__addr !== '') {
  if (preg_match('/[a-zA-Z]/', $__addr)) {
    $__result = "No letters allowed.";
  } else {
    $__result = exec("/bin/bash -c " . escapeshellarg("ping -c 1 " . $__addr));
  }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>No letters allowed &middot; commix-testbed</title>
<meta name="robots" content="noindex">
<link rel="shortcut icon" href="../../favicon.ico">
<link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../css/testbed.css?v=3">
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
    <h1>No letters allowed</h1>
    <span class="badges"><span class="type t-classic">Classic</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="no_letters.php" method="POST">
                Ping address: <input type="text" name="addr">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php echo htmlspecialchars($__result, ENT_QUOTES); ?></b>
