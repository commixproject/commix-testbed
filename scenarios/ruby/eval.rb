#!/usr/bin/env ruby
# The value is evaluated as Ruby rather than handed to a shell.
require 'cgi'

require_relative '_layout'

cgi = CGI.new
user = cgi['user'] || ''

print Layout.top(title: "eval on the value", kind: "classic", kind_label: "Classic",
                  code: '<span class="tag">code</span>', script: "eval.rb",
                  label: "Enter your name:", field: "user")

unless user.empty?
  begin
    print "<b>" + eval("'Hello, ' + '" + user + "' + '!'").to_s + "</b>"
  rescue StandardError => e
    print "<b>" + CGI.escapeHTML(e.message) + "</b>"
  end
end

print Layout.bottom(
  explain: "<code>eval(\"'Hello, ' + '\" + user + \"' + '!'\")</code> - the value is concatenated " \
           "into a string that Ruby then evaluates as an expression.",
  lesson: "Nothing here reaches a shell, so shell metacharacters mean nothing: the payload has to " \
          "be a valid Ruby expression. It also lands inside a quoted literal, so the quote has to " \
          "be closed and reopened around it - which commix works out for itself once " \
          "<code>--eval=ruby</code> tells it what language the sink speaks.",
  verdict: "Classic code injection",
  source: "print &quot;&lt;b&gt;&quot; + eval(&quot;&#x27;Hello, &#x27; + &#x27;&quot; + user + &quot;&#x27; + &#x27;!&#x27;&quot;).to_s + &quot;&lt;/b&gt;&quot;")
