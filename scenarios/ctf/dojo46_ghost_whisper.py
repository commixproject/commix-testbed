#!/usr/bin/python3
# A quote is replaced before the value is normalized - which is exactly backwards.
import cgi
import os
import sys
import unicodedata

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)) + "/../python")
import _layout

form = cgi.FieldStorage()
whisper = form.getvalue("input", "")

print(_layout.top(title="Ghost whisper", kind="classic", kind_label="Classic", crumb="CTF challenges",
                  script="dojo46_ghost_whisper.py", label="Whisper something:", field="input"))

if whisper:
    clean = unicodedata.normalize("NFKC", whisper.replace("'", "_"))
    with os.popen("echo -n '" + clean + "' | od -A x -t x1z 2>&1") as stream:
        hextext = stream.read() + " | " + clean
    print("<b>" + hextext.replace("<", "&lt;").replace(">", "&gt;") + "</b>")

print(_layout.bottom(
    explain="<code>whisperMsg.replace(\"'\", \"_\")</code> strips a literal quote before the value "
            "is folded through <code>unicodedata.normalize(\"NFKC\", ...)</code>, which "
            "compatibility-folds a wide range of lookalike characters into their plain ASCII form. "
            "The fullwidth apostrophe (U+FF07, ＇) is one of them: it is not the <code>'</code> "
            "the replace matches, so it passes straight through, and normalization turns it into a "
            "real <code>'</code> afterwards - closing the quote the filter believed it had already "
            "neutralized.",
    lesson="Sanitizing before normalizing gets the order backwards: whatever runs last is the one "
           "the filter needed to see. Applied first, normalization would have turned the lookalike "
           "character into a plain quote before the replace ever ran.",
    verdict="Classic command injection",
    origin=" Based on <a href=\"https://www.yeswehack.com/dojo/dojo-challenge-solution-46\">"
           "YesWeHack Dojo #46: Ghost Whisper</a>.",
    source="whisperMsg = unicodedata.normalize(&quot;NFKC&quot;, whisperMsg.replace(&quot;&#x27;&quot;, &quot;_&quot;))\n"
           "with os.popen(f&quot;echo -n &#x27;{whisperMsg}&#x27; | od -A x -t x1z&quot;) as stream:\n"
           "    hextext = f&quot;{stream.read()} | {whisperMsg}&quot;\n"
           "    print(template.render(msg=whisperMsg, hextext=hextext))"))
