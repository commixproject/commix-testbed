#!/usr/bin/python3
# The value is evaluated as Python with no builtins, rather than handed to a shell.
import cgi
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import _layout

form = cgi.FieldStorage()
rule = form.getvalue("rule", "")

print(_layout.top(title="Discount rule (sandboxed)", kind="classic", kind_label="Classic",
                  code='<span class="tag">code</span>', script="sandbox.py",
                  label="Discount rule, e.g. price*0.9:", field="rule"))

if rule:
    try:
        print("<b>" + str(eval(rule, {"__builtins__": {}})).replace("<", "&lt;") + "</b>")
    except Exception as e:
        print("<b>" + str(e).replace("<", "&lt;") + "</b>")

print(_layout.bottom(
    explain="The rule is run with <code>eval(rule, {&quot;__builtins__&quot;: {}})</code> - the kind "
            "of thing a pricing engine reaches for when the rule is meant to run as code without "
            "touching anything else on the system.",
    lesson="An empty <code>__builtins__</code> blocks every builtin NAME, not the language itself - "
           "<code>().__class__.__bases__[0].__subclasses__()</code> reaches every class Python has "
           "already loaded through plain attribute access, no builtin call of its own needed, and one "
           "of them still carries a real <code>__builtins__</code> in its own module's globals.",
    verdict="Classic code injection",
    solve="<details class=\"solve\"><summary>Hint</summary>"
          "<p>The rule is run whole, with nothing around it - start from an empty string "
          "(<code>--data=\"rule=&#x27;&#x27;\"</code>), not a bare word, since the seed value has to "
          "be valid Python on its own before anything gets appended to it. Python does not coerce a "
          "number and a string the way some other languages here do, so a string is what the rest of "
          "the payload can actually be appended onto.</p>"
          "<table class=\"solve-glossary\"><tr><td><code>--eval=python</code></td>"
          "<td>tells commix the sink evaluates Python</td></tr>"
          "<tr><td><code>--tamper=pybuiltinsescape</code></td>"
          "<td>reaches commix's own <code>__import__</code> and <code>open</code> through the "
          "subclasses of <code>object</code>, for a payload evaluated with no builtins at all</td></tr>"
          "</table></details>",
    source="print(&quot;&lt;b&gt;&quot; + str(eval(rule, {&quot;__builtins__&quot;: {}})) + "
           "&quot;&lt;/b&gt;&quot;)"))
