<?php
// Nothing is executed here: this page only describes the service and points at what it publishes.
$spec = "openapi.json";
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>An API that describes itself &middot; commix-testbed</title>
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
    <h1>An API that describes itself</h1>
    <span class="badges"><span class="type t-classic">Classic</span><span class="lang">PHP</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <p>There is nothing to fill in here, and nothing on this page reaches a shell. What the
         service accepts is written down somewhere else:</p>
      <br>
      <b><a href="<?php echo $spec; ?>"><?php echo $spec; ?></a></b>
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The service publishes an OpenAPI description of itself. That document names two operations - a <code>GET</code> taking a query parameter and a <code>POST</code> taking a JSON body - and both of them hand what they are given to a shell. Neither endpoint is linked from any page of this testbed, and neither appears in any form or any script.</p>
      <p class="lesson">Crawling finds what a site links to. An API links to nothing: its endpoints are reached by clients that were told about them, and increasingly what tells them is a machine-readable description the service serves itself. A scan that only follows links sees a page with one link on it and stops; the description beside it lists the paths, the methods, the parameters and the shape of every body - which is the whole testable surface, written down.</p>
      <details class="solve">
        <summary>Hint</summary>
        <p>This flag reads the description and derives one request per operation, filling each parameter and body from the examples and types the document gives.</p>
        <table class="solve-glossary">
          <tr><td><code>--openapi</code></td><td>derives requests from the target's own published OpenAPI/Swagger description</td></tr>
          <tr><td><code>--openapi-base</code></td><td>names the document's own base URL, for paths that are relative and otherwise unclear where they live</td></tr>
          <tr><td><code>--openapi-tags</code></td><td>tests only the operations under the given OpenAPI tag, instead of the whole document</td></tr>
        </table>
      </details>
      <p class="verdict">Identified by commix as <b><a href="https://github.com/commixproject/commix/wiki/Techniques#1-results-based-classic-command-injection">Classic command injection</a></b>, once for each of the two operations.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>// api_lookup.php - reached with ?host=
$host = isset($_GET[&#x27;host&#x27;]) ? $_GET[&#x27;host&#x27;] : &#x27;&#x27;;
echo shell_exec(&quot;/bin/ping -c 4 &quot;.$host.&quot; 2&gt;&amp;1&quot;);

// api_report.php - reached with {&quot;host&quot;: &quot;...&quot;}
$sent = json_decode(file_get_contents(&quot;php://input&quot;), true);
$host = is_array($sent) &amp;&amp; isset($sent[&#x27;host&#x27;]) ? $sent[&#x27;host&#x27;] : &#x27;&#x27;;
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

</body>
</html>
