# The page chrome the Ruby scenarios share, matching the PHP and Python ones.
#
# Not a CGI script itself - the Apache configuration keeps it from being served.

module Layout
  TOP = <<-'TOP'
Content-Type: text/html

<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>%{title} &middot; commix-testbed</title>
<meta name="robots" content="noindex">
<link rel="shortcut icon" href="../../favicon.ico">
<link href="https://fonts.googleapis.com/css?family=Exo+2:400,500,600,700" rel="stylesheet">
<link rel="stylesheet" href="../../css/testbed.css"></head><body>
<nav class="nav"><div class="wrap">
<a class="brand" href="../../index.php"><img src="../../img/testbed_header_logo.png" alt="commix-testbed"></a>
<a class="back" href="../../index.php">&larr; All scenarios</a></div></nav>
<div class="wrap page">
<header class="sc-head"><p class="crumb">%{crumb}</p><h1>%{title}</h1>
<span class="badges"><span class="type t-%{kind}">%{kind_label}</span>%{code}<span class="lang">Ruby</span></span>
</header><div class="cols"><section class="panel live"><h2>The page</h2>
<form action="%{script}" method="POST">%{label} <input type="text" name="%{field}">
<input value="Submit!" type="submit"></form>
TOP

  BOTTOM = <<-'BOTTOM'
</section><aside class="panel"><h2>What this page does</h2>
<p class="explain">%{explain}</p>
<p class="lesson">%{lesson}</p>
<p class="verdict">Reported by commix as <b>%{verdict}</b>.%{origin}</p>
<details class="src"><summary>The vulnerable code</summary><pre><code>%{source}</code></pre></details>
</aside></div>
<footer><p>Made in Greece with <span class="heart">&hearts;</span> by
<a href="https://github.com/stasinopoulos">Anastasios Stasinopoulos</a> &middot;
part of the <a href="https://commixproject.com">Commix Project</a>.</p>
<p class="copy">commix-testbed is GPLv3 licensed &copy; 2015-2026.</p></footer>
</div></body></html>
BOTTOM

  def self.top(opts)
    opts = {code: "", crumb: "Ruby scenarios"}.merge(opts)
    TOP % opts
  end

  def self.bottom(opts)
    opts = {origin: ""}.merge(opts)
    BOTTOM % opts
  end
end
