<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain"><code>PreProd_Sanitize()</code> rejects the request outright if <code>cmd</code> contains <b>any letter</b> (<code>A-Za-z</code>) or any of <code>_*^@%+=:,./-</code>. Digits, <code>$</code>, <code>\</code>, <code>'</code>, whitespace and the common separators (<code>;</code>, <code>&amp;</code>, <code>|</code>) all pass through untouched.</p>
      <p class="lesson">A filter built to block letters and punctuation still leaves every shell metacharacter that matters for chaining a command: <code>;</code>, <code>&amp;</code>, <code>|</code> and <code>$</code>. A command name is just letters spelled another way - ANSI-C quoting (<code>$'\NNN...'</code>) spells them as octal escapes instead, none of which this filter's letter rule ever sees.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>. Based on <a href="https://www.yeswehack.com/dojo/dojo-ctf-challenge-winners-36">YesWeHack Dojo #36: Shell Escape</a>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>function PreProd_Sanitize($input) {
  if (preg_match(&#x27;/[A-Za-z]/&#x27;, $input)) {
    return false;
  }
  if (preg_match(&#x27;/[_*^@%+=:,.\/-]/&#x27;, $input)) {
    return false;
  }
  return true;
}

$cmd = isset($_GET[&#x27;cmd&#x27;]) ? $_GET[&#x27;cmd&#x27;] : &#x27;&#x27;;
if ($cmd !== &#x27;&#x27;) {
  if (!PreProd_Sanitize($cmd)) {
    echo &quot;Blocked by PreProd_Sanitize filter&quot;;
  } else {
    // The target runs bash, not the default /bin/sh - which is why ANSI-C quoting works here.
    echo shell_exec(&quot;/bin/bash -c &quot; . escapeshellarg($cmd) . &quot; 2&gt;&amp;1&quot;);
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
    var submitted = location.search.length > 1 || document.referrer.indexOf(location.pathname) !== -1;
    if (!submitted) { out.remove(); return; }
    out.textContent = "(no output - the command printed nothing to stdout)";
    out.classList.add("silent");
    return;
  }
  if (/blocked|invalid|hack attempt|not allowed|denied/i.test(text)) {
    out.classList.add("refused");
  }
})();
</script>
</body>
</html>
TESTBED_TAIL;
register_shutdown_function(function () use ($__tail) { echo $__tail; });

function PreProd_Sanitize($input) {
  if (preg_match('/[A-Za-z]/', $input)) {
    return false;
  }
  if (preg_match('/[_*^@%+=:,.\/-]/', $input)) {
    return false;
  }
  return true;
}

$__result = '';
$__cmd = isset($_GET['cmd']) ? $_GET['cmd'] : '';
if ($__cmd !== '') {
  if (!PreProd_Sanitize($__cmd)) {
    $__result = "Blocked by PreProd_Sanitize filter";
  } else {
    $__result = shell_exec("/bin/bash -c " . escapeshellarg($__cmd) . " 2>&1");
  }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Shell escape &middot; commix-testbed</title>
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
    <p class="crumb">CTF challenges</p>
    <h1>Shell escape</h1>
    <span class="badges"><span class="type t-classic">Classic</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="dojo36_shell_escape.php" method="GET">
                Command: <input type="text" name="cmd" size="40">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php echo htmlspecialchars($__result, ENT_QUOTES); ?></b>
