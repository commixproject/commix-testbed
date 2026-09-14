<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">Three values are submitted and two of them are checked - <code>addr</code> against a host pattern and <code>label</code> against word characters. <code>count</code> is concatenated into the command with no check at all.</p>
      <p class="lesson">The most realistic scenario here. Someone did think about input validation - they just did not finish. A count, an id, a page number or a sort order rarely looks dangerous enough to check, and it reaches the same shell as the field that did get a regex. Testing one parameter and declaring the page safe is how this survives a review.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>$addr  = isset($_GET[&#x27;addr&#x27;])  ? ($_GET[&#x27;addr&#x27;] ?? &#x27;&#x27;)  : &#x27;&#x27;;
$count = isset($_GET[&#x27;count&#x27;]) ? ($_GET[&#x27;count&#x27;] ?? &#x27;&#x27;) : &#x27;4&#x27;;
$label = isset($_GET[&#x27;label&#x27;]) ? ($_GET[&#x27;label&#x27;] ?? &#x27;&#x27;) : &#x27;&#x27;;
if(isset($_GET[&#x27;addr&#x27;])){
  # &#x27;addr&#x27; and &#x27;label&#x27; are validated; &#x27;count&#x27; is not.
  if(!preg_match(&#x27;/^[\w.\-]+$/&#x27;, $addr)){ die(&quot;Invalid host.&quot;); }
  if(!preg_match(&#x27;/^\w*$/&#x27;, $label)){ die(&quot;Invalid label.&quot;); }
  echo exec(&quot;/bin/ping -c &quot;.$count.&quot; &quot;.$addr);
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
<title>Only one of three parameters is validated &middot; commix-testbed</title>
<meta name="robots" content="noindex">
<link rel="shortcut icon" href="../../../favicon.ico">
<link href="https://fonts.googleapis.com/css?family=Exo+2:400,500,600,700" rel="stylesheet">
<link rel="stylesheet" href="../../../css/testbed.css">
</head>
<body>

<nav class="nav">
  <div class="wrap">
    <a class="brand" href="../../../index.php">
      <img src="../../../img/testbed_header_logo.png" alt="commix-testbed"></a>
    <a class="back" href="../../../index.php">&larr; All scenarios</a>
  </div>
</nav>

<div class="wrap page">

  <header class="sc-head">
    <p class="crumb">Regular parameters</p>
    <h1>Only one of three parameters is validated</h1>
    <span class="badges"><span class="type t-classic">Classic</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="multiple_params.php" method="GET">
                Host: <input type="text" name="addr" value="127.0.0.1">
                Count: <input type="text" name="count" value="4">
                Label: <input type="text" name="label" value="home">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php
                  $addr  = isset($_GET['addr'])  ? ($_GET['addr'] ?? '')  : '';
                  $count = isset($_GET['count']) ? ($_GET['count'] ?? '') : '4';
                  $label = isset($_GET['label']) ? ($_GET['label'] ?? '') : '';
                  if(isset($_GET['addr'])){
                    # 'addr' and 'label' are validated; 'count' is not.
                    if(!preg_match('/^[\w.\-]+$/', $addr)){ die("Invalid host."); }
                    if(!preg_match('/^\w*$/', $label)){ die("Invalid label."); }
                    echo exec("/bin/ping -c ".$count." ".$addr);
                  }
                ?></b>
