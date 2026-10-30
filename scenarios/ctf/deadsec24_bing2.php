<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain"><code>bing2_filter()</code> removes the literal strings <code>' '</code>, <code>;</code>, <code>&amp;</code>, <code>flag</code>, <code>cat</code> and <code>ls</code> wherever they appear in the value, then whatever is left is run through <code>/bin/bash -c</code>.</p>
      <p class="lesson">A filter built to stop one command chaining into another, and to stop the obvious ones from being typed at all, still lets through every substitute the shell itself treats as equivalent: a semicolon is not the only way to run a second command, and a space is not the only thing a shell reads as a separator - commix never needed either one to begin with. It reads literal strings too - it has nothing to say about a glob, so a filename that never contains the word "flag" can still expand into one, the way the original challenge's own solution did by hand.</p>
      <details class="solve">
        <summary>Hint</summary>
        <p>No extra flags needed.</p>
      </details>
      <p class="verdict">Identified by commix as <b><a href="https://github.com/commixproject/commix/wiki/Techniques#1-results-based-classic-command-injection">Classic command injection</a></b>. Based on <a href="https://jonason0592.substack.com/p/deadsec-ctf-2024-web-writeup">DeadSec CTF 2024: Bing2</a>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>function bing2_filter($input) {
  $blocked = [&#x27; &#x27;, &#x27;;&#x27;, &#x27;&amp;&#x27;, &#x27;flag&#x27;, &#x27;cat&#x27;, &#x27;ls&#x27;];
  return str_replace($blocked, &#x27;&#x27;, $input);
}

$cmd = isset($_GET[&#x27;cmd&#x27;]) ? $_GET[&#x27;cmd&#x27;] : &#x27;&#x27;;
if ($cmd !== &#x27;&#x27;) {
  $clean = bing2_filter($cmd);
  echo shell_exec(&quot;/bin/bash -c &quot; . escapeshellarg($clean) . &quot; 2&gt;&amp;1&quot;);
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

function bing2_filter($input) {
  $blocked = [' ', ';', '&', 'flag', 'cat', 'ls'];
  return str_replace($blocked, '', $input);
}

$__result = '';
$__cmd = isset($_GET['cmd']) ? $_GET['cmd'] : '';
if ($__cmd !== '') {
  $__clean = bing2_filter($__cmd);
  $__result = shell_exec("/bin/bash -c " . escapeshellarg($__clean) . " 2>&1");
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Bing2 &middot; commix-testbed</title>
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
    <p class="crumb">CTF challenges</p>
    <h1>Bing2</h1>
    <span class="badges"><span class="type t-classic">Classic</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="deadsec24_bing2.php" method="GET">
                Command: <input type="text" name="cmd" size="40">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php echo htmlspecialchars($__result, ENT_QUOTES); ?></b>
