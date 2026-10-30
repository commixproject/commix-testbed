#!/usr/bin/env ruby
# The value is evaluated as Ruby inside a BasicObject, rather than handed to a shell.
require 'cgi'

require_relative '_layout'

cgi = CGI.new
rule = cgi['rule'] || ''

print Layout.top(title: "Discount rule (sandboxed)", kind: "classic", kind_label: "Classic",
                  code: '<span class="tag">code</span>', script: "sandbox.rb",
                  label: "Discount rule, e.g. price*0.9:", field: "rule")

unless rule.empty?
  begin
    result = BasicObject.new.instance_eval(rule)
    print "<b>" + CGI.escapeHTML(result.to_s) + "</b>"
  rescue ::Exception => e
    print "<b>" + CGI.escapeHTML(e.message) + "</b>"
  end
end

print Layout.bottom(
  explain: "The rule is run with <code>BasicObject.new.instance_eval(rule)</code> - the kind of " \
           "thing a pricing engine reaches for when the rule is meant to run as a small DSL " \
           "without touching the rest of the application.",
  lesson: "<code>BasicObject</code> does not inherit from <code>Object</code>, so none of " \
          "<code>Kernel</code>'s methods are defined on it - no backtick, no <code>sleep</code>, " \
          "no <code>exit</code>. But <code>instance_eval</code> and <code>__send__</code> are " \
          "defined directly on <code>BasicObject</code> itself, and <code>::Kernel</code> is " \
          "still a reachable top-level constant - <code>::Kernel.instance_eval{ ... }</code> " \
          "runs a block with <code>self</code> rebound to <code>Kernel</code>, where all three " \
          "work exactly as they would outside any sandbox.",
  verdict: "Classic code injection",
  solve: "<details class=\"solve\"><summary>Hint</summary>" \
         "<p>The rule is run whole, with nothing around it - start from an empty string " \
         "(<code>--data=\"rule=&#x27;&#x27;\"</code>), not a bare word, since the seed value has " \
         "to be valid Ruby on its own before anything gets appended to it.</p>" \
         "<table class=\"solve-glossary\"><tr><td><code>--eval=ruby</code></td>" \
         "<td>tells commix the sink evaluates Ruby</td></tr>" \
         "<tr><td><code>--tamper=basicobjectescape</code></td>" \
         "<td>runs the payload with <code>Kernel</code> as <code>self</code>, for a payload " \
         "evaluated inside a <code>BasicObject</code> rather than a plain <code>eval()</code></td></tr>" \
         "</table></details>",
  source: "result = BasicObject.new.instance_eval(rule)\n" \
          "print &quot;&lt;b&gt;&quot; + result.to_s + &quot;&lt;/b&gt;&quot;")
