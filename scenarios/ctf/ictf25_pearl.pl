#!/usr/bin/perl
# A trailing pipe on a two-argument open() is not part of the filename - it tells Perl to run
# everything before it as a shell command and read that command's own output instead of a file.
use strict;
use warnings;
use FindBin;
use lib "$FindBin::Bin/../perl";
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

# A pipe chaining into another command is stripped; a pipe with nothing after it is left alone, on
# the assumption that a pipe needs a second command on its far side to do anything.
sub read_note {
  my ($name) = @_;
  $name =~ s/\|(?=\s*\S)//g;
  my $out;
  if (open(my $fh, $name)) {
    local $/;
    $out = <$fh>;
    close($fh);
  } else {
    $out = "could not open: $!";
  }
  return $out // "";
}

my $note = form_value("note");

print Layout::top(title => "Pearl", kind => "classic", kind_label => "Classic", crumb => "CTF challenges",
                   code => '<span class="tag">code</span>', script => "ictf25_pearl.pl",
                   label => "Note filename:", field => "note");

if ($note ne "") {
  my $out = read_note($note);
  $out =~ s/</&lt;/g;
  print "<b>" . $out . "</b>";
}

print Layout::bottom(
  explain => "<code>open(\$fh, \$note)</code> is Perl's two-argument <code>open</code> - a lone " .
             "trailing <code>|</code> on the filename is not a character of the name at all, it " .
             "tells Perl to run everything before it as a shell command and read that command's " .
             "own output in place of a file's contents.",
  lesson => "The filter only strips a <code>|</code> that still has something to chain into, on the " .
            "assumption a pipe needs a second command on its far side to matter - exactly backwards " .
            "for this sink, where a pipe with nothing after it is the one that runs a command, and a " .
            "pipe with something after it is just a character in a filename that doesn't exist.",
  verdict => "Classic command injection",
  origin => " Based on <a href=\"https://jia.je/ctf-writeups/2025-09-05-imaginary-ctf-2025/pearl.html\">" .
            "ImaginaryCTF 2025: pearl</a>.",
  solve => "<details class=\"solve\"><summary>Hint</summary>" .
           "<table class=\"solve-glossary\"><tr><td><code>--suffix=\"|\"</code></td>" .
           "<td>ends every payload in a bare pipe, so the command run before it is read back as " .
           "the value instead of a filename</td></tr></table></details>",
  source => "my \$name = \$note;\n" .
            "\$name =~ s/\\|(?=\\s*\\S)//g;\n" .
            "open(my \$fh, \$name) or die \$!;\n" .
            "local \$/;\n" .
            "print &quot;&lt;b&gt;&quot; . &lt;\$fh&gt; . &quot;&lt;/b&gt;&quot;;");
