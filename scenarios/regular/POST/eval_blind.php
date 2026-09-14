<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The value is evaluated as PHP - <code>eval('$greeting = "Hello, ' . $user . '!";')</code> - but the greeting it builds is never printed. The page answers with the same fixed sentence whatever you send it.</p>
      <p class="lesson">The fourth corner of the matrix, and the hardest of the four to find. The sink is an evaluated string rather than a shell, so shell metacharacters do nothing - and the result is never printed, so there is nothing to read either. Both of the usual footholds are gone at once: the payload has to be valid PHP, and the proof has to come from timing or a side effect. A page that quietly evaluates input and says only 'saved' is the realistic shape of this bug.</p>
      <p class="verdict">Reported by commix as <b>Blind code injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>if (isset($_POST[&quot;user&quot;])){
  # The greeting is built as code, and then thrown away.
  eval(&#x27;$greeting = &quot;Hello, &#x27; . ($_POST[&#x27;user&#x27;] ?? &#x27;&#x27;) . &#x27;!&quot;;&#x27;);
  echo &quot;Your preferences have been saved.&quot;;
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
<title>eval() with nothing printed back &middot; commix-testbed</title>
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
    <h1>eval() with nothing printed back</h1>
    <span class="badges"><span class="type t-blind">Blind</span><span class="tag">code</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="eval_blind.php" method="POST">
                Enter your name: <input type="text" name="user">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php
                if (isset($_POST["user"])){
                  # The greeting is built as code, and then thrown away.
                  eval('$greeting = "Hello, ' . ($_POST['user'] ?? '') . '!";');
                  echo "Your preferences have been saved.";
                }
                ?></b>
