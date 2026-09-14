#!/usr/bin/python3
# The value goes straight into a shell command, and the output is written back to the page.
import cgi
import os
import subprocess
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import _layout

form = cgi.FieldStorage()
addr = form.getvalue("addr", "")

print(_layout.top(title="Unfiltered parameter", kind="classic", kind_label="Classic",
                  script="classic.py", label="Ping address:", field="addr"))

if addr:
    # Execute command!
    try:
        out = subprocess.check_output("ping -c 4 " + addr, shell=True, stderr=subprocess.STDOUT)
    except subprocess.CalledProcessError as err:
        out = err.output
    print("<b>" + out.decode("utf-8", "replace") + "</b>")

print(_layout.bottom(
    explain="<code>subprocess.check_output(cmd, shell=True)</code> builds the command by "
            "concatenation, so the value is parsed by a shell before anything runs. The output is "
            "written back to the page.",
    lesson="The same bug as the PHP pages, in another language - which is the point of having it "
           "here. <code>shell=True</code> is what turns a string into something a shell parses; "
           "passing a list of arguments without it would have closed the hole entirely.",
    verdict="Classic command injection",
    source="out = subprocess.check_output(&quot;ping -c 4 &quot; + addr, shell=True,\n"
           "                              stderr=subprocess.STDOUT)"))
