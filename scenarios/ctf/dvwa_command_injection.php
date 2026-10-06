<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">A blacklist strips <code>&amp; ; - $ ( ) `</code>, <code>||</code> and <code>| </code> (a pipe followed by a space) out of the value before it reaches <code>ping</code> - stripped by exact substring, not by character class, and nothing here reads a bare newline at all.</p>
      <p class="lesson">The blacklist matches <code>"| "</code> as one two-character string, not the character <code>|</code> on its own - a pipe with no space right after it, or one immediately followed by a redirect instead of an argument, never matches that entry. A newline chains a second command exactly as well as a semicolon does, and nothing here names it either. The harder edge: <code>-</code> is stripped everywhere, which breaks <code>test</code>'s own <code>-eq</code>/<code>-ne</code> - the check that tells a real answer from noise reads it as <code>test 20 eq 20</code>, a syntax error - and <code>$</code> is stripped too, which is what most techniques use to read a command's output back at all.</p>
      <p class="reach">Reach it with <code>--skip-subst --skip-calc --tamper=dashlesstest --technique=f --web-root=&lt;document root&gt;</code> - the first three keep detection to a dash-free, dollar-free <code>test</code>/<code>sleep</code> check, and file-based needs no <code>$</code> or <code>-</code> of its own to write a command's output where a plain GET can read it back.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>. Based on <a href="https://github.com/digininja/DVWA">DVWA: Command Injection</a> (security level "high").</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>$target = trim($_REQUEST[&#x27;ip&#x27;]);

$substitutions = array(
  &#x27;&amp;&#x27;  =&gt; &#x27;&#x27;,
  &#x27;;&#x27;  =&gt; &#x27;&#x27;,
  &#x27;| &#x27; =&gt; &#x27;&#x27;,
  &#x27;-&#x27;  =&gt; &#x27;&#x27;,
  &#x27;$&#x27;  =&gt; &#x27;&#x27;,
  &#x27;(&#x27;  =&gt; &#x27;&#x27;,
  &#x27;)&#x27;  =&gt; &#x27;&#x27;,
  &#x27;`&#x27;  =&gt; &#x27;&#x27;,
  &#x27;||&#x27; =&gt; &#x27;&#x27;,
);
$target = str_replace(array_keys($substitutions), $substitutions, $target);

$cmd = shell_exec(&#x27;ping  -c 4 &#x27; . $target);</code></pre>
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
$__target = isset($_REQUEST['ip']) ? trim($_REQUEST['ip']) : '';
if ($__target !== '') {
  $__substitutions = array(
    '&'  => '',
    ';'  => '',
    '| ' => '',
    '-'  => '',
    '$'  => '',
    '('  => '',
    ')'  => '',
    '`'  => '',
    '||' => '',
  );
  $__target = str_replace(array_keys($__substitutions), $__substitutions, $__target);
  $__result = shell_exec('ping  -c 4 ' . $__target);
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>DVWA: Command Injection &middot; commix-testbed</title>
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
    <h1>DVWA: Command Injection</h1>
    <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="dvwa_command_injection.php" method="POST">
                IP address: <input type="text" name="ip" size="40">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php echo htmlspecialchars($__result, ENT_QUOTES); ?></b>
