<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">A GraphQL endpoint. It reads the <code>host</code> argument - written into the document, or passed beside it in <code>variables</code> - and hands it to a shell.</p>
      <p class="lesson">The body is a document, not a list of pairs. A payload appended to the whole <code>query</code> lands past its last brace and only produces a syntax error, which reads as a parameter that is not injectable. What is worth testing is the argument inside the document, and the request has to stay a valid document while it is tested.</p>
      <p class="reach">Send it either way: as a document of its own under <code>application/graphql</code>, or as the JSON envelope a client sends. Both are read the same.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>$raw = file_get_contents(&#x27;php://input&#x27;);
$doc = $raw;
$vars = array();
$json = json_decode($raw, true);
if (is_array($json)) {
  if (isset($json[&#x27;query&#x27;]))     { $doc  = $json[&#x27;query&#x27;]; }
  if (isset($json[&#x27;variables&#x27;])) { $vars = $json[&#x27;variables&#x27;]; }
}
$host = isset($vars[&#x27;host&#x27;]) ? $vars[&#x27;host&#x27;] : &#x27;&#x27;;
// The argument as the document itself spells it, where no variable carried it.
if ($host === &#x27;&#x27; &amp;&amp; preg_match(&#x27;/host:\s*&quot;([^&quot;]*)&quot;/&#x27;, $doc, $m)) { $host = $m[1]; }
echo shell_exec(&quot;/bin/ping -c 4 &quot;.$host.&quot; 2&gt;&amp;1&quot;);</code></pre>
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
  if (/invalid|hack attempt|not allowed|no white spaces|is not set|denied|only$|required|locked|duplicate|unavailable|expired|stored/i.test(text)) {
    out.classList.add("refused");
  }
})();
</script>
</body>
</html>
TESTBED_TAIL;
register_shutdown_function(function () use ($__tail) { echo $__tail; });
$__raw = file_get_contents('php://input');
$__doc = $__raw;
$__vars = array();
$__json = json_decode($__raw, true);
if (is_array($__json)) {
  if (isset($__json['query']))     { $__doc  = $__json['query']; }
  if (isset($__json['variables'])) { $__vars = $__json['variables']; }
}
$__host = isset($__vars['host']) ? $__vars['host'] : '';
if ($__host === '' && preg_match('/host:\s*"([^"]*)"/', $__doc, $__m)) { $__host = $__m[1]; }
$__result = $__host === '' ? '' : shell_exec("/bin/ping -c 4 ".$__host." 2>&1");
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>GraphQL document, argument evaluated &middot; commix-testbed</title>
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
    <h1>GraphQL document, argument evaluated</h1>
    <span class="badges"><span class="type t-classic">Classic</span><span class="lang">PHP</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <div class="howto"><p>This page takes no form input - it reads the raw request body. Any of these reaches it:</p><pre><code>POST /scenarios/regular/POST/classic_graphql.php
Content-Type: application/graphql

query { ping(host: "127.0.0.1") }</code></pre><pre><code>Content-Type: application/json

{"query":"query { ping(host: \"127.0.0.1\") }"}</code></pre><pre><code>Content-Type: application/json

{"query":"query($h:String!){ping(host:$h)}","variables":{"host":"127.0.0.1"}}</code></pre></div><b><?php echo $__result; ?></b>
