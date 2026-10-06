<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">A single <code>output</code> value must read as exactly <code>"cmd arg"</code> - split on whitespace into precisely two pieces, or the request is rejected. <code>arg</code> then has every <code>"</code>, <code>'</code>, backtick, <code>{</code> and <code>}</code> replaced with <code>_</code>, before being wrapped in <code>escapeshellarg()</code>. For <code>echo</code> specifically, the template re-wraps that already-single-quoted value inside <b>double</b> quotes - single quotes are inert data there, but <code>$(...)</code> command substitution still expands.</p>
      <p class="lesson">Two layers of quoting that do not agree with each other are worse than one: <code>escapeshellarg()</code> did its job, wrapping the value so the shell reads it as one inert argument - and then the template put that argument inside a second, different quote character, where the protection it relied on no longer applies.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>. Based on <a href="https://dojo-yeswehack.com/challenge-of-the-month/Dojo-30">YesWeHack Dojo #30: Terminal Isolation</a>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>class Terminal {
  function run($cmd, $arg) {
    $arg = str_replace(array(&#x27;&quot;&#x27;, &quot;&#x27;&quot;, &#x27;`&#x27;, &#x27;{&#x27;, &#x27;}&#x27;), &#x27;_&#x27;, $arg);
    $arg = escapeshellarg($arg);
    switch ($cmd) {
      case &#x27;echo&#x27;:
        $full = sprintf(&#x27;echo -n &quot;%s&quot;&#x27;, $arg);
        break;
      case &#x27;ls&#x27;:
        $full = &quot;ls &quot; . $arg;
        break;
      case &#x27;cd&#x27;:
        $full = &quot;cd &quot; . $arg;
        break;
      default:
        $full = &quot;echo invalid&quot;;
    }
    return shell_exec($full . &quot; 2&gt;&amp;1&quot;);
  }
}

$raw = isset($_POST[&#x27;output&#x27;]) ? $_POST[&#x27;output&#x27;] : &#x27;&#x27;;
$parts = preg_split(&#x27;/[\s]+/&#x27;, $raw);
if (count($parts) != 2) {
  echo &quot;Error: only one command and one argument allowed&quot;;
  exit;
}
list($cmd, $arg) = $parts;
$t = new Terminal();
echo $t-&gt;run($cmd, $arg);</code></pre>
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
  if (/invalid|hack attempt|not allowed|no white spaces|is not set|denied|only allowed|error:/i.test(text)) {
    out.classList.add("refused");
  }
})();
</script>
</body>
</html>
TESTBED_TAIL;
register_shutdown_function(function () use ($__tail) { echo $__tail; });

class Terminal {
  function run($cmd, $arg) {
    $arg = str_replace(array('"', "'", '`', '{', '}'), '_', $arg);
    $arg = escapeshellarg($arg);
    switch ($cmd) {
      case 'echo':
        $full = sprintf('echo -n "%s"', $arg);
        break;
      case 'ls':
        $full = "ls " . $arg;
        break;
      case 'cd':
        $full = "cd " . $arg;
        break;
      default:
        $full = "echo invalid";
    }
    return shell_exec($full . " 2>&1");
  }
}

$__result = '';
$__raw = isset($_POST['output']) ? $_POST['output'] : '';
if ($__raw !== '') {
  $__parts = preg_split('/[\s]+/', $__raw);
  if (count($__parts) != 2) {
    $__result = "Error: only one command and one argument allowed";
  } else {
    list($__cmd, $__arg) = $__parts;
    $__t = new Terminal();
    $__result = $__t->run($__cmd, $__arg);
  }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Terminal isolation &middot; commix-testbed</title>
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
    <p class="crumb">CTF challenges</p>
    <h1>Terminal isolation</h1>
    <span class="badges"><span class="type t-classic">Classic</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="dojo30_terminal_isolation.php" method="POST">
                Command (e.g. "echo hello"): <input type="text" name="output" size="40">
                <input value="Submit!" type="submit">
                </form>
                <br>
                <b><?php echo htmlspecialchars($__result, ENT_QUOTES); ?></b>
