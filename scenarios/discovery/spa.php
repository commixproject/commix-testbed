<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The page carries one empty element and loads <code>app.js</code>. Every route it talks to is written inside that script, and none of them is linked from the markup.</p>
      <p class="lesson">A crawler follows what a page links to, and skips <code>.js</code> on its way to the anchors - so against an application whose routes live in its bundle it comes back with nothing at all. The endpoints are not hidden; they are simply not written where a crawler looks.</p>
      <details class="solve">
        <summary>Hint</summary>
        <p>These flags read the scripts a page loads and add the endpoints they name to the crawl:</p>
        <table class="solve-glossary">
          <tr><td><code>--crawl=1</code></td><td>follows links from the target page to find more of the application</td></tr>
          <tr><td><code>--mine-endpoints</code></td><td>looks for endpoints named only inside the scripts a page loads</td></tr>
        </table>
      </details>
      <p class="verdict">The routes it names lead to <b><a href="https://github.com/commixproject/commix/wiki/Techniques#1-results-based-classic-command-injection">Classic command injection</a></b> and <b><a href="https://github.com/commixproject/commix/wiki/Techniques#2-dynamic-code-evaluation">Classic code injection</a></b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>&lt;!-- The whole page. The routes are in the script, not here. --&gt;
&lt;script src=&quot;app.js&quot;&gt;&lt;/script&gt;
&lt;div id=&quot;app&quot;&gt;&lt;/div&gt;

// app.js
var API = {
  ping:  &quot;/scenarios/regular/GET/classic.php?addr=127.0.0.1&quot;,
  greet: &quot;/scenarios/regular/GET/eval.php?user=test&quot;
};</code></pre>
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

<script src="app.js"></script>
</body>
</html>
TESTBED_TAIL;
register_shutdown_function(function () use ($__tail) { echo $__tail; });
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Routes that live only in the script &middot; commix-testbed</title>
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
    <p class="crumb">Discovery</p>
    <h1>Routes that live only in the script</h1>
    <span class="badges"><span class="type t-classic">Classic</span><span class="lang">PHP</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <div id="app">Loading...</div>
