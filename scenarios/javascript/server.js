#!/usr/bin/env node
// The value is evaluated as JavaScript rather than handed to a shell.
//
// Standalone rather than CGI like the Python and Ruby pages: the testbed's base image carries a
// glibc from 2014, too old for any Node built this decade, and a Node app is ordinarily its own
// long-running server rather than a process Apache launches per request - so that is what this is.
'use strict';

const http = require('http');
const vm = require('vm');
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

function evalScenario(user) {
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
    solve: '<details class="solve"><summary>Hint</summary>' +
           '<table class="solve-glossary"><tr><td><code>--eval=javascript</code></td>' +
           '<td>tells commix the sink evaluates Node/JavaScript</td></tr></table></details>',
    source: 'out += &quot;&lt;b&gt;&quot; + String(eval(&quot;&#x27;Hello, &#x27; + &#x27;&quot; + ' +
            'user + &quot;&#x27; + &#x27;!&#x27;&quot;)) + &quot;&lt;/b&gt;&quot;',
  });

  return out;
}

function vmSandboxScenario(rule) {
  let out = layout.top({
    title: 'Pricing rule (sandboxed)', kind: 'classic', kind_label: 'Classic',
    code: '<span class="tag">code</span>', script: '/vm-sandbox',
    label: 'Discount rule, e.g. price*0.9:', field: 'rule',
  });

  if (rule) {
    try {
      const sandbox = {};
      vm.createContext(sandbox);
      const value = vm.runInContext(rule, sandbox, {timeout: 1000});
      out += '<b>' + String(value).replace(/</g, '&lt;') + '</b>';
    } catch (e) {
      out += '<b>' + String(e.message).replace(/</g, '&lt;') + '</b>';
    }
  }

  out += layout.bottom({
    explain: 'The rule is run with <code>vm.runInContext()</code>, in a context built from an ' +
             'empty, prototype-less object - the kind of thing a pricing engine or a rules ' +
             'feature reaches for, since it looks like exactly the sandbox the job calls for.',
    lesson: 'Node\'s own documentation says <code>vm</code> "is not a security mechanism" - it ' +
            'separates which variables an object graph starts with, not what the language itself ' +
            'can still do. Every object, sandboxed or not, inherits <code>constructor</code> from ' +
            '<code>Object.prototype</code>, and <code>Function</code>\'s own constructor compiles ' +
            'new code in the real global scope, outside the sandbox entirely - ' +
            '<code>this.constructor.constructor("return process")()</code> hands back the host\'s ' +
            'own <code>process</code>, with <code>require</code> reachable from there.',
    verdict: 'Classic code injection',
    solve: '<details class="solve"><summary>Hint</summary>' +
           '<p>The rule is run whole, with nothing around it - start from a number ' +
           '(<code>--data="rule=0"</code>), not a bare word, since the seed value has to be ' +
           'valid JavaScript on its own before anything gets appended to it.</p>' +
           '<table class="solve-glossary"><tr><td><code>--eval=javascript</code></td>' +
           '<td>tells commix the sink evaluates Node/JavaScript</td></tr>' +
           '<tr><td><code>--tamper=jsvmescape</code></td>' +
           '<td>reaches commix\'s own require() through the constructor chain, for a payload ' +
           'landing inside a vm sandbox rather than a plain eval()</td></tr></table></details>',
    source: 'const sandbox = {};\n' +
            'vm.createContext(sandbox);\n' +
            'const value = vm.runInContext(rule, sandbox, {timeout: 1000});',
  });

  return out;
}

const server = http.createServer(async (req, res) => {
  const raw = req.method === 'POST' ? await readBody(req) : (req.url.split('?')[1] || '');
  const params = querystring.parse(raw);
  const urlPath = req.url.split('?')[0];

  let out;
  if (urlPath === '/vm-sandbox') {
    out = vmSandboxScenario(params.rule || '');
  } else {
    out = evalScenario(params.user || '');
  }

  res.writeHead(200, {'Content-Type': 'text/html'});
  res.end(out);
});

server.listen(process.env.PORT || 3000, '0.0.0.0');
