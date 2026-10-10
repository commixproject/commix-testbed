<?php
ob_start();
// A scenario that ends in die() would otherwise take the rest of the page with it - the
// explanation, the footer and the script that colours the result. A shutdown function still runs
// after die(), so the page is completed either way.
$__tail = <<<'TESTBED_TAIL'
    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">The <code>session</code> cookie carries a JSON Web Token. The page reads the <code>kid</code> ("key id") field out of the token's header to find out which key it should verify with, and locates that key by concatenating the value into a shell command - <code>ls -l /var/www/keys/&lt;kid&gt;</code>.</p>
      <p class="lesson">The ordering is the whole bug. <code>kid</code> names the key the signature would be checked against, so it has to be read <i>before</i> that signature can be checked - which means the lookup runs on whatever the client put there, every request, with nothing authenticated yet. Signing the token perfectly changes nothing: the sink is reached on the way to deciding whether the token was ever valid. Resolve a key id against a fixed map of known ids, never by building a path or a command out of it.</p>
      <p class="reach">Named by commix as a candidate with <code>--jwt</code>.</p>
      <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>$key_dir = &quot;/var/www/keys/&quot;;
$token = (isset($_COOKIE[&#x27;session&#x27;]) ? $_COOKIE[&#x27;session&#x27;] : &#x27;&#x27;);
$parts = explode(&#x27;.&#x27;, $token);
$header = json_decode(base64_decode(strtr($parts[0], &#x27;-_&#x27;, &#x27;+/&#x27;)), true);
$kid = (isset($header[&#x27;kid&#x27;]) ? $header[&#x27;kid&#x27;] : &#x27;&#x27;);
# The key is located before the signature it would verify has been checked.
echo exec(&quot;ls -l &quot; . $key_dir . $kid);</code></pre>
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

// Minted for a first-time visitor so the page has a token to read, the way the application that
// issued it would have. Only the 'kid' below is ever acted on.
function testbed_issue_token() {
  $segment = function ($data) {
    return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
  };
  $header = array("alg" => "HS256", "typ" => "JWT", "kid" => "prod.pem");
  $payload = array("user" => "guest", "iat" => time());
  // Signed with a key that is never checked here - the lookup below happens first either way.
  $signing_input = $segment($header) . "." . $segment($payload);
  $signature = rtrim(strtr(base64_encode(hash_hmac('sha256', $signing_input, 'testbed', true)), '+/', '-_'), '=');
  return $signing_input . "." . $signature;
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>JSON Web Token - 'kid' key lookup &middot; commix-testbed</title>
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
    <p class="crumb">Stateful &amp; multi-step</p>
    <h1>JSON Web Token - 'kid' key lookup</h1>
    <span class="badges"><span class="type t-classic">Classic</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <div class="howto"><p>The value lives inside the JSON Web Token in your <b>session</b> cookie, which the page issues on your first visit. Decode the token's first segment, edit the <code>kid</code> field, re-encode it and send the cookie back - the signature is never checked, so it does not have to be rebuilt.</p></div><br>
                <b><?php
                $key_dir = "/var/www/keys/";
                if (!isset($_COOKIE['session'])) {
                    setcookie("session", testbed_issue_token(), time() + (86400 * 30), "/");
                    echo "No session token yet - reload the page to be issued one.";
                } else {
                    $token = (isset($_COOKIE['session']) ? $_COOKIE['session'] : '');
                    $parts = explode('.', $token);
                    $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
                    $kid = (isset($header['kid']) ? $header['kid'] : '');
                    if ($kid === '') {
                        echo "The token's header names no 'kid' to look up!";
                    } else {
                        if( stristr(php_uname('s'), 'Windows NT')){
                          # Windows-based command execution.
                          echo exec("dir " . $key_dir . $kid);
                        } else {
                          # Unix-based command execution.
                          echo exec("ls -l " . $key_dir . $kid);
                        }
                    }
                }
                ?></b>
