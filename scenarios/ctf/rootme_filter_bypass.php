<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">A blacklist strips <code>\ $ | ` ; &amp; &lt; &gt; ]</code> outright before the value reaches <code>ping</code>. That covers the common separators (<code>;</code>, <code>&amp;</code>, <code>|</code>) and, critically, both of POSIX shell's command-substitution syntaxes at once - <code>$(...)</code> needs the <code>$</code>, and backtick command substitution needs the backtick it also strips. A bare newline is not in the list.</p>
      <p class="lesson">A blacklist stops exactly the characters it names. It blocked both ways to nest a command inside another, but a newline still ends one command and starts the next just as well as a semicolon does - and nothing here names it.</p>
      <p class="verdict">Reported by commix as <b>Blind command injection</b> (time-based, with <code>--skip-subst</code>). Based on <a href="https://www.root-me.org/en/Challenges/Web-Server/Command-injection-Filter-bypass">Root-Me: Command injection - Filter bypass</a>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>$ip = isset($_POST[&#x27;ip&#x27;]) ? $_POST[&#x27;ip&#x27;] : &#x27;&#x27;;
$clean = preg_replace(&#x27;/[\\\\$|`;&amp;&lt;&gt;\]]/&#x27;, &#x27;&#x27;, $ip);
if ($clean !== &#x27;&#x27;) {
  echo shell_exec(&quot;ping -c 1 &quot; . $clean . &quot; 2&gt;&amp;1&quot;);
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
// result starts hard left. This page is blind by design - a delay is the only oracle - so nothing
// here colours a "real" answer differently from a reflected one.
(function () {
  var out = document.querySelector(".live > b:last-of-type");
  if (!out) return;
  var lines = out.textContent.split(/\r?\n/);
  var filled = lines.filter(function (l) { return l.trim(); });
  var common = filled.reduce(function (n, l) { return Math.min(n, l.match(/^[ \t]*/)[0].length); }, 1e9);
  if (filled.length) {
    out.textContent = lines.map(function (l) { return l.slice(common); }).join(String.fromCharCode(10)).trim();
  }
  if (!out.textContent.trim()) {
    var submitted = location.search.length > 1 || document.referrer.indexOf(location.pathname) !== -1;
    if (!submitted) { out.remove(); return; }
    out.textContent = "(no output - the command printed nothing to stdout)";
    out.classList.add("silent");
  }
})();
</script>
</body>
</html>
TESTBED_TAIL;
register_shutdown_function(function () use ($__tail) { echo $__tail; });

$__result = '';
$__ip = isset($_POST['ip']) ? $_POST['ip'] : '';
if ($__ip !== '') {
  $__clean = preg_replace('/[\\\\$|`;&<>\]]/', '', $__ip);
  $__result = shell_exec("ping -c 1 " . $__clean . " 2>&1");
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Filter bypass &middot; commix-testbed</title>
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
    <h1>Filter bypass</h1>
    <span class="badges"><span class="type t-blind">Blind</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="rootme_filter_bypass.php" method="POST">
                Ping address: <input type="text" name="ip" size="40">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php echo htmlspecialchars($__result, ENT_QUOTES); ?></b>
