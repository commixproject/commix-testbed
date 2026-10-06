// The page chrome the JavaScript scenario uses.
//
// Standalone rather than sharing the PHP/Python/Ruby pages' own stylesheet: this page is served by
// its own Node process rather than the same Apache document root, so a relative "../../css/..."
// would resolve against the wrong origin. TESTBED_URL names where the main site actually is, for
// the one link back to it.

const TESTBED_URL = process.env.TESTBED_URL || 'http://localhost:8081';

const STYLE = `
body{font-family:-apple-system,Segoe UI,Helvetica,Arial,sans-serif;max-width:900px;margin:2rem auto;padding:0 1rem;line-height:1.5;color:#1a1a1a;background:#fff}
nav{margin-bottom:1.5rem;font-size:.9rem}
nav a{color:#0a5;text-decoration:none}
h1{margin-bottom:.25rem}
.crumb{color:#888;font-size:.85rem;margin:0}
.badges span{display:inline-block;background:#eee;border-radius:3px;padding:.1rem .5rem;margin-right:.3rem;font-size:.8rem}
form{margin:1rem 0}
input[type=text]{padding:.3rem;width:16rem}
input[type=submit]{padding:.3rem .8rem;margin-left:.3rem}
.live{border:1px solid #ddd;border-radius:6px;padding:1rem;margin-bottom:1rem}
.live b{display:block;margin-top:.5rem;word-break:break-all}
aside{border-left:3px solid #0a5;padding-left:1rem;color:#333}
pre{background:#f6f6f6;padding:.75rem;border-radius:4px;overflow-x:auto}
footer{margin-top:2rem;font-size:.8rem;color:#888}
`;

function top(values) {
  return `Content-Type: text/html

<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>${values.title} &middot; commix-testbed</title>
<meta name="robots" content="noindex">
<style>${STYLE}</style></head><body>
<nav><a href="${TESTBED_URL}/index.php">&larr; All scenarios</a> (served from its own Node process - see the note below)</nav>
<p class="crumb">${values.crumb || 'JavaScript scenarios'}</p><h1>${values.title}</h1>
<div class="badges"><span>${values.kind_label}</span>${values.code || ''}<span>JavaScript</span></div>
<section class="live"><h2>The page</h2>
<form action="${values.script}" method="POST">${values.label} <input type="text" name="${values.field}">
<input value="Submit!" type="submit"></form>
`;
}

function bottom(values) {
  return `</section><aside><h2>What this page does</h2>
<p>${values.explain}</p>
<p>${values.lesson}</p>
<p>Reported by commix as <b>${values.verdict}</b>.${values.origin || ''}</p>
<details><summary>The vulnerable code</summary><pre><code>${values.source}</code></pre></details>
<p><i>This one scenario runs as a plain Node HTTP server on its own port, not as CGI under the
main site's Apache - the base image's glibc predates any Node build that would run there.</i></p>
</aside>
<footer><p>Made in Greece with &hearts; by
<a href="https://github.com/stasinopoulos">Anastasios Stasinopoulos</a> &middot;
part of the <a href="https://commixproject.com">Commix Project</a>.</p>
<p>commix-testbed is GPLv3 licensed &copy; 2015-2026.</p></footer>
</body></html>
`;
}

module.exports = {top, bottom};
