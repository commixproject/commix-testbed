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
      <div class="stat"><b>74</b><span>vulnerable pages</span></div>
      <div class="stat"><b>28</b><span>parameter scenarios</span></div>
      <div class="stat"><b>12</b><span>filter bypasses</span></div>
      <div class="stat"><b>15</b><span>injectable headers</span></div>
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
    </div>
  </div>

  <main>
    <section class="group" id="regular">
      <h2><button class="fold" type="button" aria-expanded="true">Regular parameters <span class="count">28</span></button></h2>
      <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-text="unfiltered parameter exec(&quot;/bin/ping -c 4 &quot;.$addr) with the output echoed straight back. nothing is filtered and nothing is quoted, so this is the plainest case there is. classic command injection">
          <div class="card-top">
            <h3>Unfiltered parameter</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>exec("/bin/ping -c 4 ".$addr)</code> with the output echoed straight back. Nothing is filtered and nothing is quoted, so this is the plainest case there is.</p>
            <p class="lesson">Concatenating a request value into a shell command is the whole bug - there is no clever trick to find here. Every other scenario on this site is this same mistake with something placed in the way, so it is worth seeing what the undefended case looks like first. The fix is not to escape the value but to stop invoking a shell: pass the address as an argument to the program, never as part of a string the shell will parse.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-text="base64-encoded parameter the value is only accepted when base64_encode(base64_decode($x)) === $x, so a raw payload is rejected before it ever reaches the shell. it has to arrive base64-encoded. classic command injection">
          <div class="card-top">
            <h3>Base64-encoded parameter</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_b64.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_b64.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The value is only accepted when <code>base64_encode(base64_decode($x)) === $x</code>, so a raw payload is rejected before it ever reaches the shell. It has to arrive Base64-encoded.</p>
            <p class="lesson">Encoding is not validation. The check only proves the value is well-formed Base64, and it is decoded straight into the command afterwards, so it filters nothing at all - it just changes the alphabet an attacker has to write in. Encodings that sit between the client and the sink hide a payload from anything inspecting the raw request, which is why a WAF in front of this page would see nothing worth blocking.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-text="hex-encoded parameter the same idea in hex: the value must satisfy bin2hex(pack(&#x27;h*&#x27;, $x)) === $x before it is unpacked into the command. classic command injection">
          <div class="card-top">
            <h3>Hex-encoded parameter</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_hex.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_hex.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same idea in hex: the value must satisfy <code>bin2hex(pack('H*', $x)) === $x</code> before it is unpacked into the command.</p>
            <p class="lesson">The same lesson as the Base64 case, and it is worth trying both: the only thing that changes is the transport encoding, and the vulnerability underneath is identical. If a scanner can solve one and not the other, the gap is in its encoding support, not in the application.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-text="value inside single quotes the value lands inside single quotes: exec(&quot;ping &#x27;&quot;.$addr.&quot;&#x27;&quot;). a single-quoted word expands nothing, so the quote has to be closed before the shell reads any of it. classic command injection">
          <div class="card-top">
            <h3>Value inside single quotes</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_quote.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_quote.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The value lands inside single quotes: <code>exec("ping '".$addr."'")</code>. A single-quoted word expands nothing, so the quote has to be closed before the shell reads any of it.</p>
            <p class="lesson">Quoting is often mistaken for escaping. Single quotes do stop the shell expanding anything inside them - but only until the value supplies a quote of its own and ends the quoted span early. Because the closing quote is still sitting in the template afterwards, the payload also has to leave the command syntactically valid, which is why a comment or a second quote usually shows up at the end of a working payload.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-text="value inside double quotes as above but with double quotes - and a double-quoted word still expands $(...), so command substitution works here without closing anything first. classic command injection">
          <div class="card-top">
            <h3>Value inside double quotes</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_double_quote.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_double_quote.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>As above but with double quotes - and a double-quoted word still expands <code>$(...)</code>, so command substitution works here without closing anything first.</p>
            <p class="lesson">This is the more dangerous of the two quoting mistakes, and the more common. A double-quoted word still performs command substitution and variable expansion, so the value does not even need to break out of the quotes - the shell will happily run what is inside them. Anyone reviewing this code and thinking the quotes make it safe has it backwards.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-text="whitespace rejected preg_match(&#x27;/\s/&#x27;) throws the value out if it contains any whitespace at all, so the payload has to separate its words some other way. classic command injection">
          <div class="card-top">
            <h3>Whitespace rejected</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_non_space.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_non_space.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>preg_match('/\s/')</code> throws the value out if it contains any whitespace at all, so the payload has to separate its words some other way.</p>
            <p class="lesson">Filters written against what a payload usually looks like rather than what the shell actually parses. The shell has several ways to separate words - a tab, a newline, brace expansion, or the value of $IFS - and this check knows about exactly one of them. It is a good illustration of why denylists lose: the author has to think of every alternative, the attacker only needs one.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-text="metacharacter blacklist a str_replace() strips ;, &amp;amp;&amp;amp;, | and backticks. it never looks at a single &amp;amp; or at a newline, and it only passes over the value once, so a stripped sequence can also be rebuilt around itself. classic command injection">
          <div class="card-top">
            <h3>Metacharacter blacklist</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_blacklisting.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_blacklisting.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>A <code>str_replace()</code> strips <code>;</code>, <code>&amp;&amp;</code>, <code>|</code> and backticks. It never looks at a single <code>&amp;</code> or at a newline, and it only passes over the value once, so a stripped sequence can also be rebuilt around itself.</p>
            <p class="lesson">Two separate mistakes, either of which is fatal. The list is incomplete - a single & and a newline both chain commands and neither is on it - and the replacement runs once over the value, so a sequence written around a stripped one survives the pass that was meant to remove it. Stripping characters from input is nearly always the wrong shape of defence.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-text="piped into md5sum the parameter is string rather than addr, and it is injected ahead of a pipe: exec(&#x27;echo &#x27;.$string.&#x27; | md5sum&#x27;). unix-only - the page refuses to run on windows. classic command injection">
          <div class="card-top">
            <h3>Piped into md5sum</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_hash.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_hash.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The parameter is <code>string</code> rather than <code>addr</code>, and it is injected ahead of a pipe: <code>exec('echo '.$string.' | md5sum')</code>. Unix-only - the page refuses to run on Windows.</p>
            <p class="lesson">Worth opening because the injection point is not where you would look for it. The parameter is named string rather than addr, and the value is spliced in ahead of a pipe, so the command line continues after it. A scan that only tries parameters called addr, or that assumes the value sits at the end of the command, misses this one entirely.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-text="behind basic authentication the injection point sits behind http basic authentication. the credentials are admin:admin; without them every request comes back 401 and nothing is tested. classic command injection">
          <div class="card-top">
            <h3>Behind Basic authentication</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_basic_auth.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_basic_auth.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The injection point sits behind HTTP Basic authentication. The credentials are <code>admin:admin</code>; without them every request comes back 401 and nothing is tested.</p>
            <p class="lesson">Authentication is not mitigation. The flaw is identical to the plain case; it just needs valid credentials to reach. This scenario is here because an unauthenticated scan reports the page as not injectable, which is the wrong conclusion - it never actually tested it. Anything behind a login needs credentials supplied before the result means anything.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-text="behind digest authentication the same injection point behind http digest authentication, again admin:admin. digest needs the challenge answered per request rather than one static header. classic command injection">
          <div class="card-top">
            <h3>Behind Digest authentication</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/classic_digest_auth.php">GET</a><a class="m m-post" href="scenarios/regular/POST/classic_digest_auth.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same injection point behind HTTP Digest authentication, again <code>admin:admin</code>. Digest needs the challenge answered per request rather than one static header.</p>
            <p class="lesson">The same point as the Basic case, with a handshake that cannot be replayed as one static header - Digest answers a per-request challenge. It is here to check that a tool follows the challenge rather than only knowing how to attach an Authorization header it prepared in advance.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="regular" data-text="output never reflected the command runs, but all the page says is whether the host seems to be up. with nothing to read, execution has to be proven some other way - a delay, a file, or a request to a server of your own. blind command injection">
          <div class="card-top">
            <h3>Output never reflected</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/blind.php">GET</a><a class="m m-post" href="scenarios/regular/POST/blind.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The command runs, but all the page says is whether the host <em>seems to be up</em>. With nothing to read, execution has to be proven some other way - a delay, a file, or a request to a server of your own.</p>
            <p class="lesson">The command runs exactly as in the classic case; only the evidence is missing. This is the usual real-world situation and the reason blind techniques exist - an application that prints a tidy status message instead of raw output is not safer, it is just quieter. Proof has to come from something other than the page body: how long the reply takes, a file that appears, or a request arriving at a server you control.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="regular" data-text="output and exit status discarded output goes to /dev/null and the command is backgrounded with &amp;amp;, so not even the exit status reaches the page. it looks identical whatever you send it. blind command injection">
          <div class="card-top">
            <h3>Output and exit status discarded</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/double_blind.php">GET</a><a class="m m-post" href="scenarios/regular/POST/double_blind.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Output goes to <code>/dev/null</code> and the command is backgrounded with <code>&amp;</code>, so not even the exit status reaches the page. It looks identical whatever you send it.</p>
            <p class="lesson">One step past the previous page: with output redirected and the command backgrounded, even the success or failure of the command is invisible, so the response is byte-for-byte identical whatever you send. Timing is close to the only channel left, and the backgrounding means a delay has to be arranged carefully to still be observable.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="regular" data-text="eval() on the value eval(&quot;echo \&quot;hello, &quot;.$user.&quot;!\&quot;;&quot;) - the value is evaluated as php, never handed to a shell. a shell separator means nothing here; the payload has to be valid php. classic code injection">
          <div class="card-top">
            <h3>eval() on the value</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/eval.php">GET</a><a class="m m-post" href="scenarios/regular/POST/eval.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>eval("echo \"Hello, ".$user."!\";")</code> - the value is evaluated as PHP, never handed to a shell. A shell separator means nothing here; the payload has to be valid PHP.</p>
            <p class="lesson">A different sink with the same consequence. Nothing here reaches a shell, so shell metacharacters are meaningless - a semicolon does not chain anything, it is just a semicolon in a PHP statement. The payload has to be valid code in the language doing the evaluating, which is why commix treats this as a separate kind of injection rather than a variant of the command case.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="regular" data-text="eval() on a base64 value the same evaluated sink, except the value must be valid base64 before it is decoded into the eval(). classic code injection">
          <div class="card-top">
            <h3>eval() on a Base64 value</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/eval_b64.php">GET</a><a class="m m-post" href="scenarios/regular/POST/eval_b64.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same evaluated sink, except the value must be valid Base64 before it is decoded into the <code>eval()</code>.</p>
            <p class="lesson">Combines the two ideas already met separately: an evaluated sink reached through an encoding. Useful for checking that encoding and sink are handled independently rather than as one hard-coded combination.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-text="json request body the body is json read from php://input and its addr member goes into exec(). the payload has to sit inside the json rather than be appended to it. classic command injection">
          <div class="card-top">
            <h3>JSON request body</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/regular/POST/classic_json.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The body is JSON read from <code>php://input</code> and its <code>addr</code> member goes into <code>exec()</code>. The payload has to sit inside the JSON rather than be appended to it.</p>
            <p class="lesson">The vulnerability is ordinary; what is different is where the value lives. The body is parsed as JSON, so a payload appended to the request is not a parameter at all - it has to be placed inside a member and stay valid JSON, with whatever quoting and escaping that implies. Plenty of tooling that handles form bodies fails here.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="regular" data-text="json request body, nothing returned the json case with no output in the response. blind command injection">
          <div class="card-top">
            <h3>JSON request body, nothing returned</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/regular/POST/blind_json.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The JSON case with no output in the response.</p>
            <p class="lesson">The structured-body case with the response closed off, so it needs the body handling and a blind technique at the same time.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="regular" data-text="json request body, evaluated an evaluated sink reached through a json member rather than a form field. classic code injection">
          <div class="card-top">
            <h3>JSON request body, evaluated</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/regular/POST/eval_json.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>An evaluated sink reached through a JSON member rather than a form field.</p>
            <p class="lesson">A structured body feeding an evaluated sink - the two complications that are usually met separately, on one page.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-text="soap/xml request body the body is xml parsed with libxml_noent | libxml_dtdload, and both &amp;lt;addr&amp;gt; and &amp;lt;count&amp;gt; are concatenated into the command - two injection points on one page, not one. classic command injection">
          <div class="card-top">
            <h3>SOAP/XML request body</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/regular/POST/classic_xml.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The body is XML parsed with <code>LIBXML_NOENT | LIBXML_DTDLOAD</code>, and both <code>&lt;addr&gt;</code> and <code>&lt;count&gt;</code> are concatenated into the command - two injection points on one page, not one.</p>
            <p class="lesson">Two things to notice. Both addr and count reach the command, so the page has more than one injection point and finding only the first one is an incomplete result. The parser is also built with LIBXML_NOENT and LIBXML_DTDLOAD, which resolves external entities - so on top of the command injection this document is an XXE waiting to happen.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="regular" data-text="soap/xml request body, nothing returned the xml case with no output reflected back. blind command injection">
          <div class="card-top">
            <h3>SOAP/XML request body, nothing returned</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/regular/POST/blind_xml.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The XML case with no output reflected back.</p>
            <p class="lesson">The XML case with nothing reflected, so the injection point has to be found and confirmed without ever seeing output.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-text="ip-format validation the value has to look like an ip address - but the pattern is /^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$/m, and that m flag makes ^ and $ match at every line rather than once. a second line walks straight past it. classic command injection">
          <div class="card-top">
            <h3>IP-format validation</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/preg_match.php">GET</a><a class="m m-post" href="scenarios/regular/POST/preg_match.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The value has to look like an IP address - but the pattern is <code>/^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$/m</code>, and that <code>m</code> flag makes <code>^</code> and <code>$</code> match at every line rather than once. A second line walks straight past it.</p>
            <p class="lesson">The most instructive filter on the site. The author did the right thing - validate the shape of the value - and still lost, to one letter. The m flag makes ^ and $ match at every line rather than at the start and end of the whole value, so a string whose first line is a valid IP passes no matter what follows on the next line. Anchors without that flag, or a check that the value contains no newline, would have held.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="regular" data-text="ip-format validation, nothing returned the same multiline-anchor mistake, this time with nothing readable in the response. blind command injection">
          <div class="card-top">
            <h3>IP-format validation, nothing returned</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/preg_match_blind.php">GET</a><a class="m m-post" href="scenarios/regular/POST/preg_match_blind.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same multiline-anchor mistake, this time with nothing readable in the response.</p>
            <p class="lesson">The same anchor mistake with the response closed off, so the bypass has to be found blind.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="regular" data-text="attacker-controlled regex both the pattern and the replacement come from the query string, so the pattern&#x27;s own modifiers belong to the attacker - and the e modifier evaluates the replacement as php. needs a php older than 7, where that modifier still exists. classic code injection">
          <div class="card-top">
            <h3>Attacker-controlled regex</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/preg_replace.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Both the pattern and the replacement come from the query string, so the pattern's own modifiers belong to the attacker - and the <code>e</code> modifier evaluates the replacement as PHP. Needs a PHP older than 7, where that modifier still exists.</p>
            <p class="lesson">The pattern itself is user input, which hands over far more than the text being matched: a regex carries modifiers, and PHP's e modifier evaluated the replacement as code. It was removed in PHP 7, so this page needs an older interpreter - but the shape of the mistake outlives the feature. Never let a request supply the pattern to a matching function.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="regular" data-text="assert() on the value assert(trim(&quot;&#x27;&quot;.$_get[&#x27;user&#x27;].&quot;&#x27;&quot;)) evaluates its argument as php. the value is wrapped in single quotes, so those have to be closed first. classic code injection">
          <div class="card-top">
            <h3>assert() on the value</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/assert.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>assert(trim("'".$_GET['user']."'"))</code> evaluates its argument as PHP. The value is wrapped in single quotes, so those have to be closed first.</p>
            <p class="lesson">assert() took a string and evaluated it as PHP, which makes it a code-execution sink that reads like a sanity check. The value is wrapped in quotes, so those have to be closed first. PHP 7 deprecated the string form and PHP 8 removed it; the lesson is that any function that takes code as a string is a sink, however harmless its name sounds.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="regular" data-text="quotes stripped, then evaluated backslashes and both kinds of quote are removed, then the value is dropped into a double-quoted eval(&quot;echo(\&quot;$user\&quot;);&quot;). quotes are not needed: php expands ${...} inside a double-quoted string. classic code injection">
          <div class="card-top">
            <h3>Quotes stripped, then evaluated</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/str_replace.php">GET</a><a class="m m-post" href="scenarios/regular/POST/str_replace.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Backslashes and both kinds of quote are removed, then the value is dropped into a double-quoted <code>eval("echo(\"$user\");")</code>. Quotes are not needed: PHP expands <code>${...}</code> inside a double-quoted string.</p>
            <p class="lesson">A sanitiser that removes exactly the characters the author imagined an attacker needs. Backslashes and both quotes are stripped - but the value lands in a double-quoted PHP string, where ${...} performs an expansion without a single quote being involved. The defence and the attack are aimed at different things.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="regular" data-text="create_function() on the value the value is concatenated into the body of a create_function(), which compiles it as php. removed in php 8, so this one needs an older interpreter. classic code injection">
          <div class="card-top">
            <h3>create_function() on the value</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/create_function.php">GET</a><a class="m m-post" href="scenarios/regular/POST/create_function.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The value is concatenated into the body of a <code>create_function()</code>, which compiles it as PHP. Removed in PHP 8, so this one needs an older interpreter.</p>
            <p class="lesson">create_function() built a function by compiling a string, so anything concatenated into its body became code. It was deprecated in PHP 7.2 and removed in 8. Like assert(), it is worth seeing because the dangerous part is not the syntax but the idea: text that becomes code.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="code" data-group="regular" data-text="eval() with nothing printed back the value is evaluated as php - eval(&#x27;$greeting = &quot;hello, &#x27; . $user . &#x27;!&quot;;&#x27;) - but the greeting it builds is never printed. the page answers with the same fixed sentence whatever you send it. blind code injection">
          <div class="card-top">
            <h3>eval() with nothing printed back</h3>
            <span class="badges"><span class="type t-blind" title="Blind code injection">Blind</span><span class="tag">code</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/eval_blind.php">GET</a><a class="m m-post" href="scenarios/regular/POST/eval_blind.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The value is evaluated as PHP - <code>eval('$greeting = "Hello, ' . $user . '!";')</code> - but the greeting it builds is never printed. The page answers with the same fixed sentence whatever you send it.</p>
            <p class="lesson">The fourth corner of the matrix, and the hardest of the four to find. The sink is an evaluated string rather than a shell, so shell metacharacters do nothing - and the result is never printed, so there is nothing to read either. Both of the usual footholds are gone at once: the payload has to be valid PHP, and the proof has to come from timing or a side effect. A page that quietly evaluates input and says only 'saved' is the realistic shape of this bug.</p>
            <p class="verdict">Reported by commix as <b>Blind code injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-text="only one of three parameters is validated three values are submitted and two of them are checked - addr against a host pattern and label against word characters. count is concatenated into the command with no check at all. classic command injection">
          <div class="card-top">
            <h3>Only one of three parameters is validated</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-get" href="scenarios/regular/GET/multiple_params.php">GET</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Three values are submitted and two of them are checked - <code>addr</code> against a host pattern and <code>label</code> against word characters. <code>count</code> is concatenated into the command with no check at all.</p>
            <p class="lesson">The most realistic scenario here. Someone did think about input validation - they just did not finish. A count, an id, a page number or a sort order rarely looks dangerous enough to check, and it reaches the same shell as the field that did get a regex. Testing one parameter and declaring the page safe is how this survives a review.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="regular" data-text="put requests only the endpoint turns away anything that is not a put, and reads its body with parse_str() rather than from $_post. classic command injection">
          <div class="card-top">
            <h3>PUT requests only</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-put" href="scenarios/regular/PUT/classic.php">PUT</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The endpoint turns away anything that is not a <code>PUT</code>, and reads its body with <code>parse_str()</code> rather than from <code>$_POST</code>.</p>
            <p class="lesson">The vulnerability is ordinary; reaching it is not. A scan that only sends GET and POST never gets a useful response from this endpoint, and the body has to be parsed out of the raw request rather than read from $_POST. Plenty of an application's surface sits behind methods nobody thinks to try.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
      </div>
    </section>

    <section class="group" id="filters">
      <h2><button class="fold" type="button" aria-expanded="true">Weak filters <span class="count">12</span></button></h2>
      <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-text="lax domain validation /^\w+\..*\w+\.\w+$/ looks like it demands a domain name, but the .* in the middle accepts anything at all. keep a domain-shaped head and tail and the middle is yours. classic command injection">
          <div class="card-top">
            <h3>Lax domain validation</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/lax_domain_name.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>/^\w+\..*\w+\.\w+$/</code> looks like it demands a domain name, but the <code>.*</code> in the middle accepts anything at all. Keep a domain-shaped head and tail and the middle is yours.</p>
            <p class="lesson">A pattern that looks strict read quickly and is not. \w+\. at the front and \w+\.\w+ at the end are real constraints, but .* between them accepts anything at all, including separators and a whole command. A regex has to be read for what it permits, not for what it appears to describe.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-text="value inside nested quotes exec(&quot;/bin/ping -c 4 \&quot;{$addr}\&quot;&quot;) - the value sits inside double quotes that are themselves inside a php double-quoted string. classic command injection">
          <div class="card-top">
            <h3>Value inside nested quotes</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/nested_quotes.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>exec("/bin/ping -c 4 \"{$addr}\"")</code> - the value sits inside double quotes that are themselves inside a PHP double-quoted string.</p>
            <p class="lesson">Two layers of quoting - PHP's around the string, the shell's inside it - which mostly serves to make the code hard to reason about. Working out which layer consumes which character is the exercise here; the shell only ever sees the result of the PHP layer.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-text="separators blocked: ; | &amp;amp; $ four metacharacters are blacklisted. a newline is not one of them, and it ends a command just as well as a semicolon does. classic command injection">
          <div class="card-top">
            <h3>Separators blocked: ; | &amp; $</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/no_colon_no_pipe_no_ampersand_no_dollar.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Four metacharacters are blacklisted. A newline is not one of them, and it ends a command just as well as a semicolon does.</p>
            <p class="lesson">A denylist of the separators people write down from memory. A newline is not on it, and a newline ends a command as surely as a semicolon does - it is simply harder to see in a request. Any filter that enumerates metacharacters should be assumed incomplete.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-text="spaces blocked only the literal space is rejected. a tab, a newline or ${ifs} all still separate one word from the next. classic command injection">
          <div class="card-top">
            <h3>Spaces blocked</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/no_space.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Only the literal space is rejected. A tab, a newline or <code>${IFS}</code> all still separate one word from the next.</p>
            <p class="lesson">Only the literal space character is rejected, which leaves every other way the shell has of separating words: a tab, a newline, ${IFS}, or brace expansion. The check reflects what payloads look like rather than how the shell parses them.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-text="spaces and three separators blocked whitespace and ; | &amp;amp; are gone, which leaves the newline to chain with and ${ifs} to separate words. classic command injection">
          <div class="card-top">
            <h3>Spaces and three separators blocked</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/no_space_no_colon_no_pipe_no_ampersand.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Whitespace and <code>;</code> <code>|</code> <code>&amp;</code> are gone, which leaves the newline to chain with and <code>${IFS}</code> to separate words.</p>
            <p class="lesson">More characters on the list, and the same weakness - what is left is still enough. It is worth working out which combination survives here rather than reaching for a tool, because the answer says exactly what the filter forgot.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-text="spaces, separators and $ blocked with $ gone too, ${ifs} is out - but a tab is still whitespace to the shell and it is not a space. classic command injection">
          <div class="card-top">
            <h3>Spaces, separators and $ blocked</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/no_space_no_colon_no_pipe_no_ampersand_no_dollar.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>With <code>$</code> gone too, <code>${IFS}</code> is out - but a tab is still whitespace to the shell and it is not a space.</p>
            <p class="lesson">With $ gone, the ${IFS} trick that solved the previous pages is unavailable, so this one forces a different answer for the same problem. A good demonstration that a bypass is not a universal key - it is specific to what a particular filter neglected.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-text="all whitespace blocked /\s+/ covers the tab and the newline as well as the space, so the word separator has to be something that expands into whitespace rather than something that is it. classic command injection">
          <div class="card-top">
            <h3>All whitespace blocked</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/no_white_chars.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>/\s+/</code> covers the tab and the newline as well as the space, so the word separator has to be something that <em>expands</em> into whitespace rather than something that is it.</p>
            <p class="lesson">\s covers space, tab and newline together, which is a genuinely tighter check than the pages before it. It still does not help: the word separator does not have to be whitespace in the request, only to become whitespace by the time the shell splits the line.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-text="must start with alphanumerics /^\w+/ - the value only has to begin with word characters. everything after that first run goes unchecked. classic command injection">
          <div class="card-top">
            <h3>Must start with alphanumerics</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/simple_start_alphanum.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>/^\w+/</code> - the value only has to <em>begin</em> with word characters. Everything after that first run goes unchecked.</p>
            <p class="lesson">A check that constrains only the beginning of the value and says nothing about the rest, so anything appended after that first run of word characters goes straight through. Partial validation is not validation.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-text="must end with alphanumerics /\w+$/ - the value only has to end with word characters, so the payload goes in front and a harmless word closes it. classic command injection">
          <div class="card-top">
            <h3>Must end with alphanumerics</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/simple_stop_alphanum.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>/\w+$/</code> - the value only has to <em>end</em> with word characters, so the payload goes in front and a harmless word closes it.</p>
            <p class="lesson">The mirror image: only the end is constrained, so the payload goes in front and a harmless word satisfies the anchor. Seeing both pages together makes the point that an anchor pins one end of a string and leaves the other free.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-text="must start with alphanumerics, no whitespace both conditions at once: begin with word characters, and carry no whitespace anywhere. classic command injection">
          <div class="card-top">
            <h3>Must start with alphanumerics, no whitespace</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/no_white_chars_start_alphanum.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>Both conditions at once: begin with word characters, and carry no whitespace anywhere.</p>
            <p class="lesson">Two partial checks stacked. Neither is sufficient alone and together they only narrow the shape of a working payload rather than preventing one - the requirements can be met at the same time.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-text="must end with alphanumerics, no whitespace end with word characters and carry no whitespace - so the suffix has to satisfy the anchor without putting a space back. classic command injection">
          <div class="card-top">
            <h3>Must end with alphanumerics, no whitespace</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/no_white_chars_stop_alnum.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>End with word characters and carry no whitespace - so the suffix has to satisfy the anchor without putting a space back.</p>
            <p class="lesson">The same pair of constraints anchored at the other end. The interesting part is that the suffix satisfying the anchor must not reintroduce the whitespace the other check forbids.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="filters" data-text="command names blacklisted the command names themselves are matched: echo|wget|nc|whoami|cat|ncat on unix and powershell|cmd on windows. the match is on literal text, so anything that hides the letters from the regex without hiding them from the shell gets through. classic command injection">
          <div class="card-top">
            <h3>Command names blacklisted</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-post" href="scenarios/filters/multiple_os_commands_blacklisting.php">POST</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The command names themselves are matched: <code>echo|wget|nc|whoami|cat|ncat</code> on Unix and <code>powershell|cmd</code> on Windows. The match is on literal text, so anything that hides the letters from the regex without hiding them from the shell gets through.</p>
            <p class="lesson">Blocking command names rather than metacharacters, which fails for a different reason: the regex matches literal text, while the shell sees the string only after its own expansions have run. Anything that breaks up the letters on the way to the shell defeats the match without changing what executes. There are also far more ways to read a file than the six names on this list.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
      </div>
    </section>

      <section class="group" id="user-agent">
        <h2><button class="fold" type="button" aria-expanded="true">User-Agent header <span class="count">3</span></button></h2>
        <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="headers" data-text="echoed through a shell exec(&quot;echo &#x27;&quot;.$user_agent.&quot;&#x27;&quot;) - the header goes through a shell inside single quotes, and the output comes back on the page. classic command injection">
          <div class="card-top">
            <h3>Echoed through a shell</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/user-agent/ua(classic).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>exec("echo '".$user_agent."'")</code> - the header goes through a shell inside single quotes, and the output comes back on the page.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="headers" data-text="nothing returned the same injection with the output thrown away. unix-only. blind command injection">
          <div class="card-top">
            <h3>Nothing returned</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/user-agent/ua(blind).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same injection with the output thrown away. Unix-only.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="headers" data-text="evaluated as php the header reaches an eval() rather than a shell. classic code injection">
          <div class="card-top">
            <h3>Evaluated as PHP</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/user-agent/ua(eval).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The header reaches an <code>eval()</code> rather than a shell.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        </div>
      </section>
      <section class="group" id="cookie">
        <h2><button class="fold" type="button" aria-expanded="true">Cookie header <span class="count">5</span></button></h2>
        <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="headers" data-text="echoed through a shell the addr cookie is concatenated into a ping and the output is returned. classic command injection">
          <div class="card-top">
            <h3>Echoed through a shell</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/cookie/cookie(classic).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The <code>addr</code> cookie is concatenated into a ping and the output is returned.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="headers" data-text="cookie inside single quotes the user cookie lands inside single quotes in an echo, so the quote has to be closed first. classic command injection">
          <div class="card-top">
            <h3>Cookie inside single quotes</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/cookie/cookie(classic_quote).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The <code>user</code> cookie lands inside single quotes in an <code>echo</code>, so the quote has to be closed first.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="command" data-group="headers" data-text="base64 cookie value the cookie is base64 and is decoded before it reaches the command, so the payload has to be encoded to survive the round trip. classic command injection">
          <div class="card-top">
            <h3>Base64 cookie value</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/cookie/cookie(b64).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The cookie is Base64 and is decoded before it reaches the command, so the payload has to be encoded to survive the round trip.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="headers" data-text="nothing returned the cookie-borne command runs with nothing reflected back. blind command injection">
          <div class="card-top">
            <h3>Nothing returned</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/cookie/cookie(blind).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The cookie-borne command runs with nothing reflected back.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="headers" data-text="evaluated as php the cookie reaches an eval() rather than a shell. classic code injection">
          <div class="card-top">
            <h3>Evaluated as PHP</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/cookie/cookie(eval).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The cookie reaches an <code>eval()</code> rather than a shell.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        </div>
      </section>
      <section class="group" id="referer">
        <h2><button class="fold" type="button" aria-expanded="true">Referer header <span class="count">3</span></button></h2>
        <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="headers" data-text="echoed through a shell the referer header is concatenated into an echo and its output comes back on the page. classic command injection">
          <div class="card-top">
            <h3>Echoed through a shell</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/referer/referer(classic).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The <code>Referer</code> header is concatenated into an <code>echo</code> and its output comes back on the page.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="headers" data-text="nothing returned the same header injection with no output reflected. unix-only. blind command injection">
          <div class="card-top">
            <h3>Nothing returned</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/referer/referer(blind).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same header injection with no output reflected. Unix-only.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="classic" data-sink="code" data-group="headers" data-text="evaluated as php the header reaches an eval(). classic code injection">
          <div class="card-top">
            <h3>Evaluated as PHP</h3>
            <span class="badges"><span class="type t-classic" title="Classic code injection">Classic</span><span class="tag">code</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/referer/referer(eval).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The header reaches an <code>eval()</code>.</p>
            <p class="verdict">Reported by commix as <b>Classic code injection</b>.</p>
          </div>
        </article>
        </div>
      </section>
      <section class="group" id="host">
        <h2><button class="fold" type="button" aria-expanded="true">X-Forwarded-Host header <span class="count">2</span></button></h2>
        <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="headers" data-text="echoed through a shell x-forwarded-host is read to build the canonical url and reaches a shell. a proxy is supposed to set it, but nothing stops a client sending it directly. classic command injection">
          <div class="card-top">
            <h3>Echoed through a shell</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/host/host(classic).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>X-Forwarded-Host</code> is read to build the canonical URL and reaches a shell. A proxy is supposed to set it, but nothing stops a client sending it directly.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="headers" data-text="nothing returned the same forwarded-host sink with nothing reflected. unix-only. blind command injection">
          <div class="card-top">
            <h3>Nothing returned</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/host/host(blind).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same forwarded-host sink with nothing reflected. Unix-only.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
          </div>
        </article>
        </div>
      </section>
      <section class="group" id="custom-header">
        <h2><button class="fold" type="button" aria-expanded="true">Custom header <span class="count">2</span></button></h2>
        <div class="grid">
        <article class="card" data-kind="classic" data-sink="command" data-group="headers" data-text="x-forwarded-for echoed through a shell x-forwarded-for is read for the access log and concatenated into a command. it is an ordinary request header: anyone can send it and set it to anything. classic command injection">
          <div class="card-top">
            <h3>X-Forwarded-For echoed through a shell</h3>
            <span class="badges"><span class="type t-classic" title="Classic command injection">Classic</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/custom-header/xff(classic).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p><code>X-Forwarded-For</code> is read for the access log and concatenated into a command. It is an ordinary request header: anyone can send it and set it to anything.</p>
            <p class="verdict">Reported by commix as <b>Classic command injection</b>.</p>
          </div>
        </article>
        <article class="card" data-kind="blind" data-sink="command" data-group="headers" data-text="x-forwarded-for, nothing returned the same custom-header sink with no output reflected. unix-only. blind command injection">
          <div class="card-top">
            <h3>X-Forwarded-For, nothing returned</h3>
            <span class="badges"><span class="type t-blind" title="Blind command injection">Blind</span></span>
          </div>
          <div class="methods"><a class="m m-header" href="scenarios/custom-header/xff(blind).php">HEADER</a></div>
          <button class="hint-toggle" type="button" aria-expanded="false">What is going on here</button>
          <div class="hint" hidden>
            <p>The same custom-header sink with no output reflected. Unix-only.</p>
            <p class="verdict">Reported by commix as <b>Blind command injection</b>.</p>
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
