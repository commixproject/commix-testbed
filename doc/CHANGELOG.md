## Version 1.0
* Added: Python scenarios - classic, blind and code injection - served as CGI, so the same bugs can be exercised in a second language.
* Added: Scenarios are tagged with the language they are written in, and the index can be filtered by it.
* Added: A `Dockerfile` that updates the published image in place, enabling CGI for the Python scenarios.
* Added: New scenario category for the `Host` / `X-Forwarded-Host` request header (classic and blind).
* Added: New scenario category for a custom `X-Forwarded-For` request header (classic and blind).
* Added: `PUT`-only endpoint, which reads its body with `parse_str()` rather than from `$_POST`.
* Added: Multiple-parameter example, where only two of the three values are validated.
* Added: Blind code injection example - the value is evaluated as PHP and the result is never printed.
* Added: Every scenario now carries an explanation of what the code gets wrong and what the mistake teaches.
* Added: The vulnerable source of each scenario is shown on its own page.
* Added: Pages that take no form input (JSON, SOAP/XML, and the header-based ones) now state what a request to them looks like.
* Added: Search and per-type filtering on the index, and collapsible categories.
* Revised: Rebuilt the user interface; the scenario index and every scenario page now share one stylesheet.
* Revised: Scenarios are labelled with the injection type commix reports them as - classic or blind, command or code injection.
* Revised: `simple_start_alphanum.php` and `simple_stop_alphanum.php` now anchor where their names say they do.
* Fixed: `cookie(classic_quote).php` had a stray parenthesis that stopped the page from parsing at all, and was never linked from the index.
* Fixed: Undefined index, undefined property and empty-document errors raised on PHP 7 and later, without changing what reaches the sink.
* Fixed: A scenario ending in `die()` no longer takes the rest of the page with it.
* Fixed: The `<?xml` declaration shown on the SOAP/XML pages is no longer parsed as PHP where short open tags are enabled.
* Removed: Bootstrap, jQuery and the bundled web fonts, none of which were still used.

## Version 0.1
* Added: `assert()` example, where a function that reads like a sanity check evaluates its argument as PHP.
* Added: Filter example that blacklists command names rather than metacharacters, on both Windows and Unix-like targets.
* Added: SOAP/XML request-body examples, classic and blind, where two elements of the document reach the same command.
* Added: Nested-quotes example, where the value lands inside double quotes that are themselves inside a PHP double-quoted string.
* Added: Hex example, where the parameter is only accepted if it survives a hex round trip before being unpacked into the command.
* Added: `create_function()` example, where the value is compiled as the body of a function.
* Added: A category of weak regex filters, each one failing for a different reason.
* Added: Non-space example, where whitespace is rejected outright and the payload has to separate its words another way.
* Added: Classic example behind HTTP Digest authentication, which answers a per-request challenge rather than one static header.
* Added: Classic example behind HTTP Basic authentication, where an unauthenticated scan reports the page as not injectable.
* Added: Windows-based command execution examples, so the cmd.exe payload shapes are exercised alongside the POSIX ones.
* Added: Double blind examples, where output is discarded and the command backgrounded, so not even its exit status reaches the page (based on [this](https://github.com/commixproject/commix/issues/17) issue).
* Added: Classic, eval and blind JSON examples, where the value arrives in a request body rather than a form field.
* Added: Cookie example whose value is Base64 and is decoded before it reaches the command.
* Added: Base64 example, where only a value that survives a Base64 round trip is accepted.
* Added: `str_replace()` example, where quotes are stripped and the value is then evaluated inside a double-quoted string.
* Added: `preg_replace()` example, where both the pattern and the replacement come from the request, so its modifiers belong to the attacker.
* Added: Backtick filtering examples.
* Added: Blacklisting examples, where a `str_replace()` strips a handful of metacharacters and passes over the value only once.
* Added: Hashing example, where the value is injected ahead of a pipe into `md5sum` and the parameter is not the usual one.
* Added: Double-quote examples, where the value lands inside double quotes - which still expand command substitution.
* Added: Blind regexp examples, where the same validation mistake has to be found without reading any output.
* Added: Regexp examples, where an IP-format check is anchored with the multiline flag, so a second line walks past it.
* Added: Referer-based examples, classic, blind and eval.
* Added: User-Agent based examples, classic, blind and eval.
* Added: Cookie based examples, classic, blind and eval.
* The initial commit.
