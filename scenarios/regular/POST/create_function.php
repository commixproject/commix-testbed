<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The value is concatenated into the body of a <code>create_function()</code>, which compiles it as PHP. Removed in PHP 8, so this one needs an older interpreter.</p>
      <p class="lesson">create_function() built a function by compiling a string, so anything concatenated into its body became code. It was deprecated in PHP 7.2 and removed in 8. Like assert(), it is worth seeing because the dangerous part is not the syntax but the idea: text that becomes code.</p>
      <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>if (isset($_POST[&quot;user&quot;])){
# Execute command!
$dyn_function = create_function(&#x27;&#x27;, &quot;echo \&quot;Hello, &quot;.($_POST[&#x27;user&#x27;] ?? &#x27;&#x27;).&quot;!\&quot;;&quot;);
$dyn_function(&#x27;&#x27;);
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
<title>create_function() on the value &middot; commix-testbed</title>
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
    <h1>create_function() on the value</h1>
    <span class="badges"><span class="type t-classic">Classic</span><span class="tag">code</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="create_function.php" method="POST">
              Enter your name: <input type="text" name="user">
              <input value="Submit!" type="submit">
              </form>
                <br>
                <b><?php
                if (isset($_POST["user"])){
                # Execute command!
                $dyn_function = create_function('', "echo \"Hello, ".($_POST['user'] ?? '')."!\";");
                $dyn_function(''); 

                }
                ?></b>
