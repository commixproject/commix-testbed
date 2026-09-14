<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The value has to look like an IP address - but the pattern is <code>/^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$/m</code>, and that <code>m</code> flag makes <code>^</code> and <code>$</code> match at every line rather than once. A second line walks straight past it.</p>
      <p class="lesson">The most instructive filter on the site. The author did the right thing - validate the shape of the value - and still lost, to one letter. The m flag makes ^ and $ match at every line rather than at the start and end of the whole value, so a string whose first line is a valid IP passes no matter what follows on the next line. Anchors without that flag, or a check that the value contains no newline, would have held.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>$addr = ($_GET[&#x27;addr&#x27;] ?? &#x27;&#x27;);
if(isset($addr)){
    # Inspired from pentesterlab.com - &#x27;Web for Pentester&#x27; course.
    # https://pentesterlab.com/exercises/web_for_pentester
    if (!(preg_match(&#x27;/^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$/m&#x27;,$addr))){
        die(&quot;Invalid IP address format.&quot;);
    }else{
        # Execute command!
        echo exec(&quot;/bin/ping -c 4 &quot;.$addr);
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
<title>IP-format validation &middot; commix-testbed</title>
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
    <h1>IP-format validation</h1>
    <span class="badges"><span class="type t-classic">Classic</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="preg_match.php" method="GET">
                Ping address: <input type="text" name="addr">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php
                    $addr = ($_GET['addr'] ?? '');
                    if(isset($addr)){
                        # Inspired from pentesterlab.com - 'Web for Pentester' course.
                        # https://pentesterlab.com/exercises/web_for_pentester
                        if (!(preg_match('/^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$/m',$addr))){
                            die("Invalid IP address format.");
                        }else{
                            # Execute command!
                            echo exec("/bin/ping -c 4 ".$addr);
                        }
                    }
                ?></b>
