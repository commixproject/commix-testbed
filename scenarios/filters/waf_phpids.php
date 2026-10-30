<?php
// A converged filter in front of this page, in the spirit of PHPIDS (the intrusion-detection
// library DVWA itself bundles): several independent signatures each add their own impact score,
// and the request is blocked - its own response, its own status code, nothing of the app below
// reached at all - once the total crosses a threshold, rather than any single rule deciding alone.
function waf_impact($raw) {
  $signatures = [
    // A well-known shell-separator bypass, carried as its own signature - high impact on its own.
    '/\$\{IFS\}/i'    => 10,
    // A literal space preparing a shell command - just as high on its own, the one every other
    // encoding here is written to avoid.
    '/\x20/'          => 10,
    // Everything below is a weaker signal on its own - real enough to carry its own weight, but
    // none of them, stacked together, reach the block threshold unassisted by either signature
    // above. A converged filter still only adds up what its author thought to write against.
    '/[;&|]/'         => 2, // a bare shell metacharacter
    '/\$\(|`/'        => 3, // command substitution syntax
    '/-/'             => 2, // a bare hyphen, the flag-injection signature every ping-wrapper filter carries
    '/\bsleep\b/'     => 2, // the timing-attack keyword, case-sensitive
  ];
  $score = 0;
  foreach ($signatures as $pattern => $impact) {
    if (preg_match($pattern, $raw)) {
      $score += $impact;
    }
  }
  return $score;
}

$__raw = urldecode($_SERVER['QUERY_STRING'] . '&' . file_get_contents('php://input'));
if (waf_impact($__raw) >= 10) {
  http_response_code(403);
  header('Content-Type: text/plain');
  die("Forbidden - request blocked by security policy (impact score too high).\n");
}

ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">A converged filter sits in front of this page, the way DVWA's own bundled PHPIDS integration scores a request rather than matching it against one rule. A literal space or the <code>${IFS}</code> trick alone already crosses the block threshold; a bare metacharacter, <code>$(...)</code>, a hyphen or the word <code>sleep</code> each only add a little on their own - stack every one of them and the total still falls short. A tab needs none of it and stays under the threshold on its own.</p>
      <p class="lesson">A converged score still only adds up the signatures its author thought to write, each weighed by how much it alone is worth worrying about. Nothing here was written against a tab standing in for a space, so a request built around one never approaches the threshold at all - whatever else it also happens to carry.</p>
      <details class="solve">
        <summary>Hint</summary>
        <table class="solve-glossary">
          <tr><td><code>--tamper=space2htab</code></td><td>replaces each space with a horizontal tab</td></tr>
        </table>
      </details>
      <p class="verdict">Identified by commix as <b><a href="https://github.com/commixproject/commix/wiki/Techniques#1-results-based-classic-command-injection">Classic command injection</a></b>, once its own evasion has stepped past the <code>${IFS}</code> tier.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>function waf_impact($raw) {
  $signatures = [
    &#x27;/\$\{IFS\}/i&#x27; =&gt; 10,
    &#x27;/\x20/&#x27;       =&gt; 10,
    &#x27;/[;&amp;|]/&#x27;      =&gt; 2,
    &#x27;/\$\(|`/&#x27;     =&gt; 3,
    &#x27;/-/&#x27;          =&gt; 2,
    &#x27;/\bsleep\b/&#x27;  =&gt; 2,
  ];
  $score = 0;
  foreach ($signatures as $pattern =&gt; $impact) {
    if (preg_match($pattern, $raw)) {
      $score += $impact;
    }
  }
  return $score;
}

$raw = urldecode($_SERVER[&#x27;QUERY_STRING&#x27;] . &#x27;&amp;&#x27; . file_get_contents(&#x27;php://input&#x27;));
if (waf_impact($raw) &gt;= 10) {
  http_response_code(403);
  die(&quot;Forbidden - request blocked by security policy.&quot;);
}

$addr = isset($_POST[&#x27;addr&#x27;]) ? $_POST[&#x27;addr&#x27;] : &#x27;&#x27;;
if ($addr !== &#x27;&#x27;) {
  echo exec(&quot;/bin/ping -c 4 &quot; . $addr);
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
// The page prints its result into the last <b>: empty means nothing was submitted yet, and the
// shared indentation of the markup is taken off so the result starts hard left.
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
})();
</script>
</body>
</html>
TESTBED_TAIL;
register_shutdown_function(function () use ($__tail) { echo $__tail; });

$__result = '';
$__addr = isset($_POST['addr']) ? $_POST['addr'] : '';
if ($__addr !== '') {
  $__result = exec("/bin/ping -c 4 " . $__addr);
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>WAF: PHPIDS-style scoring &middot; commix-testbed</title>
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
    <h1>WAF: PHPIDS-style scoring</h1>
    <span class="badges"><span class="type t-classic">Classic</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="waf_phpids.php" method="POST">
                Ping address: <input type="text" name="addr">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php echo htmlspecialchars($__result, ENT_QUOTES); ?></b>
