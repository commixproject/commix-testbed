<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The value is rejected if it names one of a dozen common shell/recon commands as a whole word, case-insensitively (<code>cat</code>, <code>ls</code>, <code>wget</code>, <code>curl</code>, <code>nc</code>, <code>bash</code>, <code>python</code>, <code>perl</code>, <code>chmod</code>, <code>base64</code>, <code>id</code>, <code>whoami</code>, <code>uname</code>, <code>echo</code>, <code>sleep</code>). Every separator (<code>;</code> <code>&amp;</code> <code>|</code>), every quote and <code>$(...)</code> itself still gets through - the list names programs, not syntax.</p>
      <p class="lesson">Nothing here calls a particular shell, so PHP's <code>exec()</code> reaches its own default - <code>/bin/sh</code>, which on this (and most Debian/Ubuntu) targets is <code>dash</code>. ANSI-C quoting (<code>$'\NNN...'</code>) is <code>bash</code>/<code>ksh</code>/<code>zsh</code> syntax; used as a command name under <code>dash</code> it fails outright rather than expanding. A command substitution around <code>printf</code>'s own octal escapes (<code>$(printf '\143\141\164')</code>) is plain POSIX instead, and reaches every name the blacklist was written against - including the ones <code>commix</code>'s own payloads need, like <code>echo</code> and <code>sleep</code>. The one word nothing here hides it behind is <code>printf</code> itself, simply never listed.</p>
      <p class="reach">Reach it with <code>--tamper=printfoctal</code>.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>function blocks_command_name($input) {
  $blacklist = &#x27;cat|ls|wget|curl|nc|bash|python|perl|chmod|base64|id|whoami|uname|echo|sleep&#x27;;
  return (bool) preg_match(&#x27;/\b(&#x27; . $blacklist . &#x27;)\b/i&#x27;, $input);
}

$addr = isset($_POST[&#x27;addr&#x27;]) ? $_POST[&#x27;addr&#x27;] : &#x27;&#x27;;
if ($addr !== &#x27;&#x27;) {
  if (blocks_command_name($addr)) {
    echo &quot;Blocked: a listed command name is not allowed.&quot;;
  } else {
    // No shell named here - PHP falls back to its own default, which is &#x27;/bin/sh&#x27;
    // and, on this target, dash.
    echo exec(&quot;/bin/ping -c 4 &quot; . $addr);
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
    var submitted = location.search.length > 1 || document.referrer.indexOf(location.pathname) !== -1;
    if (!submitted) { out.remove(); return; }
    out.textContent = "(no output - the command printed nothing to stdout)";
    out.classList.add("silent");
    return;
  }
  if (/^blocked:/i.test(text)) {
    out.classList.add("refused");
  }
})();
</script>
</body>
</html>
TESTBED_TAIL;
register_shutdown_function(function () use ($__tail) { echo $__tail; });

function blocks_command_name($input) {
  $blacklist = 'cat|ls|wget|curl|nc|bash|python|perl|chmod|base64|id|whoami|uname|echo|sleep';
  return (bool) preg_match('/\b(' . $blacklist . ')\b/i', $input);
}

$__result = '';
$__addr = isset($_POST['addr']) ? $_POST['addr'] : '';
if ($__addr !== '') {
  if (blocks_command_name($__addr)) {
    $__result = "Blocked: a listed command name is not allowed.";
  } else {
    // No shell named here - PHP falls back to its own default, which is '/bin/sh'
    // and, on this target, dash.
    $__result = exec("/bin/ping -c 4 " . $__addr);
  }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Command name blacklist &middot; commix-testbed</title>
<meta name="robots" content="noindex">
<link rel="shortcut icon" href="../../favicon.ico">
<link href="https://fonts.googleapis.com/css?family=Exo+2:400,500,600,700" rel="stylesheet">
<link rel="stylesheet" href="../../css/testbed.css">
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
    <h1>Command name blacklist</h1>
    <span class="badges"><span class="type t-classic">Classic</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="command_name_blacklist.php" method="POST">
                Ping address: <input type="text" name="addr">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php echo htmlspecialchars($__result, ENT_QUOTES); ?></b>
