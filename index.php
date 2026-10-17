<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>commix-testbed &middot; command injection test environment</title>
<meta name="description" content="A collection of web pages vulnerable to command injection flaws, used to exercise commix's detection and exploitation features.">
<link rel="shortcut icon" href="favicon.ico">
<link href="https://fonts.googleapis.com/css?family=Exo+2:400,500,600,700" rel="stylesheet">
<link rel="stylesheet" href="css/testbed.css">
</head>
<body>

<nav class="nav">
  <div class="wrap">
    <a class="brand" href="index.php">
      <img src="img/testbed_header_logo.png" alt="commix-testbed"></a>
    <a class="gh" href="https://github.com/commixproject/commix-testbed">GitHub</a>
  </div>
</nav>

<div class="wrap">

  <header class="hero">
    <h1>A place to point commix at, before you point it at anything real.</h1>
    <p>Every page here contains a deliberate
      <a href="https://owasp.org/www-community/attacks/Command_Injection">command injection flaw</a>.
      They differ in what the application does with your input - where it lands, what it filters, and
      how much of the result it gives back - so each one exercises a different part of
      <a href="https://github.com/commixproject/commix">commix</a>. Open a hint when you want to know
      what a page is doing wrong.</p>
    <div class="stats">
      <div class="stat"><b>112</b><span>vulnerable pages</span></div>
      <div class="stat"><b>30</b><span>parameter scenarios</span></div>
      <div class="stat"><b>15</b><span>filter bypasses</span></div>
      <div class="stat"><b>15</b><span>injectable headers</span></div>
      <div class="stat"><b>13</b><span>stateful flows</span></div>
      <div class="stat"><b>3</b><span>discovery pages</span></div>
      <div class="stat"><b>8</b><span>CTF challenges</span></div>
    </div>
    <p class="warn"><b>Run this locally.</b> These pages execute whatever they are given. Do not expose
      the testbed to a network you do not control.</p>
  </header>

  <div class="bar">
    <input id="q" type="search" placeholder="Search scenarios, e.g. base64, blind, quote, JSON&hellip;" autocomplete="off">
    <div class="chips">
      <button class="chip" data-k="all" data-v="all" aria-pressed="true">All</button>
      <button class="chip" data-k="kind" data-v="classic" aria-pressed="false">Classic</button>
      <button class="chip" data-k="kind" data-v="blind" aria-pressed="false">Blind</button>
      <button class="chip" data-k="sink" data-v="code" aria-pressed="false">Code injection</button>
      <button class="chip" data-k="group" data-v="filters" aria-pressed="false">Filter bypass</button>
      <button class="chip" data-k="group" data-v="stateful" aria-pressed="false">Stateful</button>
      <button class="chip" data-k="group" data-v="discovery" aria-pressed="false">Discovery</button>
      <button class="chip" data-k="group" data-v="ctf" aria-pressed="false">CTF challenges</button>
      <button class="chip" data-k="lang" data-v="php" aria-pressed="false">PHP</button>
      <button class="chip" data-k="lang" data-v="python" aria-pressed="false">Python</button>
      <button class="chip" data-k="lang" data-v="ruby" aria-pressed="false">Ruby</button>
      <button class="chip" data-k="lang" data-v="javascript" aria-pressed="false">JavaScript</button>
    </div>
  </div>

  <main>
    <section class="group" id="regular">
      <h2><button class="fold" type="button" aria-expanded="true">Regular parameters <span class="count">30</span></button></h2>
      <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-lang="php" data-text="unfiltered parameter exec(&quot;/bin/ping -c 4 &quot;.$addr) with the output echoed straight back. nothing is filtered and nothing is quoted, so this is the plainest case there is. classic command injection php">
          <div class="card-top">
            <h3>Unfiltered parameter</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>exec("/bin/ping -c 4 ".$addr)</code> with the output echoed straight back. Nothing is filtered and nothing is quoted, so this is the plainest case there is.</p>
            <p class="lesson">Concatenating a request value into a shell command is the whole bug - there is no clever trick to find here. Every other scenario on this site is this same mistake with something placed in the way, so it is worth seeing what the undefended case looks like first. The fix is not to escape the value but to stop invoking a shell: pass the address as an argument to the program, never as part of a string the shell will parse.</p>
            <p class="reach">No flag needed beyond <code>-u</code>/<code>-p</code>/<code>-d</code> - nothing is filtered or quoted.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-lang="php" data-text="base64-encoded parameter the value is only accepted when base64_encode(base64_decode($x)) === $x, so a raw payload is rejected before it ever reaches the shell. it has to arrive base64-encoded. classic command injection php">
          <div class="card-top">
            <h3>Base64-encoded parameter</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_b64.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_b64.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The value is only accepted when <code>base64_encode(base64_decode($x)) === $x</code>, so a raw payload is rejected before it ever reaches the shell. It has to arrive Base64-encoded.</p>
            <p class="lesson">Encoding is not validation. The check only proves the value is well-formed Base64, and it is decoded straight into the command afterwards, so it filters nothing at all - it just changes the alphabet an attacker has to write in. Encodings that sit between the client and the sink hide a payload from anything inspecting the raw request, which is why a WAF in front of this page would see nothing worth blocking.</p>
            <p class="reach">Reach it with <code>--param-encoding=addr=base64</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-lang="php" data-text="hex-encoded parameter the same idea in hex: the value must satisfy bin2hex(pack(&#x27;h*&#x27;, $x)) === $x before it is unpacked into the command. classic command injection php">
          <div class="card-top">
            <h3>Hex-encoded parameter</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_hex.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_hex.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same idea in hex: the value must satisfy <code>bin2hex(pack('H*', $x)) === $x</code> before it is unpacked into the command.</p>
            <p class="lesson">The same lesson as the Base64 case, and it is worth trying both: the only thing that changes is the transport encoding, and the vulnerability underneath is identical. If a scanner can solve one and not the other, the gap is in its encoding support, not in the application.</p>
            <p class="reach">Reach it with <code>--param-encoding=addr=hex</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-lang="php" data-text="value inside single quotes the value lands inside single quotes: exec(&quot;ping &#x27;&quot;.$addr.&quot;&#x27;&quot;). a single-quoted word expands nothing, so the quote has to be closed before the shell reads any of it. classic command injection php">
          <div class="card-top">
            <h3>Value inside single quotes</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_quote.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_quote.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The value lands inside single quotes: <code>exec("ping '".$addr."'")</code>. A single-quoted word expands nothing, so the quote has to be closed before the shell reads any of it.</p>
            <p class="lesson">Quoting is often mistaken for escaping. Single quotes do stop the shell expanding anything inside them - but only until the value supplies a quote of its own and ends the quoted span early. Because the closing quote is still sitting in the template afterwards, the payload also has to leave the command syntactically valid, which is why a comment or a second quote usually shows up at the end of a working payload.</p>
            <p class="reach">Reach it with <code>--level=3</code> - closing a quote is only tried from level 3 up.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-lang="php" data-text="value inside double quotes as above but with double quotes - and a double-quoted word still expands $(...), so command substitution works here without closing anything first. classic command injection php">
          <div class="card-top">
            <h3>Value inside double quotes</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_double_quote.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_double_quote.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>As above but with double quotes - and a double-quoted word still expands <code>$(...)</code>, so command substitution works here without closing anything first.</p>
            <p class="lesson">This is the more dangerous of the two quoting mistakes, and the more common. A double-quoted word still performs command substitution and variable expansion, so the value does not even need to break out of the quotes - the shell will happily run what is inside them. Anyone reviewing this code and thinking the quotes make it safe has it backwards.</p>
            <p class="reach">Reach it with <code>--level=3</code> - closing a quote is only tried from level 3 up.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-lang="php" data-text="whitespace rejected preg_match(&#x27;/\s/&#x27;) throws the value out if it contains any whitespace at all, so the payload has to separate its words some other way. classic command injection php">
          <div class="card-top">
            <h3>Whitespace rejected</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_non_space.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_non_space.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>preg_match('/\s/')</code> throws the value out if it contains any whitespace at all, so the payload has to separate its words some other way.</p>
            <p class="lesson">Filters written against what a payload usually looks like rather than what the shell actually parses. The shell has several ways to separate words - a tab, a newline, brace expansion, or the value of $IFS - and this check knows about exactly one of them. It is a good illustration of why denylists lose: the author has to think of every alternative, the attacker only needs one.</p>
            <p class="reach">Reach it with <code>--tamper=space2ifs</code> - commix's own payloads still need a word separator of their own.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-lang="php" data-text="metacharacter blacklist a str_replace() strips ;, &amp;amp;&amp;amp;, | and backticks. it never looks at a single &amp;amp; or at a newline, and it only passes over the value once, so a stripped sequence can also be rebuilt around itself. classic command injection php">
          <div class="card-top">
            <h3>Metacharacter blacklist</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_blacklisting.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_blacklisting.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>A <code>str_replace()</code> strips <code>;</code>, <code>&amp;&amp;</code>, <code>|</code> and backticks. It never looks at a single <code>&amp;</code> or at a newline, and it only passes over the value once, so a stripped sequence can also be rebuilt around itself.</p>
            <p class="lesson">Two separate mistakes, either of which is fatal. The list is incomplete - a single & and a newline both chain commands and neither is on it - and the replacement runs once over the value, so a sequence written around a stripped one survives the pass that was meant to remove it. Stripping characters from input is nearly always the wrong shape of defence.</p>
            <p class="reach">No flag needed beyond <code>-u</code>/<code>-p</code> - a newline chains commands just as well as the separators this strips.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-lang="php" data-text="piped into md5sum the parameter is string rather than addr, and it is injected ahead of a pipe: exec(&#x27;echo &#x27;.$string.&#x27; | md5sum&#x27;). unix-only - the page refuses to run on windows. classic command injection php">
          <div class="card-top">
            <h3>Piped into md5sum</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_hash.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_hash.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The parameter is <code>string</code> rather than <code>addr</code>, and it is injected ahead of a pipe: <code>exec('echo '.$string.' | md5sum')</code>. Unix-only - the page refuses to run on Windows.</p>
            <p class="lesson">Worth opening because the injection point is not where you would look for it. The parameter is named string rather than addr, and the value is spliced in ahead of a pipe, so the command line continues after it. A scan that only tries parameters called addr, or that assumes the value sits at the end of the command, misses this one entirely.</p>
            <p class="reach">No flag needed beyond <code>-u</code>/<code>-p string</code> - commix tests whatever parameter is present.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-lang="php" data-text="behind basic authentication the injection point sits behind http basic authentication. the credentials are admin:admin; without them every request comes back 401 and nothing is tested. classic command injection php">
          <div class="card-top">
            <h3>Behind Basic authentication</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_basic_auth.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_basic_auth.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The injection point sits behind HTTP Basic authentication. The credentials are <code>admin:admin</code>; without them every request comes back 401 and nothing is tested.</p>
            <p class="lesson">Authentication is not mitigation. The flaw is identical to the plain case; it just needs valid credentials to reach. This scenario is here because an unauthenticated scan reports the page as not injectable, which is the wrong conclusion - it never actually tested it. Anything behind a login needs credentials supplied before the result means anything.</p>
            <p class="reach">Reach it with <code>--auth-type=basic --auth-cred=admin:admin</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-lang="php" data-text="behind digest authentication the same injection point behind http digest authentication, again admin:admin. digest needs the challenge answered per request rather than one static header. classic command injection php">
          <div class="card-top">
            <h3>Behind Digest authentication</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_digest_auth.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_digest_auth.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same injection point behind HTTP Digest authentication, again <code>admin:admin</code>. Digest needs the challenge answered per request rather than one static header.</p>
            <p class="lesson">The same point as the Basic case, with a handshake that cannot be replayed as one static header - Digest answers a per-request challenge. It is here to check that a tool follows the challenge rather than only knowing how to attach an Authorization header it prepared in advance.</p>
            <p class="reach">Reach it with <code>--auth-type=digest --auth-cred=admin:admin</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="regular" data-lang="php" data-text="output never reflected the command runs, but all the page says is whether the host seems to be up. with nothing to read, execution has to be proven some other way - a delay, a file, or a request to a server of your own. blind command injection php">
          <div class="card-top">
            <h3>Output never reflected</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/blind.php">GET</a><a class="m m-post" href="scenarios/regular/POST/blind.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The command runs, but all the page says is whether the host <em>seems to be up</em>. With nothing to read, execution has to be proven some other way - a delay, a file, or a request to a server of your own.</p>
            <p class="lesson">The command runs exactly as in the classic case; only the evidence is missing. This is the usual real-world situation and the reason blind techniques exist - an application that prints a tidy status message instead of raw output is not safer, it is just quieter. Proof has to come from something other than the page body: how long the reply takes, a file that appears, or a request arriving at a server you control.</p>
            <p class="reach">No flag needed beyond <code>-u</code>/<code>-p</code> - the blind techniques are tried by default.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="regular" data-lang="php" data-text="output and exit status discarded output goes to /dev/null and the command is backgrounded with &amp;amp;, so not even the exit status reaches the page. it looks identical whatever you send it. blind command injection php">
          <div class="card-top">
            <h3>Output and exit status discarded</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/double_blind.php">GET</a><a class="m m-post" href="scenarios/regular/POST/double_blind.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Output goes to <code>/dev/null</code> and the command is backgrounded with <code>&amp;</code>, so not even the exit status reaches the page. It looks identical whatever you send it.</p>
            <p class="lesson">One step past the previous page: with output redirected and the command backgrounded, even the success or failure of the command is invisible, so the response is byte-for-byte identical whatever you send. Timing is close to the only channel left, and the backgrounding means a delay has to be arranged carefully to still be observable.</p>
            <p class="reach">No flag needed beyond <code>-u</code>/<code>-p</code> - the injected separator ends the line before the trailing <code>&amp;</code>, so only that empty tail is backgrounded, not the payload.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="regular" data-lang="php" data-text="eval() on the value eval(&quot;echo \&quot;hello, &quot;.$user.&quot;!\&quot;;&quot;) - the value is evaluated as php, never handed to a shell. a shell separator means nothing here; the payload has to be valid php. classic code injection php">
          <div class="card-top">
            <h3>eval() on the value</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/eval.php">GET</a><a class="m m-post" href="scenarios/regular/POST/eval.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>eval("echo \"Hello, ".$user."!\";")</code> - the value is evaluated as PHP, never handed to a shell. A shell separator means nothing here; the payload has to be valid PHP.</p>
            <p class="lesson">A different sink with the same consequence. Nothing here reaches a shell, so shell metacharacters are meaningless - a semicolon does not chain anything, it is just a semicolon in a PHP statement. The payload has to be valid code in the language doing the evaluating, which is why commix treats this as a separate kind of injection rather than a variant of the command case.</p>
            <p class="reach">Reach it with <code>--eval=php</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="regular" data-lang="php" data-text="eval() on a base64 value the same evaluated sink, except the value must be valid base64 before it is decoded into the eval(). classic code injection php">
          <div class="card-top">
            <h3>eval() on a Base64 value</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/eval_b64.php">GET</a><a class="m m-post" href="scenarios/regular/POST/eval_b64.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same evaluated sink, except the value must be valid Base64 before it is decoded into the <code>eval()</code>.</p>
            <p class="lesson">Combines the two ideas already met separately: an evaluated sink reached through an encoding. Useful for checking that encoding and sink are handled independently rather than as one hard-coded combination.</p>
            <p class="reach">Reach it with <code>--eval=php --param-encoding=user=base64</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-lang="php" data-text="json request body the body is json read from php://input and its addr member goes into exec(). the payload has to sit inside the json rather than be appended to it. classic command injection php">
          <div class="card-top">
            <h3>JSON request body</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/regular/POST/classic_json.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The body is JSON read from <code>php://input</code> and its <code>addr</code> member goes into <code>exec()</code>. The payload has to sit inside the JSON rather than be appended to it.</p>
            <p class="lesson">The vulnerability is ordinary; what is different is where the value lives. The body is parsed as JSON, so a payload appended to the request is not a parameter at all - it has to be placed inside a member and stay valid JSON, with whatever quoting and escaping that implies. Plenty of tooling that handles form bodies fails here.</p>
            <p class="reach">No flag needed beyond <code>-u</code>/<code>-d</code> - commix recognises a JSON body and places the payload inside it.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="regular" data-lang="php" data-text="blind, but the page says whether it worked the command runs and its output is thrown away. all the page says is whether it succeeded, taken from the exit status. string content oracle blind command injection php">
          <div class="card-top">
            <h3>Blind, but the page says whether it worked</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/blind_exit_status.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The command runs and its output is thrown away. All the page says is whether it succeeded - <code>Host is up</code> or <code>Host is down</code>, taken from the exit status.</p>
            <p class="lesson">Blind, but not silent. Nothing of the output comes back, yet the page still answers one question about what happened - and a question with a yes and a no is all a search needs. Chain a test onto the command and its exit status becomes the answer, a byte at a time, without the target ever printing one.</p>
            <p class="reach">Reach it with <code>--string</code>, anchored to the element the result is printed into rather than to the words alone - the page explains itself, so the words appear in its prose as well.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-lang="php" data-text="graphql document, argument evaluated the body is a graphql document and its host argument goes into a shell. sent as application/graphql or as the json envelope a client sends, with the argument written in or passed in variables. classic command injection php">
          <div class="card-top">
            <h3>GraphQL document, argument evaluated</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/regular/POST/classic_graphql.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The body is a GraphQL document and its <code>host</code> argument goes into a shell. It is read the same whether it arrives under <code>application/graphql</code>, inside the JSON envelope a client sends, or with the argument passed beside the document in <code>variables</code>.</p>
            <p class="lesson">A document is not a list of pairs. A payload appended to the whole <code>query</code> lands past its last brace and only produces a syntax error, which reads as a parameter that is not injectable - so what has to be tested is the argument inside, with the request staying a valid document throughout.</p>
            <p class="reach">Send it either way: as a document of its own under <code>application/graphql</code>, or as the JSON envelope a client sends. Both are read the same.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="regular" data-lang="php" data-text="json request body, nothing returned the json case with no output in the response. blind command injection php">
          <div class="card-top">
            <h3>JSON request body, nothing returned</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/regular/POST/blind_json.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The JSON case with no output in the response.</p>
            <p class="lesson">The structured-body case with the response closed off, so it needs the body handling and a blind technique at the same time.</p>
            <p class="reach">No flag needed beyond <code>-u</code>/<code>-d</code> - commix still recognises the JSON body and the blind techniques are tried by default.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="regular" data-lang="php" data-text="json request body, evaluated an evaluated sink reached through a json member rather than a form field. classic code injection php">
          <div class="card-top">
            <h3>JSON request body, evaluated</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/regular/POST/eval_json.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>An evaluated sink reached through a JSON member rather than a form field.</p>
            <p class="lesson">A structured body feeding an evaluated sink - the two complications that are usually met separately, on one page.</p>
            <p class="reach">Reach it with <code>--eval=php</code> - commix still recognises the JSON body on its own.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-lang="php" data-text="soap/xml request body the body is xml parsed with libxml_noent | libxml_dtdload, and both &amp;lt;addr&amp;gt; and &amp;lt;count&amp;gt; are concatenated into the command - two injection points on one page, not one. classic command injection php">
          <div class="card-top">
            <h3>SOAP/XML request body</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/regular/POST/classic_xml.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The body is XML parsed with <code>LIBXML_NOENT | LIBXML_DTDLOAD</code>, and both <code>&lt;addr&gt;</code> and <code>&lt;count&gt;</code> are concatenated into the command - two injection points on one page, not one.</p>
            <p class="lesson">Two things to notice. Both addr and count reach the command, so the page has more than one injection point and finding only the first one is an incomplete result. The parser is also built with LIBXML_NOENT and LIBXML_DTDLOAD, which resolves external entities - so on top of the command injection this document is an XXE waiting to happen.</p>
            <p class="reach">No flag needed beyond <code>-u</code>/<code>-d</code> - commix recognises an XML body and places the payload inside it.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="regular" data-lang="php" data-text="soap/xml request body, nothing returned the xml case with no output reflected back. blind command injection php">
          <div class="card-top">
            <h3>SOAP/XML request body, nothing returned</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/regular/POST/blind_xml.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The XML case with no output reflected back.</p>
            <p class="lesson">The XML case with nothing reflected, so the injection point has to be found and confirmed without ever seeing output.</p>
            <p class="reach">No flag needed beyond <code>-u</code>/<code>-d</code> - commix still recognises the XML body and the blind techniques are tried by default.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-lang="php" data-text="ip-format validation the value has to look like an ip address - but the pattern is /^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$/m, and that m flag makes ^ and $ match at every line rather than once. a second line walks straight past it. classic command injection php">
          <div class="card-top">
            <h3>IP-format validation</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/preg_match.php">GET</a><a class="m m-post" href="scenarios/regular/POST/preg_match.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The value has to look like an IP address - but the pattern is <code>/^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$/m</code>, and that <code>m</code> flag makes <code>^</code> and <code>$</code> match at every line rather than once. A second line walks straight past it.</p>
            <p class="lesson">The most instructive filter on the site. The author did the right thing - validate the shape of the value - and still lost, to one letter. The m flag makes ^ and $ match at every line rather than at the start and end of the whole value, so a string whose first line is a valid IP passes no matter what follows on the next line. Anchors without that flag, or a check that the value contains no newline, would have held.</p>
            <p class="reach">Reach it with <code>--suffix=$'\n127.0.0.1'</code> - a trailing line the regex still accepts, once the real payload has already run.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="regular" data-lang="php" data-text="ip-format validation, nothing returned the same multiline-anchor mistake, this time with nothing readable in the response. blind command injection php">
          <div class="card-top">
            <h3>IP-format validation, nothing returned</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/preg_match_blind.php">GET</a><a class="m m-post" href="scenarios/regular/POST/preg_match_blind.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same multiline-anchor mistake, this time with nothing readable in the response.</p>
            <p class="lesson">The same anchor mistake with the response closed off, so the bypass has to be found blind.</p>
            <p class="reach">Reach it with <code>--suffix=$'\n127.0.0.1'</code>, the same trailing line as the non-blind case.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="regular" data-lang="php" data-text="quotes stripped, then evaluated backslashes and both kinds of quote are removed, then the value is dropped into a double-quoted eval(&quot;echo(\&quot;$user\&quot;);&quot;). quotes are not needed: php expands ${...} inside a double-quoted string. classic code injection php">
          <div class="card-top">
            <h3>Quotes stripped, then evaluated</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/str_replace.php">GET</a><a class="m m-post" href="scenarios/regular/POST/str_replace.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Backslashes and both kinds of quote are removed, then the value is dropped into a double-quoted <code>eval("echo(\"$user\");")</code>. Quotes are not needed: PHP expands <code>${...}</code> inside a double-quoted string.</p>
            <p class="lesson">A sanitiser that removes exactly the characters the author imagined an attacker needs. Backslashes and both quotes are stripped - but the value lands in a double-quoted PHP string, where ${...} performs an expansion without a single quote being involved. The defence and the attack are aimed at different things.</p>
            <p class="reach">Reach it with <code>--eval=php</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="code" data-group="regular" data-lang="php" data-text="eval() with nothing printed back the value is evaluated as php - eval(&#x27;$greeting = &quot;hello, &#x27; . $user . &#x27;!&quot;;&#x27;) - but the greeting it builds is never printed. the page answers with the same fixed sentence whatever you send it. blind code injection php">
          <div class="card-top">
            <h3>eval() with nothing printed back</h3>
            <span class="badges"><span class="type t-blind" title="Blind code injection">Blind</span><span class="tag">code</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/eval_blind.php">GET</a><a class="m m-post" href="scenarios/regular/POST/eval_blind.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The value is evaluated as PHP - <code>eval('$greeting = "Hello, ' . $user . '!";')</code> - but the greeting it builds is never printed. The page answers with the same fixed sentence whatever you send it.</p>
            <p class="lesson">The fourth corner of the matrix, and the hardest of the four to find. The sink is an evaluated string rather than a shell, so shell metacharacters do nothing - and the result is never printed, so there is nothing to read either. Both of the usual footholds are gone at once: the payload has to be valid PHP, and the proof has to come from timing or a side effect. A page that quietly evaluates input and says only 'saved' is the realistic shape of this bug.</p>
            <p class="reach">Reach it with <code>--eval=php</code>.</p>
            <p class="verdict">Reported by commix as <b>Blind code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="regular" data-lang="php" data-text="attacker-controlled regex both the pattern and the replacement come from the query string, so the pattern&#x27;s own modifiers belong to the attacker - and the e modifier evaluates the replacement as php. classic code injection php">
          <div class="card-top">
            <h3>Attacker-controlled regex</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/preg_replace.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Both the pattern and the replacement come from the query string, so the pattern's own modifiers belong to the attacker - and the <code>e</code> modifier evaluates the replacement as PHP.</p>
            <p class="lesson">The pattern itself is user input, which hands over far more than the text being matched: a regex carries modifiers, and PHP's e modifier evaluated the replacement as code. It was removed in PHP 7, so this page needs an older interpreter - but the shape of the mistake outlives the feature. Never let a request supply the pattern to a matching function.</p>
            <p class="reach">Reach it with <code>--eval=php</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="regular" data-lang="php" data-text="assert() on the value assert(trim(&quot;&#x27;&quot;.$_get[&#x27;user&#x27;].&quot;&#x27;&quot;)) evaluates its argument as php. the value is wrapped in single quotes, so those have to be closed first. classic code injection php">
          <div class="card-top">
            <h3>assert() on the value</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/assert.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>assert(trim("'".$_GET['user']."'"))</code> evaluates its argument as PHP. The value is wrapped in single quotes, so those have to be closed first.</p>
            <p class="lesson">assert() took a string and evaluated it as PHP, which makes it a code-execution sink that reads like a sanity check. PHP 7 deprecated the string form and PHP 8 stopped evaluating it; the lesson is that any function taking code as a string is a sink, however harmless its name sounds.</p>
            <p class="reach">Reach it with <code>--eval=php</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="regular" data-lang="php" data-text="create_function() on the value the value is concatenated into the body of a create_function(), which compiles it as php. classic code injection php">
          <div class="card-top">
            <h3>create_function() on the value</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/create_function.php">GET</a><a class="m m-post" href="scenarios/regular/POST/create_function.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The value is concatenated into the body of a <code>create_function()</code>, which compiles it as PHP.</p>
            <p class="lesson">create_function() built a function by compiling a string, so anything concatenated into its body became code. It was deprecated in PHP 7.2 and removed in 8. Like assert(), the dangerous part is not the syntax but the idea: text that becomes code.</p>
            <p class="reach">Reach it with <code>--eval=php</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-lang="php" data-text="only one of three parameters is validated three values are submitted and two of them are checked - addr against a host pattern and label against word characters. count is concatenated into the command with no check at all. classic command injection php">
          <div class="card-top">
            <h3>Only one of three parameters is validated</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/multiple_params.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Three values are submitted and two of them are checked - <code>addr</code> against a host pattern and <code>label</code> against word characters. <code>count</code> is concatenated into the command with no check at all.</p>
            <p class="lesson">The most realistic scenario here. Someone did think about input validation - they just did not finish. A count, an id, a page number or a sort order rarely looks dangerous enough to check, and it reaches the same shell as the field that did get a regex. Testing one parameter and declaring the page safe is how this survives a review.</p>
            <p class="reach">No flag needed beyond <code>-u</code> - commix tests all three parameters and finds the one left unchecked.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-lang="php" data-text="put requests only the endpoint turns away anything that is not a put, and reads its body with parse_str() rather than from $_post. classic command injection php">
          <div class="card-top">
            <h3>PUT requests only</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-put" href="scenarios/regular/PUT/classic.php">PUT</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The endpoint turns away anything that is not a <code>PUT</code>, and reads its body with <code>parse_str()</code> rather than from <code>$_POST</code>.</p>
            <p class="lesson">The vulnerability is ordinary; reaching it is not. A scan that only sends GET and POST never gets a useful response from this endpoint, and the body has to be parsed out of the raw request rather than read from $_POST. Plenty of an application's surface sits behind methods nobody thinks to try.</p>
            <p class="reach">Reach it with <code>--method=PUT</code>, alongside <code>-d</code> for the body.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
      </div>
    </section>

    <section class="group" id="filters">
      <h2><button class="fold" type="button" aria-expanded="true">Weak filters <span class="count">15</span></button></h2>
      <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-lang="php" data-text="a filter that reads a different parameter the check reads the first addr in the query string and php keeps the last. hpp http parameter pollution classic command injection php">
          <div class="card-top">
            <h3>A filter that reads a different parameter</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/filters/hpp.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The check walks the raw query string and stops at the first <code>addr</code>. PHP keeps the last one, and that is what reaches the shell.</p>
            <p class="lesson">A filter and the application behind it do not have to agree about which of two same-named parameters counts. Nothing about the check is wrong on its own - it simply inspects a different value from the one that runs.</p>
            <p class="reach">Reach it with <code>--hpp</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-lang="php" data-text="lax domain validation /^\w+\..*\w+\.\w+$/ looks like it demands a domain name, but the .* in the middle accepts anything at all. keep a domain-shaped head and tail and the middle is yours. classic command injection php">
          <div class="card-top">
            <h3>Lax domain validation</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/lax_domain_name.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>/^\w+\..*\w+\.\w+$/</code> looks like it demands a domain name, but the <code>.*</code> in the middle accepts anything at all. Keep a domain-shaped head and tail and the middle is yours.</p>
            <p class="lesson">A pattern that looks strict read quickly and is not. \w+\. at the front and \w+\.\w+ at the end are real constraints, but .* between them accepts anything at all, including separators and a whole command. A regex has to be read for what it permits, not for what it appears to describe.</p>
            <p class="reach">Reach it with <code>--prefix='a.' --suffix='.b.c'</code> - a domain-shaped head and tail around the payload.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-lang="php" data-text="value inside nested quotes exec(&quot;/bin/ping -c 4 \&quot;{$addr}\&quot;&quot;) - the value sits inside double quotes that are themselves inside a php double-quoted string. classic command injection php">
          <div class="card-top">
            <h3>Value inside nested quotes</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/nested_quotes.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>exec("/bin/ping -c 4 \"{$addr}\"")</code> - the value sits inside double quotes that are themselves inside a PHP double-quoted string.</p>
            <p class="lesson">Two layers of quoting - PHP's around the string, the shell's inside it - which mostly serves to make the code hard to reason about. Working out which layer consumes which character is the exercise here; the shell only ever sees the result of the PHP layer.</p>
            <p class="reach">Reach it with <code>--level=3</code> - closing a quote is only tried from level 3 up.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-lang="php" data-text="separators blocked: ; | &amp;amp; $ four metacharacters are blacklisted. a newline is not one of them, and it ends a command just as well as a semicolon does. classic command injection php">
          <div class="card-top">
            <h3>Separators blocked: ; | &amp; $</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/no_colon_no_pipe_no_ampersand_no_dollar.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Four metacharacters are blacklisted. A newline is not one of them, and it ends a command just as well as a semicolon does.</p>
            <p class="lesson">A denylist of the separators people write down from memory. A newline is not on it, and a newline ends a command as surely as a semicolon does - it is simply harder to see in a request. Any filter that enumerates metacharacters should be assumed incomplete.</p>
            <p class="reach">Reach it with <code>--skip-subst --skip-calc</code> - $ is on the list too, which rules out commix's own usual substitution and arithmetic markers.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-lang="php" data-text="spaces blocked only the literal space is rejected. a tab, a newline or ${ifs} all still separate one word from the next. classic command injection php">
          <div class="card-top">
            <h3>Spaces blocked</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/no_space.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Only the literal space is rejected. A tab, a newline or <code>${IFS}</code> all still separate one word from the next.</p>
            <p class="lesson">Only the literal space character is rejected, which leaves every other way the shell has of separating words: a tab, a newline, ${IFS}, or brace expansion. The check reflects what payloads look like rather than how the shell parses them.</p>
            <p class="reach">Reach it with <code>--tamper=space2ifs</code> - commix's own payloads still need a word separator of their own.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-lang="php" data-text="spaces and three separators blocked whitespace and ; | &amp;amp; are gone, which leaves the newline to chain with and ${ifs} to separate words. classic command injection php">
          <div class="card-top">
            <h3>Spaces and three separators blocked</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/no_space_no_colon_no_pipe_no_ampersand.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Whitespace and <code>;</code> <code>|</code> <code>&amp;</code> are gone, which leaves the newline to chain with and <code>${IFS}</code> to separate words.</p>
            <p class="lesson">More characters on the list, and the same weakness - what is left is still enough. It is worth working out which combination survives here rather than reaching for a tool, because the answer says exactly what the filter forgot.</p>
            <p class="reach">Reach it with <code>--tamper=space2ifs</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-lang="php" data-text="spaces, separators and $ blocked with $ gone too, ${ifs} is out - but a tab is still whitespace to the shell and it is not a space. classic command injection php">
          <div class="card-top">
            <h3>Spaces, separators and $ blocked</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/no_space_no_colon_no_pipe_no_ampersand_no_dollar.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>With <code>$</code> gone too, <code>${IFS}</code> is out - but a tab is still whitespace to the shell and it is not a space.</p>
            <p class="lesson">With $ gone, the ${IFS} trick that solved the previous pages is unavailable, so this one forces a different answer for the same problem. A good demonstration that a bypass is not a universal key - it is specific to what a particular filter neglected.</p>
            <p class="reach">Reach it with <code>--tamper=space2htab --skip-subst --skip-calc</code> - a tab needs no $ sign, unlike ${IFS}.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-lang="php" data-text="all whitespace blocked /\s+/ covers the tab and the newline as well as the space, so the word separator has to be something that expands into whitespace rather than something that is it. classic command injection php">
          <div class="card-top">
            <h3>All whitespace blocked</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/no_white_chars.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>/\s+/</code> covers the tab and the newline as well as the space, so the word separator has to be something that <em>expands</em> into whitespace rather than something that is it.</p>
            <p class="lesson">\s covers space, tab and newline together, which is a genuinely tighter check than the pages before it. It still does not help: the word separator does not have to be whitespace in the request, only to become whitespace by the time the shell splits the line.</p>
            <p class="reach">Reach it with <code>--tamper=space2ifs</code> - ${IFS} carries no whitespace character of its own.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-lang="php" data-text="must start with alphanumerics /^\w+/ - the value only has to begin with word characters. everything after that first run goes unchecked. classic command injection php">
          <div class="card-top">
            <h3>Must start with alphanumerics</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/simple_start_alphanum.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>/^\w+/</code> - the value only has to <em>begin</em> with word characters. Everything after that first run goes unchecked.</p>
            <p class="lesson">A check that constrains only the beginning of the value and says nothing about the rest, so anything appended after that first run of word characters goes straight through. Partial validation is not validation.</p>
            <p class="reach">No flag needed beyond <code>-u</code>/<code>-p</code> - nothing here restricts what follows the leading word characters.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-lang="php" data-text="must end with alphanumerics /\w+$/ - the value only has to end with word characters, so the payload goes in front and a harmless word closes it. classic command injection php">
          <div class="card-top">
            <h3>Must end with alphanumerics</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/simple_stop_alphanum.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>/\w+$/</code> - the value only has to <em>end</em> with word characters, so the payload goes in front and a harmless word closes it.</p>
            <p class="lesson">The mirror image: only the end is constrained, so the payload goes in front and a harmless word satisfies the anchor. Seeing both pages together makes the point that an anchor pins one end of a string and leaves the other free.</p>
            <p class="reach">No flag needed beyond <code>-u</code>/<code>-p</code> - nothing here restricts what precedes the trailing word characters.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-lang="php" data-text="must start with alphanumerics, no whitespace both conditions at once: begin with word characters, and carry no whitespace anywhere. classic command injection php">
          <div class="card-top">
            <h3>Must start with alphanumerics, no whitespace</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/no_white_chars_start_alphanum.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Both conditions at once: begin with word characters, and carry no whitespace anywhere.</p>
            <p class="lesson">Two partial checks stacked. Neither is sufficient alone and together they only narrow the shape of a working payload rather than preventing one - the requirements can be met at the same time.</p>
            <p class="reach">Reach it with <code>--tamper=space2ifs</code> - the leading-alphanumeric part is already satisfied by commix's own marker.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-lang="php" data-text="must end with alphanumerics, no whitespace end with word characters and carry no whitespace - so the suffix has to satisfy the anchor without putting a space back. classic command injection php">
          <div class="card-top">
            <h3>Must end with alphanumerics, no whitespace</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/no_white_chars_stop_alnum.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>End with word characters and carry no whitespace - so the suffix has to satisfy the anchor without putting a space back.</p>
            <p class="lesson">The same pair of constraints anchored at the other end. The interesting part is that the suffix satisfying the anchor must not reintroduce the whitespace the other check forbids.</p>
            <p class="reach">Reach it with <code>--tamper=space2ifs</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-lang="php" data-text="command names blacklisted the command names themselves are matched: echo|wget|nc|whoami|cat|ncat on unix and powershell|cmd on windows. the match is on literal text, so anything that hides the letters from the regex without hiding them from the shell gets through. classic command injection php">
          <div class="card-top">
            <h3>Command names blacklisted</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/multiple_os_commands_blacklisting.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The command names themselves are matched: <code>echo|wget|nc|whoami|cat|ncat</code> on Unix and <code>powershell|cmd</code> on Windows. The match is on literal text, so anything that hides the letters from the regex without hiding them from the shell gets through.</p>
            <p class="lesson">Blocking command names rather than metacharacters, which fails for a different reason: the regex matches literal text, while the shell sees the string only after its own expansions have run. Anything that breaks up the letters on the way to the shell defeats the match without changing what executes. There are also far more ways to read a file than the six names on this list.</p>
            <p class="reach">No flag needed beyond <code>-u</code>/<code>-p</code> - commix already falls back to <code>printf</code> where <code>echo</code> is blocked.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="filters" data-lang="php" data-text="function names blacklisted a blacklist sits in front of an eval() sink and refuses the request outright where the query string contains system exec shell_exec passthru popen proc_open assert call_user_func a backtick or the names of the commands worth running. it reads the query string and only the query string. results-based code injection php">
          <div class="card-top">
            <h3>Function names blacklisted</h3>
            <span class="badges"><span class="type t-classic" title="Results-based code injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/filters/eval_function_blacklisting.php?user=test">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>A blacklist in front of an <code>eval()</code> sink refuses the request outright - <code>403</code> and nothing else - where the query string contains any of <code>system</code>, <code>exec</code>, <code>shell_exec</code>, <code>passthru</code>, a backtick, or the names of the commands worth running. It reads the query string and only the query string.</p>
            <p class="lesson">The shape most filtering actually takes: a list of words matched against the part of the request the filter was written to watch. What defeats it is not a cleverer spelling of those words but putting them somewhere the filter never looks.</p>
            <p class="reach">Reach it with <code>--eval=php --tamper=phpserverheaders</code>.</p>
            <p class="verdict">Reported by commix as <b>Results-based code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="filters" data-lang="php" data-text="dash blocked a filter rejects the value if it contains a bare hyphen, meant to stop flag injection into ping. every separator and every command substitution syntax still gets through untouched. blind command injection php">
          <div class="card-top">
            <h3>Dash blocked</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/no_dash.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>A filter rejects the value if it contains a bare <code>-</code> - written to stop flag injection into <code>ping</code>, not to stop command chaining. Every separator and every command-substitution syntax still gets through untouched.</p>
            <p class="lesson">A character blocked for what it does to one program's own argument parsing is still blocked everywhere else it appears in the value - including inside <code>test</code>'s own <code>-eq</code>/<code>-le</code>, which a blind technique's own proof-of-execution is built from. The command itself runs and chains exactly as it would unfiltered; only the check confirming it did needs a dash-free spelling.</p>
            <p class="reach">Reach it with <code>--tamper=dashlesstest</code>.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-lang="php" data-text="command name blacklist a dozen common shell/recon command names are blocked as whole words, case-insensitively - but the list names programs, not syntax, and nothing here calls a particular shell. ansi-c quoting is bash/ksh/zsh syntax and fails outright under this target's default dash. classic command injection php posix dash printf">
          <div class="card-top">
            <h3>Command name blacklist</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/command_name_blacklist.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>A dozen common shell/recon command names are blocked as whole words, case-insensitively - but the list names programs, not syntax, and every separator, quote and <code>$(...)</code> itself still gets through.</p>
            <p class="lesson">Nothing here calls a particular shell, so PHP's <code>exec()</code> reaches its own default - <code>dash</code> on this (and most Debian/Ubuntu) targets, not <code>bash</code>. ANSI-C quoting (<code>$'\NNN...'</code>) is <code>bash</code>/<code>ksh</code>/<code>zsh</code> syntax and fails outright there; a command substitution around <code>printf</code>'s own octal escapes is plain POSIX and reaches every blacklisted name instead.</p>
            <p class="reach">Reach it with <code>--tamper=printfoctal</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
      </div>
    </section>

    <section class="group" id="ctf">
      <h2><button class="fold" type="button" aria-expanded="true">CTF challenges <span class="count">8</span></button></h2>
      <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="ctf" data-lang="php" data-text="terminal isolation escapeshellarg() wraps the value in single quotes, but the echo command's own template re-wraps that already-quoted value inside double quotes - where $(...) command substitution still expands. classic command injection php yeswehack dojo 30">
          <div class="card-top">
            <h3>Terminal isolation</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/ctf/dojo30_terminal_isolation.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>escapeshellarg()</code> wraps the value in single quotes, but the <code>echo</code> command's own template re-wraps that already-quoted value inside <b>double</b> quotes - where <code>$(...)</code> command substitution still expands.</p>
            <p class="lesson">Two layers of quoting that do not agree are worse than one: the protection the first layer built no longer applies once the second wraps it in something else.</p>
            <p class="reach">Reach it with <code>--tamper=space2ifsraw,cmd2redir --technique=r</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>. Based on <a href="https://dojo-yeswehack.com/challenge-of-the-month/Dojo-30">YesWeHack Dojo #30: Terminal Isolation</a>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="ctf" data-lang="php" data-text="shell escape a filter rejects the request if it contains any letter or common punctuation, but every shell metacharacter that matters for chaining a command still gets through. classic command injection php yeswehack dojo 36">
          <div class="card-top">
            <h3>Shell escape</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/ctf/dojo36_shell_escape.php?cmd=id">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>A filter rejects the request outright if it contains any letter or common punctuation - but every shell metacharacter that matters for chaining a command still gets through untouched.</p>
            <p class="lesson">A command name is just letters spelled another way. ANSI-C quoting spells them as octal escapes instead, none of which a letter-blocking filter ever sees.</p>
            <p class="reach">Reach it with <code>--tamper=ansiquoteoctal --skip-calc</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>. Based on <a href="https://www.yeswehack.com/dojo/dojo-ctf-challenge-winners-36">YesWeHack Dojo #36: Shell Escape</a>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="ctf" data-lang="ruby" data-text="ruby treasure a filename is validated per line rather than across the whole string, so one clean line anywhere in a multi-line value is enough. IO.read runs the rest as a shell command when the value starts with a pipe. classic command injection ruby yeswehack dojo 41">
          <div class="card-top">
            <h3>Ruby treasure</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">Ruby</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/ctf/dojo41_ruby_treasure.rb?page=x">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>A filename is validated with <code>^[a-zA-Z0-9_-]+$</code>, but Ruby matches <code>^</code>/<code>$</code> per line rather than across the whole string - one clean line anywhere in a multi-line value is enough. <code>IO.read</code> then runs the rest as a shell command when the value starts with <code>|</code>.</p>
            <p class="lesson">Two Ruby-specific behaviors, neither a bug on its own, that only add up to one together.</p>
            <p class="reach">Reach it with <code>--suffix=$'\nx' --technique=r</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>. Based on <a href="https://www.yeswehack.com/dojo/dojo-ctf-challenge-winners-41">YesWeHack Dojo #41: Ruby Treasure</a>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="ctf" data-lang="python" data-text="ghost whisper a quote is replaced before the value is normalized. unicodedata.normalize nfkc folds a fullwidth apostrophe into a real quote afterwards, closing a quote the filter believed it had already neutralized. classic command injection python yeswehack dojo 46">
          <div class="card-top">
            <h3>Ghost whisper</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">Python</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/ctf/dojo46_ghost_whisper.py">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>A quote is replaced before the value is normalized - which is exactly backwards. <code>unicodedata.normalize("NFKC", ...)</code> folds a fullwidth apostrophe into a real <code>'</code> afterwards, closing the quote the filter believed it had already neutralized.</p>
            <p class="lesson">Sanitizing before normalizing gets the order backwards: whatever runs last is the one the filter needed to see.</p>
            <p class="reach">Reach it with <code>--level=3 --tamper=unicodequote --skip-calc --technique=r</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>. Based on <a href="https://www.yeswehack.com/dojo/dojo-challenge-solution-46">YesWeHack Dojo #46: Ghost Whisper</a>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="ctf" data-lang="php" data-text="filter bypass a blacklist strips backslash dollar pipe backtick semicolon ampersand angle brackets and bracket outright, leaving neither of posix shell's two command-substitution syntaxes standing. only a bare newline still separates commands. blind command injection php root-me">
          <div class="card-top">
            <h3>Filter bypass</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/ctf/rootme_filter_bypass.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>A blacklist strips <code>\ $ | ` ; &amp; &lt; &gt; ]</code> outright, leaving neither of POSIX shell's two command-substitution syntaxes standing - only a bare newline still separates commands.</p>
            <p class="lesson">A blacklist stops exactly the characters it names, and nothing here names the one separator that still works.</p>
            <p class="reach">Reach it with <code>--technique=t --skip-subst</code>.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>. Based on <a href="https://www.root-me.org/en/Challenges/Web-Server/Command-injection-Filter-bypass">Root-Me: Command injection - Filter bypass</a>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="ctf" data-lang="php" data-text="php eval-hard a filter rejects the request outright if it contains any letter. eval() runs whatever is left. php decodes a double-quoted or backtick string's own octal escapes only once eval() is already running it, by which point the filter has long since passed four digits and a backslash. classic code injection php root-me">
          <div class="card-top">
            <h3>PHP Eval-hard</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/ctf/rootme_eval_hard.php?code=___">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>A filter rejects the request outright if it contains <b>any letter</b> - digits, <code>$</code>, <code>_</code>, quotes and backticks all pass, and the rest reaches <code>eval()</code>.</p>
            <p class="lesson">PHP decodes a double-quoted or backtick string's own octal escapes (<code>\NNN</code>) only once <code>eval()</code> is already running it - by then the filter has long since looked at, and passed, four digits and a backslash. A function reached by a name built the same way never writes <code>system</code> or <code>exec</code> either.</p>
            <p class="reach">Reach it with <code>--tamper=phpnoletters</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>. Based on <a href="https://www.root-me.org/en/Challenges/Web-Server/PHP-Eval-hard">Root-Me: PHP - Eval-hard</a>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="ctf" data-lang="php" data-text="natas16 dictionary search a filter blocks semicolon pipe ampersand backtick and both quotes, but not dollar parenthesis. bash expands command substitution inside the grep argument's own double quotes before grep ever runs, and a redirect inside it leaves nothing for the substitution to return. blind command injection php overthewire natas">
          <div class="card-top">
            <h3>Natas16: dictionary search</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/ctf/natas16_dictionary_search.php?needle=apple">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>A filter blocks <code>; | &amp; ` ' "</code> but not <code>$</code>, <code>(</code>, <code>)</code> or <code>&gt;</code> - and the value sits inside <code>grep -i "value" dictionary.txt</code>, itself already double-quoted.</p>
            <p class="lesson">Bash expands <code>$(...)</code> inside double quotes before the outer command ever runs. A command whose output is redirected to a file, rather than returned, leaves the substitution empty - and the real answer waits on disk under a name the request already knows.</p>
            <p class="reach">Reach it with <code>--prefix='$(' --suffix=')' --technique=f --web-root=&lt;document root&gt;</code>.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>. Based on <a href="https://overthewire.org/wargames/natas/natas16.html">OverTheWire: Natas Level 16</a>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="ctf" data-lang="php" data-text="dvwa command injection (high) a blacklist strips ampersand semicolon dash dollar sign parens backtick and pipe-followed-by-space, stripped by exact substring not character class - a bare pipe or a newline chains a command exactly as well. dash blocked everywhere breaks test's own -eq/-ne, dollar blocked everywhere breaks most output-reading techniques. classic command injection php dvwa">
          <div class="card-top">
            <h3>DVWA: Command Injection</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/ctf/dvwa_command_injection.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>A blacklist strips <code>&amp; ; - $ ( ) `</code>, <code>||</code> and <code>| </code> (pipe-then-space, as an exact two-character match) - a bare pipe or a newline chains a second command exactly as well as anything on the list.</p>
            <p class="lesson"><code>-</code> stripped everywhere breaks <code>test</code>'s own <code>-eq</code>/<code>-ne</code> the same way it breaks the ping flag the blacklist meant to stop, and <code>$</code> stripped everywhere takes most output-reading techniques with it.</p>
            <p class="reach">Reach it with <code>--skip-subst --skip-calc --tamper=dashlesstest --technique=f --web-root=&lt;document root&gt;</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>. Based on <a href="https://github.com/digininja/DVWA">DVWA: Command Injection</a> (security level "high").</p>
          </div>
        </article>
      </div>
    </section>

    <section class="group" id="python">
      <h2><button class="fold" type="button" aria-expanded="true">Python scenarios <span class="count">3</span></button></h2>
      <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="python" data-lang="python" data-text="unfiltered parameter subprocess.check_output(cmd, shell=true) builds the command by concatenation, so the value is parsed by a shell before anything runs, and the output comes back on the page. classic command injection python">
          <div class="card-top">
            <h3>Unfiltered parameter</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">Python</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/python/classic.py">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>subprocess.check_output(cmd, shell=True)</code> builds the command by concatenation, so the value is parsed by a shell before anything runs, and the output comes back on the page.</p>
            <p class="lesson">Concatenating a request value into a shell command is the whole bug - there is no clever trick to find here. Every other scenario on this site is this same mistake with something placed in the way, so it is worth seeing what the undefended case looks like first. The fix is not to escape the value but to stop invoking a shell: pass the address as an argument to the program, never as part of a string the shell will parse.</p>
            <p class="reach">No flag needed beyond <code>-u</code>/<code>-p</code>/<code>-d</code> - nothing is filtered or quoted.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="python" data-lang="python" data-text="output never reflected the same shell concatenation with the output sent to os.devnull. only the exit status is reported, which is a single bit. blind command injection python">
          <div class="card-top">
            <h3>Output never reflected</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">Python</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/python/blind.py">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same shell concatenation with the output sent to <code>os.devnull</code>. Only the exit status is reported, which is a single bit.</p>
            <p class="lesson">The command runs exactly as in the classic case; only the evidence is missing. This is the usual real-world situation and the reason blind techniques exist - an application that prints a tidy status message instead of raw output is not safer, it is just quieter. Proof has to come from something other than the page body: how long the reply takes, a file that appears, or a request arriving at a server you control.</p>
            <p class="reach">No flag needed beyond <code>-u</code>/<code>-p</code> - the blind techniques are tried by default.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="python" data-lang="python" data-text="eval() on the value the value is concatenated into a string that python evaluates as an expression, so the payload has to be valid python rather than a shell separator - and because it lands inside a quoted literal, the quote has to be closed and reopened around it. classic code injection python">
          <div class="card-top">
            <h3>eval() on the value</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span><span class="lang">Python</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/python/eval.py">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The value is concatenated into a string that Python evaluates as an expression, so the payload has to be valid Python rather than a shell separator - and because it lands inside a quoted literal, the quote has to be closed and reopened around it.</p>
            <p class="lesson">A different sink with the same consequence. Nothing here reaches a shell, so shell metacharacters are meaningless - a semicolon does not chain anything, it is just a semicolon in a PHP statement. The payload has to be valid code in the language doing the evaluating, which is why commix treats this as a separate kind of injection rather than a variant of the command case.</p>
            <p class="reach">Reach it with <code>--eval=python</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
      </div>
    </section>

    <section class="group" id="ruby">
      <h2><button class="fold" type="button" aria-expanded="true">Ruby scenarios <span class="count">1</span></button></h2>
      <div class="grid">
        <article class="card" data-kind="classic" data-sink="code" data-group="ruby" data-lang="ruby" data-text="eval on the value the value is concatenated into a string that ruby then evaluates as an expression, the same sink shape as the php and python pages, one language over. classic code injection ruby">
          <div class="card-top">
            <h3>eval on the value</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span><span class="lang">Ruby</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/ruby/eval.rb">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The value is concatenated into a string that Ruby then evaluates as an expression, so the payload has to be valid Ruby rather than a shell separator - and because it lands inside a quoted literal, the quote has to be closed and reopened around it.</p>
            <p class="lesson">The same sink shape as the PHP and Python pages, one language over: nothing here reaches a shell, so shell metacharacters are meaningless, and the payload has to be valid code in whichever language is doing the evaluating.</p>
            <p class="reach">Reach it with <code>--eval=ruby</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
      </div>
    </section>

    <section class="group" id="javascript">
      <h2><button class="fold" type="button" aria-expanded="true">JavaScript scenario <span class="count">1</span></button></h2>
      <div class="grid">
        <article class="card" data-kind="classic" data-sink="code" data-group="javascript" data-lang="javascript" data-text="eval on the value the value is concatenated into a string that node then evaluates as an expression, the same sink shape as the php python and ruby pages, one language over. classic code injection javascript node">
          <div class="card-top">
            <h3>eval on the value</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span><span class="lang">JavaScript</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="http://localhost:8087/" target="_blank" rel="noopener">POST (own port)</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The value is concatenated into a string that Node then evaluates as an expression, so the payload has to be valid JavaScript rather than a shell separator - and because it lands inside a quoted literal, the quote has to be closed and reopened around it.</p>
            <p class="lesson">The same sink shape as the PHP, Python and Ruby pages, one language over. This one is served by its own Node process on port 8087, not by this site's Apache: the base image's glibc predates any Node build that would still run there. See the README for the command that starts it.</p>
            <p class="reach">Reach it with <code>--eval=javascript</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
      </div>
    </section>

      <section class="group" id="user-agent">
        <h2><button class="fold" type="button" aria-expanded="true">User-Agent header <span class="count">3</span></button></h2>
        <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="headers" data-lang="php" data-text="echoed through a shell exec(&quot;echo &#x27;&quot;.$user_agent.&quot;&#x27;&quot;) - the header goes through a shell inside single quotes, and the output comes back on the page. classic command injection php">
          <div class="card-top">
            <h3>Echoed through a shell</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/user-agent/ua(classic).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>exec("echo '".$user_agent."'")</code> - the header goes through a shell inside single quotes, and the output comes back on the page.</p>
            <p class="reach">Reach it with <code>--level=3</code> - User-Agent is only tested from level 3 up, which also closes the quote.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="headers" data-lang="php" data-text="nothing returned the same injection with the output thrown away. unix-only. blind command injection php">
          <div class="card-top">
            <h3>Nothing returned</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/user-agent/ua(blind).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same injection with the output thrown away. Unix-only.</p>
            <p class="reach">Reach it with <code>--level=3</code> - User-Agent is only tested from level 3 up.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="headers" data-lang="php" data-text="evaluated as php the header reaches an eval() rather than a shell. classic code injection php">
          <div class="card-top">
            <h3>Evaluated as PHP</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/user-agent/ua(eval).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The header reaches an <code>eval()</code> rather than a shell.</p>
            <p class="reach">Reach it with <code>--level=3 --eval=php</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        </div>
      </section>
      <section class="group" id="cookie">
        <h2><button class="fold" type="button" aria-expanded="true">Cookie header <span class="count">5</span></button></h2>
        <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="headers" data-lang="php" data-text="echoed through a shell the addr cookie is concatenated into a ping and the output is returned. classic command injection php">
          <div class="card-top">
            <h3>Echoed through a shell</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/cookie/cookie(classic).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The <code>addr</code> cookie is concatenated into a ping and the output is returned.</p>
            <p class="reach">Reach it with <code>--cookie="addr=1" --level=2</code> - the cookie has to be supplied, and level 2 turns on cookie testing.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="headers" data-lang="php" data-text="cookie inside single quotes the user cookie lands inside single quotes in an echo, so the quote has to be closed first. classic command injection php">
          <div class="card-top">
            <h3>Cookie inside single quotes</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/cookie/cookie(classic_quote).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The <code>user</code> cookie lands inside single quotes in an <code>echo</code>, so the quote has to be closed first.</p>
            <p class="reach">Reach it with <code>--cookie="user=1" --level=3</code> - closing the quote needs level 3, not just the level 2 that cookie testing requires.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="headers" data-lang="php" data-text="base64 cookie value the cookie is base64 and is decoded before it reaches the command, so the payload has to be encoded to survive the round trip. classic command injection php">
          <div class="card-top">
            <h3>Base64 cookie value</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/cookie/cookie(b64).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The cookie is Base64 and is decoded before it reaches the command, so the payload has to be encoded to survive the round trip.</p>
            <p class="reach">Reach it with <code>--cookie="user=1" --level=3 --param-encoding=user=base64</code> - the value is also wrapped in single quotes, which needs level 3 to close.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="headers" data-lang="php" data-text="nothing returned the cookie-borne command runs with nothing reflected back. blind command injection php">
          <div class="card-top">
            <h3>Nothing returned</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/cookie/cookie(blind).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The cookie-borne command runs with nothing reflected back.</p>
            <p class="reach">Reach it with <code>--cookie="addr=1" --level=2</code>.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="headers" data-lang="php" data-text="evaluated as php the cookie reaches an eval() rather than a shell. classic code injection php">
          <div class="card-top">
            <h3>Evaluated as PHP</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/cookie/cookie(eval).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The cookie reaches an <code>eval()</code> rather than a shell.</p>
            <p class="reach">Reach it with <code>--cookie="user=1" --level=2 --eval=php</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        </div>
      </section>
      <section class="group" id="referer">
        <h2><button class="fold" type="button" aria-expanded="true">Referer header <span class="count">3</span></button></h2>
        <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="headers" data-lang="php" data-text="echoed through a shell the referer header is concatenated into an echo and its output comes back on the page. classic command injection php">
          <div class="card-top">
            <h3>Echoed through a shell</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/referer/referer(classic).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The <code>Referer</code> header is concatenated into an <code>echo</code> and its output comes back on the page.</p>
            <p class="reach">Reach it with <code>--level=3</code> - Referer is only tested from level 3 up.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="headers" data-lang="php" data-text="nothing returned the same header injection with no output reflected. unix-only. blind command injection php">
          <div class="card-top">
            <h3>Nothing returned</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/referer/referer(blind).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same header injection with no output reflected. Unix-only.</p>
            <p class="reach">Reach it with <code>--level=3</code> - Referer is only tested from level 3 up.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="headers" data-lang="php" data-text="evaluated as php the header reaches an eval(). classic code injection php">
          <div class="card-top">
            <h3>Evaluated as PHP</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/referer/referer(eval).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The header reaches an <code>eval()</code>.</p>
            <p class="reach">Reach it with <code>--level=3 --eval=php</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        </div>
      </section>
      <section class="group" id="host">
        <h2><button class="fold" type="button" aria-expanded="true">X-Forwarded-Host header <span class="count">2</span></button></h2>
        <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="headers" data-lang="php" data-text="echoed through a shell x-forwarded-host is read to build the canonical url and reaches a shell. a proxy is supposed to set it, but nothing stops a client sending it directly. classic command injection php">
          <div class="card-top">
            <h3>Echoed through a shell</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/host/host(classic).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>X-Forwarded-Host</code> is read to build the canonical URL and reaches a shell. A proxy is supposed to set it, but nothing stops a client sending it directly.</p>
            <p class="reach">Reach it with <code>-H "X-Forwarded-Host: 1" -p X-Forwarded-Host</code> - naming a custom header this way also raises the injection level it needs.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="headers" data-lang="php" data-text="nothing returned the same forwarded-host sink with nothing reflected. unix-only. blind command injection php">
          <div class="card-top">
            <h3>Nothing returned</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/host/host(blind).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same forwarded-host sink with nothing reflected. Unix-only.</p>
            <p class="reach">Reach it with <code>-H "X-Forwarded-Host: 1" -p X-Forwarded-Host</code>.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        </div>
      </section>
      <section class="group" id="custom-header">
        <h2><button class="fold" type="button" aria-expanded="true">Custom header <span class="count">2</span></button></h2>
        <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="headers" data-lang="php" data-text="x-forwarded-for echoed through a shell x-forwarded-for is read for the access log and concatenated into a command. it is an ordinary request header: anyone can send it and set it to anything. classic command injection php">
          <div class="card-top">
            <h3>X-Forwarded-For echoed through a shell</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/custom-header/xff(classic).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>X-Forwarded-For</code> is read for the access log and concatenated into a command. It is an ordinary request header: anyone can send it and set it to anything.</p>
            <p class="reach">Reach it with <code>-H "X-Forwarded-For: 1" -p X-Forwarded-For</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="headers" data-lang="php" data-text="x-forwarded-for, nothing returned the same custom-header sink with no output reflected. unix-only. blind command injection php">
          <div class="card-top">
            <h3>X-Forwarded-For, nothing returned</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/custom-header/xff(blind).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same custom-header sink with no output reflected. Unix-only.</p>
            <p class="reach">Reach it with <code>-H "X-Forwarded-For: 1" -p X-Forwarded-For</code>.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        </div>
      </section>

    <section class="group" id="stateful">
      <h2><button class="fold" type="button" aria-expanded="true">Stateful &amp; multi-step <span class="count">13</span></button></h2>
      <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="json web token kid key lookup the session cookie carries a jwt. the kid field of its header names the key to verify with, and the page locates that key by concatenating the value into a shell command - before the signature it would verify has been checked. jwt classic command injection php">
          <div class="card-top">
            <h3>JSON Web Token - 'kid' key lookup</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/stateful/jwt_kid.php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The <code>session</code> cookie carries a JSON Web Token. The <code>kid</code> field of its header names which key to verify with, and the page locates that key by concatenating the value into <code>ls -l /var/www/keys/&lt;kid&gt;</code>.</p>
            <p class="lesson">The ordering is the whole bug. <code>kid</code> has to be read before the signature can be checked against the key it names - so the lookup runs on whatever the client put there, every request, with nothing authenticated yet. A perfectly signed token changes nothing, because the sink is reached on the way to deciding whether the token was valid at all.</p>
            <p class="reach">Named as a candidate with <code>--jwt</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="anti-csrf token every render mints a fresh csrf_token into the session and writes it into the form as a hidden field. a post whose token does not match the one currently in the session is refused before $addr reaches the shell. classic command injection php">
          <div class="card-top">
            <h3>Anti-CSRF token</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/stateful/csrf.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Every render mints a fresh <code>csrf_token</code> into the session and writes it into the form as a hidden field. A POST whose token does not match the one currently in the session is refused before <code>$addr</code> reaches the shell.</p>
            <p class="lesson">The token is not a fix for the injection - the bug underneath is the same unquoted concatenation as everywhere else. What it changes is what a scan sees: replay a captured token and every payload comes back refused, which is indistinguishable from a parameter that simply is not injectable.</p>
            <p class="reach">Reach it with <code>--csrf-token="csrf_token"</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="second-order storage the submit page stores addr and answers stored. - it never runs the value. the command runs later, when the log viewer renders what was stored. classic command injection php">
          <div class="card-top">
            <h3>Second-order storage</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/stateful/second_order.php">POST</a><a class="m m-get" href="scenarios/stateful/second_order_log.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The submit page stores <code>addr</code> and answers <code>Stored.</code> - it never runs the value. The command runs later, when the log viewer renders what was stored.</p>
            <p class="lesson">Injection and execution do not have to happen in the same request. Everything a scanner normally reads - the response body, its length, how long it took - is identical whether the payload was dangerous or harmless, because this page genuinely does nothing with it.</p>
            <p class="reach">Reach it with <code>--second-url</code> pointing at <code>second_order_log.php</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="behind a session login the same unquoted exec() as the plainest scenario, but the page answers login required. to anyone without an authenticated session. classic command injection php">
          <div class="card-top">
            <h3>Behind a session login</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/stateful/session_classic.php">GET</a><a class="m m-post" href="scenarios/stateful/login.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same unquoted <code>exec()</code> as the plainest scenario, but the page answers <code>Login required.</code> to anyone without an authenticated session.</p>
            <p class="lesson">An unauthenticated scan reports this page as not injectable, which is true of what it can see and false of the application. Most of a real application's attack surface sits behind a login.</p>
            <p class="reach">Reach it with <code>--auth-url</code> and <code>--auth-data="username=admin&amp;password=testbed"</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="one-time request id every request carries a req_id. the session remembers the ones it has already seen, and a repeat is answered with duplicate request id. before $addr is used. classic command injection php">
          <div class="card-top">
            <h3>One-time request id</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/stateful/nonce.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Every request carries a <code>req_id</code>. The session remembers the ones it has already seen, and a repeat is answered with <code>Duplicate request id.</code> before <code>$addr</code> is used.</p>
            <p class="lesson">Replay protection, not input validation. It costs an attacker nothing - a fresh id is free - but it breaks any tool that sends the same request twice, which detection does constantly. The parameter that has to change is not the parameter being tested.</p>
            <p class="reach">Reach it with <code>--randomize=req_id</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="rate-limited endpoint the session counts requests. after five without a visit to the keepalive page, everything is answered with session locked. and $addr is never used. classic command injection php">
          <div class="card-top">
            <h3>Rate-limited endpoint</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/stateful/lockout.php">GET</a><a class="m m-get" href="scenarios/stateful/keepalive.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The session counts requests. After five without a visit to the keepalive page, everything is answered with <code>Session locked.</code> and <code>$addr</code> is never used.</p>
            <p class="lesson">Detection is not one request, it is dozens, so a limit an ordinary user would never reach is hit almost immediately. Everything after the fifth request is refused for a reason that has nothing to do with the payload.</p>
            <p class="reach">Reach it with <code>--safe-url</code> pointing at <code>keepalive.php</code> and <code>--safe-freq=3</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="intermittent failure roughly half of all requests are answered with service temporarily unavailable. instead of running the command. which ones is decided at random. classic command injection php">
          <div class="card-top">
            <h3>Intermittent failure</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/stateful/flaky.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Roughly half of all requests are answered with <code>Service temporarily unavailable.</code> instead of running the command. Which ones is decided at random.</p>
            <p class="lesson">A test is only meaningful if the response answers the question that was asked. Here half of them do not, so a payload that worked can look like it failed - the failure mode is not a missed finding but an unreliable one, different every run.</p>
            <p class="reach">Reach it with <code>--retry-on="&lt;b&gt;Service temporarily"</code>, anchored to the element the result is printed into.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="chunked request body the endpoint turns away any body that arrives with a content-length. only a chunked body is read, and it is parsed out of php://input rather than taken from $_post. classic command injection php">
          <div class="card-top">
            <h3>Chunked request body</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/stateful/chunked.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The endpoint turns away any body that arrives with a <code>Content-Length</code>. Only a chunked body is read, and it is parsed out of <code>php://input</code> rather than taken from <code>$_POST</code>.</p>
            <p class="lesson">Chunked encoding splits the body into pieces the server reassembles, so a filter reading the request as one buffer sees fragments instead of a payload. Plenty of middleboxes inspect only the first chunk, or skip chunked bodies altogether.</p>
            <p class="reach">Reach it with <code>--chunked</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="non-standard parameter delimiter the query string is not parsed by php. it is split on ; instead of &amp;amp;, so addr=127.0.0.1;count=1 is two parameters, not one value containing a semicolon. classic command injection php">
          <div class="card-top">
            <h3>Non-standard parameter delimiter</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/stateful/param_del.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The query string is not parsed by PHP. It is split on <code>;</code> instead of <code>&amp;</code>, so <code>addr=127.0.0.1;count=1</code> is two parameters, not one value containing a semicolon.</p>
            <p class="lesson">An application is free to invent its own parameter syntax. The risk is in the disagreement: a tool that splits on <code>&amp;</code> treats the whole string as a single value, while the application reads two fields - so the parameter that reaches the shell is never tested on its own.</p>
            <p class="reach">Reach it with <code>--param-del=";"</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="stateful" data-lang="php" data-text="out-of-band only the command is detached and its output thrown away. the page answers queued. immediately, whatever was sent. blind command injection php">
          <div class="card-top">
            <h3>Out-of-band only</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/stateful/oob.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The command is detached and its output thrown away. The page answers <code>Queued.</code> immediately, whatever was sent.</p>
            <p class="lesson">Every signal a scan normally reads is gone at once. The body is a constant, so there is nothing to compare; the process is backgrounded, so a <code>sleep</code> in the payload delays nothing the response waits for. The command still runs - it just cannot say so through this connection.</p>
            <p class="reach">Results-based and time-based both fail by construction. Reach it with <code>--oob</code>.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="arithmetic expansion blocked a denylist that rejects the value if it contains $(( or a backtick. command substitution with $(&amp;hellip;) is left alone. classic command injection php">
          <div class="card-top">
            <h3>Arithmetic expansion blocked</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/stateful/interpreter.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>A denylist that rejects the value if it contains <code>$((</code> or a backtick. Command substitution with <code>$(&hellip;)</code> is left alone.</p>
            <p class="lesson">The filter is aimed at a payload shape rather than at a capability. Shell arithmetic is one way to make a target prove it evaluated something - but anything the target can execute can do the same calculation. Blocking the notation blocks the probe, not the injection.</p>
            <p class="reach">Reach it with <code>--interpreter=python</code>, or <code>--skip-calc</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="token from another endpoint the same token check as the inline anti-csrf scenario, except this page never shows a token. it has to be minted by a post to the token endpoint first, and each one is good for a single request. classic command injection php">
          <div class="card-top">
            <h3>Token from another endpoint</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/stateful/csrf_remote.php">POST</a><a class="m m-post" href="scenarios/stateful/token.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same token check as the inline anti-CSRF scenario, except this page never shows a token. It has to be minted by a <code>POST</code> to the token endpoint first, and each one is good for a single request.</p>
            <p class="lesson">Where the token lives changes what an attacker has to automate, not whether the bug is reachable. Fetching one from a separate service, with its own method and its own body, is more than reading a hidden field - and a tool that only knows how to do the latter reports this page as clean.</p>
            <p class="reach">Reach it with <code>--csrf-token</code>, <code>--csrf-url</code>, <code>--csrf-method=POST</code> and <code>--csrf-data</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="output only on the next request the command runs when the request arrives, but what it printed is held and shown on the next page load. the response to the injected request carries nothing. classic command injection php">
          <div class="card-top">
            <h3>Output only on the next request</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/stateful/url_reload.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The command runs when the request arrives, but what it printed is held and shown on the <em>next</em> page load. The response to the injected request carries nothing.</p>
            <p class="lesson">An application that renders its result on the following view - a flash message, a job queue, a &quot;your report is ready&quot; page - hides the evidence from the request that caused it.</p>
            <p class="reach">Reach it with <code>--url-reload</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="runs the command, answers 500 the command runs and its output is on the page, but the response carries a 500 status. nothing is wrong - the application simply answers that way. classic command injection php">
          <div class="card-top">
            <h3>Runs the command, answers 500</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/stateful/error_codes.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The command runs and its output is on the page, but the response carries a <code>500</code> status. Nothing is wrong - the application simply answers that way.</p>
            <p class="lesson">A status code is not a verdict on whether the payload ran. A scanner that treats every 5xx as a dead end walks away from a working injection point.</p>
            <p class="reach">Reach it with <code>--ignore-code=500</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="percent-encoding is not decoded the value is taken from the raw query string and used as it arrived. it is never url-decoded, so %3b reaches the shell as those three characters rather than as a semicolon. classic command injection php">
          <div class="card-top">
            <h3>Percent-encoding is not decoded</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/stateful/unencoded.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The value is taken from the raw query string and used as it arrived. It is never URL-decoded, so <code>%3B</code> reaches the shell as those three characters rather than as a semicolon.</p>
            <p class="lesson">An application that reads <code>QUERY_STRING</code> by hand skips the decoding the language would have done for it, so a payload encoded on the way out arrives inert.</p>
            <p class="reach">Reach it with <code>--skip-urlencode</code>, together with a whitespace the URL carries literally (<code>--tamper=space2ifs</code>).</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="vulnerable only to a phone the desktop view and the mobile view are different code. only the mobile one passes the value to a shell. classic command injection php">
          <div class="card-top">
            <h3>Vulnerable only to a phone</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/stateful/mobile.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The desktop view and the mobile view are different code. Only the mobile one passes the value to a shell.</p>
            <p class="lesson">A vulnerability can live behind a branch most requests never take. Where the branch is chosen by the <code>User-Agent</code>, a default scan tests the safe half of the application.</p>
            <p class="reach">Reach it with <code>--mobile</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="decoy parameters around the real one four fields arrive together. one is always echoed back unchanged, one is always empty, and one reaches the shell. classic command injection php">
          <div class="card-top">
            <h3>Decoy parameters around the real one</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/stateful/decoys.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Four fields arrive together. One is always echoed back unchanged, one is always empty, and one reaches the shell.</p>
            <p class="lesson">Most of a real form is noise. Testing every field costs requests and, on a target that rate-limits or locks out, costs the run.</p>
            <p class="reach">Reach it with <code>--skip-static</code> and <code>--skip-empty</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="non-standard cookie delimiter the cookie header is not read by php. it is split on | instead of ;, so addr=127.0.0.1|lang=en is two cookies. classic command injection php">
          <div class="card-top">
            <h3>Non-standard cookie delimiter</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/stateful/cookie_del.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The <code>Cookie</code> header is not read by PHP. It is split on <code>|</code> instead of <code>;</code>, so <code>addr=127.0.0.1|lang=en</code> is two cookies.</p>
            <p class="lesson">The same disagreement as a hand-rolled query string, moved into a header - the cookie that reaches the shell is never tested by itself.</p>
            <p class="reach">Reach it with <code>--cookie-del="|" --level=2 --skip-urlencode</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="stateful" data-lang="php" data-text="a set-cookie that breaks the next request every response sets a fresh sid. send that cookie back and the page refuses you; keep sending the one you started with and it works. classic command injection php">
          <div class="card-top">
            <h3>A Set-Cookie that breaks the next request</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/stateful/drop_set_cookie.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Every response sets a fresh <code>sid</code>. Send that cookie back and the page refuses you; keep sending the one you started with and it works.</p>
            <p class="lesson">A client that stores every <code>Set-Cookie</code> can walk itself out of its own session partway through a scan, and everything after that looks like a target that stopped being vulnerable.</p>
            <p class="reach">Reach it with <code>--drop-set-cookie</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="stateful" data-lang="php" data-text="aliased document root a blind page served from /srv/hidden through an apache alias, which is nowhere under the document root the rest of the site uses. blind command injection php">
          <div class="card-top">
            <h3>Aliased document root</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="hidden/blind.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>A blind page served from <code>/srv/hidden</code> through an Apache <code>Alias</code>, which is nowhere under the document root the rest of the site uses.</p>
            <p class="lesson">The file-based technique writes its output into the web root and reads it back over HTTP, which requires knowing where on disk the URL space begins. Here the guess from the URL is wrong. Aliases, user directories and per-vhost roots are ordinary, so the mapping is worth being told rather than inferred.</p>
            <p class="reach">Reach it with <code>--technique=f --web-root=/srv/hidden</code>.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
      </div>
    </section>

    <section class="group" id="discovery">
      <h2><button class="fold" type="button" aria-expanded="true">Discovery <span class="count">3</span></button></h2>
      <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="discovery" data-lang="php" data-text="a parameter the page never names the page reads host and hands it to a shell. nothing on the page names it: no form, no link, no field. mine-params classic command injection php">
          <div class="card-top">
            <h3>A parameter the page never names</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/discovery/hidden_param.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The page reads <code>host</code> and hands it to a shell. There is no form, no link and no field carrying it, so reading the page tells you nothing about what it accepts.</p>
            <p class="lesson">A scanner can only test the parameters it is given or can see. A debug switch left in, a field kept for an older client, a name a framework accepts without advertising - none of them appear in the markup, and any of them can be the one that reaches a shell.</p>
            <p class="reach">Reach it with <code>--mine-params</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="discovery" data-lang="php" data-text="routes that live only in the script the page carries one empty element and loads app.js. every route it talks to is written inside that script and linked nowhere. mine-endpoints crawl classic command injection php">
          <div class="card-top">
            <h3>Routes that live only in the script</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/discovery/spa.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The page carries one empty element and loads <code>app.js</code>. Every route it talks to is written inside that script, and none of them is linked from the markup.</p>
            <p class="lesson">A crawler follows what a page links to, and skips <code>.js</code> on its way to the anchors - so against an application whose routes live in its bundle it comes back with nothing at all. The endpoints are not hidden; they are simply not written where a crawler looks.</p>
            <p class="reach">Reach it with <code>--crawl=1 --mine-endpoints</code>.</p>
            <p class="verdict">The routes it names lead to <b>Classic command injection</b> and <b>Classic code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="discovery" data-lang="php" data-text="an api that describes itself the service publishes an openapi swagger description naming two operations linked from nowhere. openapi classic command injection php">
          <div class="card-top">
            <h3>An API that describes itself</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span><span class="lang">PHP</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/discovery/openapi.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The service publishes an OpenAPI description of itself. It names a <code>GET</code> taking a query parameter and a <code>POST</code> taking a JSON body, and both hand what they are given to a shell. Neither endpoint is linked from any page, form or script.</p>
            <p class="lesson">Crawling finds what a site links to, and an API links to nothing. What used to be undiscoverable is now written down by the service itself - paths, methods, parameters and the shape of every body - so the whole testable surface is there for the asking, in a file no crawler would think to follow.</p>
            <p class="reach">Reach it with <code>--openapi</code>, and scope a large one with <code>--openapi-tags</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>, once per operation.</p>
          </div>
        </article>
      </div>
    </section>

    <p class="empty" id="empty">Nothing matches that.</p>
  </main>

  <footer>
    <p>Made in Greece with <span style="color:var(--red)">&hearts;</span> by
      <a href="https://github.com/stasinopoulos">Anastasios Stasinopoulos</a> &middot;
      part of the <a href="https://commixproject.com">Commix Project</a>.</p>
    <p class="copy">commix-testbed is GPLv3 licensed &copy; 2015-2026.</p>
  </footer>
</div>

<script>
// Hints.
document.querySelectorAll(".hint-toggle").forEach(function (btn) {
  btn.addEventListener("click", function () {
    var hint = btn.nextElementSibling, show = hint.hidden;
    hint.hidden = !show;
    btn.setAttribute("aria-expanded", String(show));
    btn.childNodes[0].nodeValue = show ? "Hide explanation" : "What is going on here";
  });
});

// Sections fold away, so a long page can be narrowed to the part being worked on.
document.querySelectorAll(".fold").forEach(function (btn) {
  btn.addEventListener("click", function () {
    var section = btn.closest(".group");
    var open = section.classList.toggle("folded");
    btn.setAttribute("aria-expanded", String(!open));
  });
});

// Search and the technique filter, applied together.
var q = document.getElementById("q"), chips = document.querySelectorAll(".chip");
var fKey = "all", fVal = "all";
function apply() {
  var term = q.value.trim().toLowerCase(), any = false;
  document.querySelectorAll(".card").forEach(function (card) {
    var okText = !term || card.dataset.text.indexOf(term) !== -1;
    var okType = fKey === "all" || card.dataset[fKey] === fVal;
    var show = okText && okType;
    card.style.display = show ? "" : "none";
    if (show) any = true;
  });
  document.querySelectorAll(".group").forEach(function (g) {
    var visible = g.querySelectorAll(".card:not([style*='display: none'])").length;
    g.style.display = visible ? "" : "none";
  });
  document.getElementById("empty").classList.toggle("show", !any);
}
q.addEventListener("input", apply);
chips.forEach(function (chip) {
  chip.addEventListener("click", function () {
    chips.forEach(function (c) { c.setAttribute("aria-pressed", String(c === chip)); });
    fKey = chip.dataset.k; fVal = chip.dataset.v;
    apply();
  });
});
</script>
</body>
</html>
<?php
// Counters below are derived from the markup above, not hand-maintained,
// so they can't drift from what the page actually lists.
$html = ob_get_clean();

function commix_testbed_section_count($html, $id) {
    if (!preg_match('/<section class="group" id="' . preg_quote($id, '/') . '">(.*?)<\/section>/s', $html, $m)) {
        return 0;
    }
    return substr_count($m[1], '<article class="card"');
}

$sectionIds = ['regular', 'filters', 'ctf', 'python', 'ruby', 'javascript',
    'user-agent', 'cookie', 'referer', 'host', 'custom-header', 'stateful', 'discovery'];
$counts = [];
foreach ($sectionIds as $id) {
    $counts[$id] = commix_testbed_section_count($html, $id);
}

preg_match_all('/<a class="m m-\w+"/', $html, $pages);
$stats = [
    'vulnerable pages'    => count($pages[0]),
    'parameter scenarios' => $counts['regular'],
    'filter bypasses'     => $counts['filters'],
    'injectable headers'  => $counts['user-agent'] + $counts['cookie'] + $counts['referer']
                              + $counts['host'] + $counts['custom-header'],
    'stateful flows'      => $counts['stateful'],
    'discovery pages'     => $counts['discovery'],
    'CTF challenges'      => $counts['ctf'],
];

foreach ($stats as $label => $value) {
    $html = preg_replace(
        '/(<div class="stat"><b>)\d+(<\/b><span>' . preg_quote($label, '/') . '<\/span><\/div>)/',
        '${1}' . $value . '${2}',
        $html,
        1
    );
}

foreach ($counts as $id => $value) {
    $html = preg_replace(
        '/(<section class="group" id="' . preg_quote($id, '/') . '">\s*<h2><button class="fold" type="button" aria-expanded="true">[^<]*<span class="count">)\d+(<\/span>)/s',
        '${1}' . $value . '${2}',
        $html,
        1
    );
}

echo $html;
