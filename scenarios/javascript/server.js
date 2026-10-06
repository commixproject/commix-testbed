#!/usr/bin/env node
// The value is evaluated as JavaScript rather than handed to a shell.
//
// Standalone rather than CGI like the Python and Ruby pages: the testbed's base image carries a
// glibc from 2014, too old for any Node built this decade, and a Node app is ordinarily its own
// long-running server rather than a process Apache launches per request - so that is what this is.
'use strict';

const http = require('http');
const querystring = require('querystring');
const path = require('path');
const layout = require(path.join(__dirname, '_layout.js'));

function readBody(req) {
  return new Promise((resolve) => {
    let data = '';
    req.on('data', (chunk) => { data += chunk; });
    req.on('end', () => resolve(data));
  });
}

const server = http.createServer(async (req, res) => {
  const raw = req.method === 'POST' ? await readBody(req) : (req.url.split('?')[1] || '');
  const params = querystring.parse(raw);
  const user = params.user || '';

  let out = layout.top({
    title: 'eval on the value', kind: 'classic', kind_label: 'Classic',
    code: '<span class="tag">code</span>', script: '/',
    label: 'Enter your name:', field: 'user',
  });

  if (user) {
    try {
      out += '<b>' + String(eval("'Hello, ' + '" + user + "' + '!'")) + '</b>';
    } catch (e) {
      out += '<b>' + String(e.message).replace(/</g, '&lt;') + '</b>';
    }
  }

  out += layout.bottom({
    explain: '<code>eval("\'Hello, \' + \'" + user + "\' + \'!\'")</code> - the value is ' +
             'concatenated into a string that Node then evaluates as an expression.',
    lesson: 'The same sink shape as the PHP, Python and Ruby pages, one language over. Nothing ' +
            'here reaches a shell, so shell metacharacters are meaningless - the payload has to ' +
            'be a valid JavaScript expression, and commix works out how to close and reopen the ' +
            'quote it lands inside once <code>--eval=javascript</code> tells it what language the ' +
            'sink speaks.',
    verdict: 'Classic code injection',
    source: 'out += &quot;&lt;b&gt;&quot; + String(eval(&quot;&#x27;Hello, &#x27; + &#x27;&quot; + ' +
            'user + &quot;&#x27; + &#x27;!&#x27;&quot;)) + &quot;&lt;/b&gt;&quot;',
  });

  res.writeHead(200, {'Content-Type': 'text/html'});
  res.end(out);
});

server.listen(process.env.PORT || 3000, '0.0.0.0');
