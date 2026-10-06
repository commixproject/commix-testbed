<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">A filter rejects the request outright if <code>code</code> contains <b>any letter</b> (<code>A-Za-z</code>). Digits, <code>$</code>, <code>_</code>, quotes, backticks and every other symbol pass through untouched, and the rest is handed straight to <code>eval()</code>.</p>
      <p class="lesson">A letter never has to arrive as itself. PHP decodes a double-quoted or backtick string's own octal escapes (<code>\NNN</code>) only once <code>eval()</code> is already running it - by then the filter has long since looked at, and passed, four digits and a backslash. A function reached by name built the same way (<code>call_user_func</code>, itself spelled in octal) never writes <code>system</code> or <code>exec</code> either.</p>
      <p class="verdict">Reported by commix as <b>Classic code injection</b>. Based on <a href="https://www.root-me.org/en/Challenges/Web-Server/PHP-Eval-hard">Root-Me: PHP - Eval-hard</a>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>$code = isset($_GET[&#x27;code&#x27;]) ? $_GET[&#x27;code&#x27;] : &#x27;&#x27;;
if ($code !== &#x27;&#x27;) {
  if (preg_match(&#x27;/[a-zA-Z]/&#x27;, $code)) {
    echo &quot;Nice try...&quot;;
  } else {
    eval(&quot;\$result = &quot; . $code . &quot;;&quot;);
    echo $result;
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
  if (/nice try|blocked|invalid|hack attempt|not allowed|denied/i.test(text)) {
    out.classList.add("refused");
  }
})();
</script>
</body>
</html>
TESTBED_TAIL;
register_shutdown_function(function () use ($__tail) { echo $__tail; });

$__code = isset($_GET['code']) ? $_GET['code'] : '';
$__result = '';
if ($__code !== '') {
  if (preg_match('/[a-zA-Z]/', $__code)) {
    $__result = "Nice try...";
  } else {
    eval("\$result = " . $__code . ";");
    $__result = $result;
  }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PHP Eval-hard &middot; commix-testbed</title>
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
    <h1>PHP Eval-hard</h1>
    <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="rootme_eval_hard.php" method="GET">
                Code: <input type="text" name="code" size="40">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php echo htmlspecialchars($__result, ENT_QUOTES); ?></b>
