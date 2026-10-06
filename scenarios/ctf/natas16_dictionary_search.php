<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">A filter rejects the request if <code>needle</code> contains <code>; | &amp; ` ' "</code>. Digits, letters, <code>$</code>, <code>(</code>, <code>)</code> and <code>&gt;</code> all pass through untouched, and the value is handed to <code>grep -i "needle" dictionary.txt</code> inside its own double quotes - so a value like <code>cat</code> just searches the wordlist, but the quotes only stop a value from ending the argument early, not from being expanded before <code>grep</code> ever sees it.</p>
      <p class="lesson">Bash expands <code>$(...)</code> inside double quotes before the command it sits in ever runs - the filter blocks every way to chain a second command, but not the one syntax that runs one inside the first command's own argument. The substitution's output becomes part of what <code>grep</code> searches for, which answers nothing directly - unless the command redirects its own output to a file instead of returning it, in which case the substitution itself yields nothing (an empty search, matching most of the wordlist) while the real answer waits on disk under a name the request already knows.</p>
      <p class="verdict">Reported by commix as <b>Blind command injection</b>. Based on <a href="https://overthewire.org/wargames/natas/natas16.html">OverTheWire: Natas Level 16</a>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>$key = &quot;&quot;;
if (array_key_exists(&quot;needle&quot;, $_REQUEST)) {
  $key = $_REQUEST[&quot;needle&quot;];
}
if ($key != &quot;&quot;) {
  if (preg_match(&#x27;/[;|&amp;`\&#x27;&quot;]/&#x27;, $key)) {
    print &quot;Input contains an illegal character!&quot;;
  } else {
    passthru(&quot;grep -i \&quot;$key\&quot; dictionary.txt&quot;);
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
// result starts hard left. This page is blind by design for anything beyond the wordlist itself -
// a search hit or miss is the only channel a normal visit ever sees.
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
    out.textContent = "(no match in the wordlist)";
    out.classList.add("silent");
    return;
  }
  if (/illegal character/i.test(text)) {
    out.classList.add("refused");
  }
})();
</script>
</body>
</html>
TESTBED_TAIL;
register_shutdown_function(function () use ($__tail) { echo $__tail; });

$__key = "";
if (isset($_GET['needle'])) {
  $__key = $_GET['needle'];
}
$__result = '';
if ($__key !== '') {
  if (preg_match('/[;|&`\'"]/', $__key)) {
    $__result = "Input contains an illegal character!";
  } else {
    // passthru(), exactly as the real challenge runs it - captured only so this page can lay the
    // result out the same way every other scenario here does.
    ob_start();
    passthru("grep -i \"" . $__key . "\" " . __DIR__ . "/natas16_dictionary.txt");
    $__result = ob_get_clean();
  }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Natas16: dictionary search &middot; commix-testbed</title>
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
    <h1>Natas16: dictionary search</h1>
    <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="natas16_dictionary_search.php" method="GET">
                Find words containing: <input type="text" name="needle" size="40">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php echo htmlspecialchars($__result, ENT_QUOTES); ?></b>
