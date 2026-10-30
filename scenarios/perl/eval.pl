#!/usr/bin/perl
# The value is evaluated as Perl rather than handed to a shell.
use strict;
use warnings;
use FindBin;
use lib $FindBin::Bin;
use Layout;

sub url_decode {
  my ($value) = @_;
  $value =~ tr/+/ /;
  $value =~ s/%([0-9A-Fa-f]{2})/chr(hex($1))/ge;
  return $value;
}

sub form_value {
  my ($name) = @_;
  my $raw = "";
  if (($ENV{REQUEST_METHOD} // "") eq "POST") {
    read(STDIN, $raw, $ENV{CONTENT_LENGTH} // 0);
  } else {
    $raw = $ENV{QUERY_STRING} // "";
  }
  for my $pair (split(/&/, $raw)) {
    my ($key, $value) = split(/=/, $pair, 2);
    next unless defined $key;
    return url_decode($value // "") if url_decode($key) eq $name;
  }
  return "";
}

my $user = form_value("user");

print Layout::top(title => "eval() on the value", kind => "classic", kind_label => "Classic",
                   code => '<span class="tag">code</span>', script => "eval.pl",
                   label => "Enter your name:", field => "user");

if ($user ne "") {
  my $result = eval("'Hello, ' . '" . $user . "' . '!'");
  my $out = defined($result) ? $result : $@;
  $out =~ s/</&lt;/g;
  print "<b>" . $out . "</b>";
}

print Layout::bottom(
  explain => "<code>eval(\"'Hello, ' . '\" . \$user . \"' . '!'\")</code> - the value is concatenated " .
             "into a string that Perl then evaluates as an expression.",
  lesson => "Nothing here reaches a shell, so shell metacharacters mean nothing: the payload has to " .
            "be a valid Perl expression. It also lands inside a quoted literal, so the quote has to " .
            "be closed and reopened around it - which commix works out for itself once " .
            "<code>--eval=perl</code> tells it what language the sink speaks.",
  verdict => "Classic code injection",
  solve => "<details class=\"solve\"><summary>Hint</summary>" .
           "<table class=\"solve-glossary\"><tr><td><code>--eval=perl</code></td>" .
           "<td>tells commix the sink evaluates Perl</td></tr></table></details>",
  source => "my \$result = eval(&quot;&#x27;Hello, &#x27; . &#x27;&quot; . \$user . &quot;&#x27; . &#x27;!&#x27;&quot;);\n" .
            "print &quot;&lt;b&gt;&quot; . \$result . &quot;&lt;/b&gt;&quot;;");
