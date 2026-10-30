// The page chrome the JavaScript scenario uses.
//
// Standalone rather than sharing the PHP/Python/Ruby pages' own stylesheet: this page is served by
// its own Node process rather than the same Apache document root, so a relative "../../css/..."
// would resolve against the wrong origin. TESTBED_URL names where the main site actually is, so
// the shared stylesheet, logo and "back" link can still point at it directly - a stylesheet link
// works cross-origin with no CORS of its own to arrange.

const TESTBED_URL = process.env.TESTBED_URL || 'http://localhost:8081';

// Where each verdict's own technique is explained, for the one link the page makes to the wiki.
const TECHNIQUES = 'https://github.com/commixproject/commix/wiki/Techniques';
const VERDICT_LINKS = {
  'Classic command injection': TECHNIQUES + '#1-results-based-classic-command-injection',
  'Classic code injection': TECHNIQUES + '#2-dynamic-code-evaluation',
  'Results-based code injection': TECHNIQUES + '#2-dynamic-code-evaluation',
  'Blind command injection': TECHNIQUES + '#blind-injections',
  'Blind code injection': TECHNIQUES + '#blind-injections',
};

function top(values) {
  return `<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>${values.title} &middot; commix-testbed</title>
<meta name="robots" content="noindex">
<link rel="shortcut icon" href="${TESTBED_URL}/favicon.ico">
<link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
<link rel="stylesheet" href="${TESTBED_URL}/css/testbed.css?v=3">
</head>
<body>

<nav class="nav">
  <div class="wrap">
    <a class="brand" href="${TESTBED_URL}/index.php">
      <img src="${TESTBED_URL}/img/testbed_header_logo.png" alt="commix-testbed"></a>
    <a class="back" href="${TESTBED_URL}/index.php">&larr; All scenarios</a>
  </div>
</nav>

<div class="wrap page">

  <header class="sc-head">
    <p class="crumb">${values.crumb || 'JavaScript scenarios'}</p>
    <h1>${values.title}</h1>
    <span class="badges"><span class="type t-${values.kind}">${values.kind_label}</span>${values.code || ''}<span class="lang">JavaScript</span></span>
  </header>

  <div class="cols">
    <section class="panel live">
      <h2>The page</h2>
      <form action="${values.script}" method="POST">${values.label} <input type="text" name="${values.field}">
      <input value="Submit!" type="submit"></form>
`;
}

function bottom(values) {
  const verdictUrl = VERDICT_LINKS[values.verdict];
  const verdictHtml = verdictUrl
    ? `<a href="${verdictUrl}">${values.verdict}</a>`
    : values.verdict;
  return `    </section>

    <aside class="panel">
      <h2>What this page does</h2>
      <p class="explain">${values.explain}</p>
      <p class="lesson">${values.lesson}</p>
      ${values.solve || ''}
      <p class="verdict">Identified by commix as <b>${verdictHtml}</b>.${values.origin || ''}</p>
      <details class="src">
        <summary>The vulnerable code</summary>
        <pre><code>${values.source}</code></pre>
      </details>
      <div class="note"><p>This one scenario runs as a plain Node HTTP server on its own port, not
      as CGI under the main site's Apache - the base image's glibc predates any Node build that
      would run there.</p></div>
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
`;
}

module.exports = {top, bottom};
