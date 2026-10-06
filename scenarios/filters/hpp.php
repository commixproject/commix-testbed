<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The check reads the <b>first</b> <code>addr</code> in the query string. PHP hands the application the <b>last</b> one.</p>
      <p class="lesson">A filter and the application behind it do not have to agree about which of two same-named parameters counts, and here they do not: the check walks the raw query string and stops at the first match, while PHP keeps the last. Nothing about the check is wrong on its own - it simply inspects a different value from the one that runs.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>// The filter walks the raw query string and stops at the first 'addr'.
$first = &#x27;&#x27;;
foreach (explode(&#x27;&amp;&#x27;, $_SERVER[&#x27;QUERY_STRING&#x27;]) as $pair) {
  if (strpos($pair, &#x27;addr=&#x27;) === 0) { $first = urldecode(substr($pair, 5)); break; }
}
if (preg_match(&#x27;/[;&amp;|`$()]/&#x27;, $first)) { die(&quot;Blocked by input filter.&quot;); }

// PHP keeps the last one, and that is what reaches the shell.
$addr = isset($_GET[&#x27;addr&#x27;]) ? $_GET[&#x27;addr&#x27;] : &#x27;&#x27;;
echo shell_exec(&quot;/bin/ping -c 4 &quot;.$addr.&quot; 2&gt;&amp;1&quot;);</code></pre>
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
  if (/invalid|hack attempt|not allowed|no white spaces|is not set|denied|only$/i.test(text)) {
    out.classList.add("refused");
  }
})();
</script>
</body>
</html>
TESTBED_TAIL;
register_shutdown_function(function () use ($__tail) { echo $__tail; });
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>A filter that reads a different parameter &middot; commix-testbed</title>
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
    <p class="crumb">Weak filters</p>
    <h1>A filter that reads a different parameter</h1>
    <span class="badges"><span class="type t-classic">Classic</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="hpp.php" method="GET">
                Ping address: <input type="text" name="addr">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php
				// The filter walks the raw query string and stops at the first 'addr' it finds.
				$first = '';
				$raw = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
				foreach (explode('&', $raw) as $pair) {
					if (strpos($pair, 'addr=') === 0) { $first = urldecode(substr($pair, 5)); break; }
				}
				if ($first !== '' && preg_match('/[;&|`$()]/', $first)) {
					die("Blocked by input filter.");
				}
				// PHP keeps the last one, and that is what reaches the shell.
				$addr = isset($_GET['addr']) ? $_GET['addr'] : '';
				if ($addr !== '') {
					echo shell_exec("/bin/ping -c 4 ".$addr." 2>&1");
				}
				?></b>
