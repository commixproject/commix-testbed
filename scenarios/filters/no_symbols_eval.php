<?php
/*
No shutdown-function tail here, unlike most of this directory's scenarios: the whole point of a
boolean/blind technique against an eval() sink is that the injected code can call exit() on its own,
and the response has to actually end right there for that signal to mean anything. Padding the page
back out afterwards - the usual move so a die() in the filter check still leaves a complete page -
would hide that signal instead of carrying it.
*/
$addr = isset($_POST['addr']) ? $_POST['addr'] : '';
if ($addr !== '' && preg_match('/["\'\d_$; ]/', $addr)) {
  die("Filtered.");
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>No symbols eval &middot; commix-testbed</title>
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
    <h1>No symbols eval</h1>
    <span class="badges"><span class="type t-classic">Classic</span><span class="lang">PHP</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="no_symbols_eval.php" method="POST">
                Value: <input type="text" name="addr">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php if ($addr !== '') { eval($addr); } ?></b>
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The value is rejected if it contains a quote of either kind, a digit, an underscore, a dollar sign, a semicolon or a space - six characters at once, which rules out a literal number, a variable reference, a string and the usual way of ending one statement and starting the next, all in a single rule.</p>
      <p class="lesson">None of the six is actually needed to run code. A closing PHP tag ends a statement exactly as a semicolon would, with nothing to reopen where nothing is meant to follow it. A literal integer is the same value written as <code>true</code> added to itself that many times - a boolean the moment arithmetic touches it. An empty string an expression is meant to read as nothing is the keyword <code>NULL</code>, read back as the same nothing once <code>.</code> concatenation touches it.</p>
      <details class="solve">
        <summary>Hint</summary>
        <table class="solve-glossary">
          <tr><td><code>--eval=php</code></td><td>tells commix the sink evaluates PHP, not a shell command</td></tr>
          <tr><td><code>--tamper=phpnosymbols,space2htab</code></td><td>rewrites digits and quotes in a PHP eval payload, for a filter blocking both; and replaces each space with a horizontal tab</td></tr>
          <tr><td><code>--skip-calc</code></td><td>skips the arithmetic probe, for a sink that can't compute but can still prove itself another way</td></tr>
        </table>
      </details>
      <p class="verdict">Identified by commix as <b><a href="https://github.com/commixproject/commix/wiki/Techniques#2-dynamic-code-evaluation">Classic code injection</a></b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>$addr = isset($_POST[&#x27;addr&#x27;]) ? $_POST[&#x27;addr&#x27;] : &#x27;&#x27;;
if ($addr !== &#x27;&#x27; &amp;&amp; preg_match(&#x27;/[&quot;\&#x27;\d_$; ]/&#x27;, $addr)) {
  die(&quot;Filtered.&quot;);
}
eval($addr);</code></pre>
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

</body>
</html>
