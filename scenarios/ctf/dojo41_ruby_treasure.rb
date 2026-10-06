#!/usr/bin/env ruby
# frozen_string_literal: true

# Validated per line rather than across the whole string, and IO.read reads a leading '|' as a
# shell command rather than a filename - two Ruby quirks that only add up to a bug together.
require 'cgi'

cgi = CGI.new
Dir.chdir(File.join(File.dirname(__FILE__), 'dojo41_views'))

def validateFile(filename)
  return !!/^[a-zA-Z0-9_-]+$/.match(filename) || filename.length == 0
end

filename = cgi['page'] || ''
content = ''
if filename != ''
  if validateFile(filename)
    begin
      content = IO.read("#{filename}.erb")
    rescue StandardError
      content = IO.read('collection.erb')
    end
  else
    content = IO.read('collection.erb')
  end
end

source = <<-'SRC'
  def validateFile(filename)
    return !!/^[a-zA-Z0-9_-]+$/.match(filename) || filename.length == 0
  end

  filename = CGI.unescape(params['page'])
  if validateFile(filename)
    begin
      content = IO.read("#{filename}.erb")
    rescue
      content = IO.read("collection.erb")
    end
  end
  puts ERB.new(IO.read("index.erb")).result_with_hash({page: content})
SRC

body = <<-HTML
  <!DOCTYPE html>
  <html lang="en">
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ruby treasure &middot; commix-testbed</title>
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
      <h1>Ruby treasure</h1>
      <span class="badges"><span class="type t-classic">Classic</span><span class="lang">Ruby</span></span>
    </header>

    <div class="cols">
      <section class="panel live">
        <h2>The page</h2>
        <form action="dojo41_ruby_treasure.rb" method="GET">
          Page name: <input type="text" name="page" size="40">
          <input value="Submit!" type="submit">
        </form>
        <br>
        <b>#{CGI.escapeHTML(content)}</b>
      </section>

      <aside class="panel">
        <h2>What this page does</h2>
        <p class="explain"><code>validateFile()</code> checks <code>/^[a-zA-Z0-9_-]+$/.match(filename)</code>, but Ruby's <code>^</code> and <code>$</code> match at the start and end of every line in a multi-line string, not of the whole string. One clean line anywhere is enough for the whole value to pass. <code>IO.read</code> then treats a value starting with <code>|</code> as a command to run through a shell, not a filename to open.</p>
        <p class="lesson">A value like <code>"|cmd\\nx"</code> passes validation - line 2, <code>"x"</code>, matches - and then runs as a shell command, since line 1 starts with <code>|</code>. Two Ruby-specific behaviors, neither a bug on its own, that only add up to one together.</p>
        <p class="verdict">Reported by commix as <b>Classic command injection</b>. Based on <a href="https://www.yeswehack.com/dojo/dojo-ctf-challenge-winners-41">YesWeHack Dojo #41: Ruby Treasure</a>.</p>
        <details class="src">
          <summary>The vulnerable code</summary>
          <pre><code>#{CGI.escapeHTML(source)}</code></pre>
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
  (function () {
    var out = document.querySelector(".live > b:last-of-type");
    if (!out) return;
    var lines = out.textContent.split(/\\r?\\n/);
    var filled = lines.filter(function (l) { return l.trim(); });
    var common = filled.reduce(function (n, l) { return Math.min(n, l.match(/^[ \\t]*/)[0].length); }, 1e9);
    if (filled.length) {
      out.textContent = lines.map(function (l) { return l.slice(common); }).join(String.fromCharCode(10)).trim();
    }
    if (!out.textContent.trim()) { out.remove(); }
  })();
  </script>
  </body>
  </html>
HTML

puts cgi.header('type' => 'text/html')
puts body
