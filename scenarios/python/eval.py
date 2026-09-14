#!/usr/bin/python3
# The value is evaluated as Python rather than handed to a shell.
import cgi
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import _layout

form = cgi.FieldStorage()
user = form.getvalue("user", "")

print(_layout.top(title="eval() on the value", kind="classic", kind_label="Classic",
                  code='<span class="tag">code</span>', script="eval.py",
                  label="Enter your name:", field="user"))

if user:
    # Execute command!
    print("<b>" + str(eval("'Hello, ' + '" + user + "' + '!'")) + "</b>")

print(_layout.bottom(
    explain="<code>eval(\"'Hello, ' + '\" + user + \"' + '!'\")</code> - the value is concatenated "
            "into a string that Python then evaluates as an expression.",
    lesson="Nothing here reaches a shell, so shell metacharacters mean nothing: the payload has to "
           "be a valid Python expression. It also lands inside a quoted literal, so the quote has to "
           "be closed and reopened around it - which commix works out for itself once "
           "<code>--eval=python</code> tells it what language the sink speaks.",
    verdict="Classic code injection",
    source="print(&quot;&lt;b&gt;&quot; + str(eval(&quot;&#x27;Hello, &#x27; + &#x27;&quot; + user + &quot;&#x27; + &#x27;!&#x27;&quot;)) + &quot;&lt;/b&gt;&quot;)"))
