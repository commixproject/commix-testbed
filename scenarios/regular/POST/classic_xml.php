<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The body is XML parsed with <code>LIBXML_NOENT | LIBXML_DTDLOAD</code>, and both <code>&lt;addr&gt;</code> and <code>&lt;count&gt;</code> are concatenated into the command - two injection points on one page, not one.</p>
      <p class="lesson">Two things to notice. Both addr and count reach the command, so the page has more than one injection point and finding only the first one is an incomplete result. The parser is also built with LIBXML_NOENT and LIBXML_DTDLOAD, which resolves external entities - so on top of the command injection this document is an XXE waiting to happen.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>$xmlfile = file_get_contents(&#x27;php://input&#x27;);
$dom = new DOMDocument();
if ($xmlfile === &#x27;&#x27;) { $xmlfile = &#x27;&lt;root&gt;&lt;addr&gt;&lt;/addr&gt;&lt;count&gt;&lt;/count&gt;&lt;/root&gt;&#x27;; }
$dom-&gt;loadXML($xmlfile, LIBXML_NOENT | LIBXML_DTDLOAD);
$xml = simplexml_import_dom($dom);
$addr_data = $xml-&gt;addr;
$count_data = $xml-&gt;count;
$addr = &quot;$addr_data&quot;;
$count = &quot;$count_data&quot;;
echo exec(&quot;/bin/ping -c &quot;.$count.&quot; &quot;.$addr);</code></pre>
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
<title>SOAP/XML request body &middot; commix-testbed</title>
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
    <h1>SOAP/XML request body</h1>
    <span class="badges"><span class="type t-classic">Classic</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <div class="howto"><p>This page takes no form input - it reads the raw request body. Send it something shaped like this:</p><pre><code>POST /scenarios/regular/POST/classic_xml.php
Content-Type: application/xml

&lt;root&gt;
  &lt;addr&gt;127.0.0.1&lt;/addr&gt;
  &lt;count&gt;4&lt;/count&gt;
&lt;/root&gt;</code></pre><p class="note">Both <code>addr</code> and <code>count</code> reach the command - two injection points.</p></div><b><?php
                    $xmlfile = file_get_contents('php://input');
                    $dom = new DOMDocument();
                    if ($xmlfile === '') { $xmlfile = '<root><addr></addr><count></count></root>'; }
                    $dom->loadXML($xmlfile, LIBXML_NOENT | LIBXML_DTDLOAD);
                    $xml = simplexml_import_dom($dom);
                    $addr_data = $xml->addr;
                    $count_data = $xml->count;
                    $addr = "$addr_data";
                    $count = "$count_data";
                    echo exec("/bin/ping -c ".$count." ".$addr);
                ?></b>
