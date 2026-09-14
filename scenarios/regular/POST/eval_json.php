<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">An evaluated sink reached through a JSON member rather than a form field.</p>
      <p class="lesson">A structured body feeding an evaluated sink - the two complications that are usually met separately, on one page.</p>
      <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>// JSON request format:
// {&quot;name&quot;:&quot;ancst&quot;}
// read JSon input
$data_back = json_decode(file_get_contents(&#x27;php://input&#x27;));
// set json string to php variables
$name = (isset($data_back-&gt;{&quot;name&quot;}) ? $data_back-&gt;{&quot;name&quot;} : &#x27;&#x27;);
// create json response
$responses = array(&quot;User&quot; =&gt; array(&quot;Name&quot;, eval(&quot;echo \&quot;Hello, &quot;.$name.&quot;!\&quot;;&quot;)));
echo json_encode($responses);</code></pre>
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
<title>JSON request body, evaluated &middot; commix-testbed</title>
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
    <h1>JSON request body, evaluated</h1>
    <span class="badges"><span class="type t-classic">Classic</span><span class="tag">code</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <div class="howto"><p>This page takes no form input - it reads the raw request body. Send it something shaped like this:</p><pre><code>POST /scenarios/regular/POST/eval_json.php
Content-Type: application/json

{"name":"ancst"}</code></pre><p class="note"><code>name</code> is evaluated as PHP rather than run by a shell.</p></div><b><?php
                // JSON request format:
                // {"name":"ancst"}
                // read JSon input
                $data_back = json_decode(file_get_contents('php://input'));
                // set json string to php variables
                $name = (isset($data_back->{"name"}) ? $data_back->{"name"} : '');
                // create json response
                $responses = array("User" => array("Name", eval("echo \"Hello, ".$name."!\";")));
                echo json_encode($responses);
                ?></b>
