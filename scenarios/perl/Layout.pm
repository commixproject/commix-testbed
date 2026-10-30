# The page chrome the Perl scenario uses, matching the PHP/Python/Ruby ones.
#
# Not a CGI script itself - the Apache configuration keeps it from being served.
package Layout;

use strict;
use warnings;

my $TECHNIQUES = "https://github.com/commixproject/commix/wiki/Techniques";
my %VERDICT_LINKS = (
  "Classic command injection" => $TECHNIQUES . "#1-results-based-classic-command-injection",
  "Classic code injection" => $TECHNIQUES . "#2-dynamic-code-evaluation",
  "Results-based code injection" => $TECHNIQUES . "#2-dynamic-code-evaluation",
  "Blind command injection" => $TECHNIQUES . "#blind-injections",
  "Blind code injection" => $TECHNIQUES . "#blind-injections",
);

sub top {
  my %opt = @_;
  $opt{code} //= "";
  $opt{crumb} //= "Perl scenarios";
  return "Content-Type: text/html\n\n" .
    "<!DOCTYPE html><html lang=\"en\"><head><meta charset=\"utf-8\">\n" .
    "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n" .
    "<title>$opt{title} &middot; commix-testbed</title>\n" .
    "<meta name=\"robots\" content=\"noindex\">\n" .
    "<link rel=\"shortcut icon\" href=\"../../favicon.ico\">\n" .
    "<link href=\"https://fonts.googleapis.com/css2?family=Exo+2:wght\@400;500;600;700&amp;display=swap\" rel=\"stylesheet\">\n" .
    "<link rel=\"stylesheet\" href=\"../../css/testbed.css?v=3\"></head><body>\n" .
    "<nav class=\"nav\"><div class=\"wrap\">\n" .
    "<a class=\"brand\" href=\"../../index.php\"><img src=\"../../img/testbed_header_logo.png\" alt=\"commix-testbed\"></a>\n" .
    "<a class=\"back\" href=\"../../index.php\">&larr; All scenarios</a></div></nav>\n" .
    "<div class=\"wrap page\">\n" .
    "<header class=\"sc-head\"><p class=\"crumb\">$opt{crumb}</p><h1>$opt{title}</h1>\n" .
    "<span class=\"badges\"><span class=\"type t-$opt{kind}\">$opt{kind_label}</span>$opt{code}<span class=\"lang\">Perl</span></span>\n" .
    "</header><div class=\"cols\"><section class=\"panel live\"><h2>The page</h2>\n" .
    "<form action=\"$opt{script}\" method=\"POST\">$opt{label} <input type=\"text\" name=\"$opt{field}\">\n" .
    "<input value=\"Submit!\" type=\"submit\"></form>\n";
}

sub bottom {
  my %opt = @_;
  $opt{origin} //= "";
  $opt{solve} //= "";
  my $url = $VERDICT_LINKS{$opt{verdict}};
  my $verdict_html = $url ? "<a href=\"$url\">$opt{verdict}</a>" : $opt{verdict};
  return "</section><aside class=\"panel\"><h2>What this page does</h2>\n" .
    "<p class=\"explain\">$opt{explain}</p>\n" .
    "<p class=\"lesson\">$opt{lesson}</p>\n" .
    "$opt{solve}<p class=\"verdict\">Identified by commix as <b>$verdict_html</b>.$opt{origin}</p>\n" .
    "<details class=\"src\"><summary>The vulnerable code</summary><pre><code>$opt{source}</code></pre></details>\n" .
    "</aside></div>\n" .
    "<footer><p>Made in Greece with <span class=\"heart\">&hearts;</span> by\n" .
    "<a href=\"https://github.com/stasinopoulos\">Anastasios Stasinopoulos</a> &middot;\n" .
    "part of the <a href=\"https://commixproject.com\">Commix Project</a>.</p>\n" .
    "<p class=\"copy\">commix-testbed is GPLv3 licensed &copy; 2015-2026.</p></footer>\n" .
    "</div>\n</body></html>\n";
}

1;
