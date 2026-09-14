#!/usr/bin/python3
# The command runs, but nothing it produces reaches the page.
import cgi
import os
import subprocess
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import _layout

form = cgi.FieldStorage()
addr = form.getvalue("addr", "")

print(_layout.top(title="Output never reflected", kind="blind", kind_label="Blind",
                  script="blind.py", label="Ping address:", field="addr"))

if addr:
    # Execute command!
    rc = subprocess.call("ping -c 4 " + addr, shell=True,
                         stdout=open(os.devnull, "wb"), stderr=subprocess.STDOUT)
    print("<b>The ip " + cgi.escape(addr) +
          (" seems to be up and running!" if rc == 0 else " seems to be down!") + "</b>")

print(_layout.bottom(
    explain="<code>subprocess.call(cmd, shell=True)</code> with the output sent to "
            "<code>os.devnull</code>. All the page reports is whether the exit status was zero.",
    lesson="The same shell concatenation as the classic Python page, with the evidence taken away. "
           "Only the exit status leaks, which is a single bit - so proving execution means timing "
           "the reply, writing a file, or reaching a server of your own.",
    verdict="Blind command injection",
    source="rc = subprocess.call(&quot;ping -c 4 &quot; + addr, shell=True,\n"
           "                     stdout=open(os.devnull, &quot;wb&quot;), stderr=subprocess.STDOUT)"))
