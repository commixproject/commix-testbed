<p align="center">
  <img alt="commix-testbed" src="img/testbed_header_logo.png" height="120" />
</p>

<p align="center">
  <a href="https://hub.docker.com/r/commixproject/commix-testbed"><img alt="Docker" src="https://img.shields.io/badge/Docker-commix--testbed-2496ED.svg?style=for-the-badge&logo=docker&logoColor=white"></a>
  <a href="https://github.com/commixproject/commix-testbed/blob/master/doc/CHANGELOG.md"><img alt="Version 2.0" src="https://img.shields.io/badge/Version-2.0-2EA44F.svg?style=for-the-badge"></a>
  <a href="https://github.com/commixproject/commix-testbed/blob/master/doc/COPYING"><img alt="GPLv3 License" src="https://img.shields.io/badge/License-GPLv3-6A1B9A.svg?style=for-the-badge&logo=gnu&logoColor=white"></a>
  <a href="https://x.com/commixproject"><img alt="Follow @commixproject" src="https://img.shields.io/badge/Follow-@commixproject-000000.svg?style=for-the-badge&logo=x&logoColor=white"></a>
</p>

**commix-testbed** is a collection of deliberately vulnerable web pages, written by **[Anastasios Stasinopoulos](https://github.com/stasinopoulos)** (**[@ancst](https://x.com/ancst)**), used to exercise **[commix](https://github.com/commixproject/commix)**'s detection and exploitation features - and to learn what **[command](https://owasp.org/www-community/attacks/Command_Injection)** (and **[code](https://owasp.org/www-community/attacks/Code_Injection)**) injection actually looks like in code.

Every page contains the same underlying bug: a request value reaches a shell, or a string the application evaluates as code. What changes from page to page is everything around it - where the value lands, what the application does to it first, and how much of the result comes back. That is what separates an injection point you find in seconds from one that is nearly invisible, and it is why a scanner needs more than one technique.

Each scenario carries an explanation of what the code gets wrong and what the mistake teaches, so the collection reads as a tour of the bug class rather than a pile of targets.

![Screenshot](img/commix-testbed.png)

> [!WARNING]
> **These pages execute whatever they are given.** Run the testbed locally and keep it off any
> network you do not control.
>
> It is a target, not a demo you leave running.

## Scenarios

* **Regular parameters** - `GET`, `POST` and `PUT` bodies, JSON, GraphQL and SOAP/XML documents, Base64 and hex encodings, single and double quoting, HTTP Basic and Digest authentication, and a page where only two of three parameters are validated.
* **Code evaluation sinks** - `eval()`, `assert()`, `preg_replace()` with an attacker-controlled pattern, `create_function()`, and `str_replace()` sanitising into an evaluated string.
* **Weak filters** - denylisted metacharacters, whitespace rejection, anchored patterns that constrain only one end, lax domain validation, a command-name blocklist, a function-name blocklist that reads the query string and only the query string, and a check that inspects the first of two same-named parameters while the application reads the last. Each one is wrong in a different way.
* **HTTP headers** - `User-Agent`, `Cookie`, `Referer` and `X-Forwarded-Host`, plus a custom `X-Forwarded-For` header read for the access log.
* **Stateful and multi-step** - pages that ask for something carried between requests rather than something clever inside one: a login and the session it hands out, anti-CSRF tokens and single-use nonces, a second-order sink that answers on another page, account lockout, a connection held open, an endpoint that refuses at random, a request body sent chunked, non-standard parameter and cookie delimiters, an out-of-band-only sink, a document root that is not the default, output held back until the next request, a branch only a phone reaches, decoy parameters beside the real one, a body that is never percent-decoded, a sink that answers `500` while running the command, and a `Set-Cookie` that breaks the session if it is stored.
* **Discovery** - pages whose injectable surface is not written where a scan looks for it: a parameter the markup never names, routes that exist only inside the script a page loads, and an API that publishes an OpenAPI description of endpoints nothing links to.
* **CTF challenges** - real, named challenges recreated faithfully, filter and language included: YesWeHack Dojo #30 (Terminal Isolation), #36 (Shell Escape) and #41 (Ruby Treasure, the one page here served as a genuine Ruby CGI script rather than PHP standing in for it), YesWeHack Dojo #46 (Ghost Whisper), a Root-Me filter bypass and its PHP - Eval-hard, OverTheWire Natas Level 16, DVWA's own Command Injection at "high", ImaginaryCTF 2025's pearl (a genuine Perl CGI script, the two-argument `open()` pipe quirk rather than `eval()`), and DeadSec CTF 2024's Bing2.
* **Python scenarios** - the same classic, blind and code-injection bugs written in Python and served as CGI, so a run is not only exercising PHP.
* **Ruby scenarios** - the same `eval()`-on-the-value code injection, in Ruby.
* **Perl scenario** - the same `eval()`-on-the-value code injection, in Perl, matching commix's own `--eval=perl` support.
* **Lua scenario** - the same `eval()`-on-the-value code injection, in Lua, matching commix's own `--eval=lua` support.
* **JavaScript scenario** - the same `eval()`-on-the-value code injection, in Node, matching commix's own `--eval=javascript` support. Served by its own process rather than by the main site's Apache (see **[Running it](#running-it)**).
* **Java scenario** - the same bug again, evaluated through Java's own Nashorn engine rather than Node, so the `javascript` grammar is exercised against a second engine. Served by its own process (see **[Running it](#running-it)**).
* **Every reported type** - classic and blind, command and code injection, so all four combinations commix can report are covered.

Scenarios are labelled with the type commix reports them as, and each page says which option reaches it - several exist to exercise one option and cannot be reached without it. See the **[techniques](https://github.com/commixproject/commix/wiki/Techniques)** wiki page for what those mean and which technique reaches each one.

## Getting it

Clone the repository, on any platform :

    $ git clone https://github.com/commixproject/commix-testbed.git commix-testbed

## Running it

### With Docker

The published image already carries PHP, Python, Ruby, Perl and Lua, so one command gets you most of
the collection :

    $ docker run --rm -p 8080:80 commixproject/commix-testbed

Two scenarios are not in that image and run as servers of their own. To bring up everything at
once, from a clone of the repository :

    $ docker compose up -d

> [!NOTE]
> The first run takes a few minutes and looks idle for most of them: the image is rebuilt on top of
> the published one and installs Ruby and Lua from Ubuntu 14.04's archives. Later runs start in
> seconds. `docker compose logs -f testbed` shows what it is doing.

| | Address |
|---|---|
| The site - every PHP, Python, Ruby, Perl and Lua scenario | <http://127.0.0.1:8080> |
| JavaScript scenario (Node) | <http://127.0.0.1:8087> |
| Java scenario (Nashorn) | <http://127.0.0.1:8088> |

> [!NOTE]
> Those two are separate because of what the base image is. It is Ubuntu 14.04, chosen so the older
> PHP scenarios stay exploitable - and its glibc predates any Node build that still runs, while the
> newest JDK it can install is 7, which has no Nashorn.

### Without Docker

Nothing here is built or packaged, so serving the files is enough - but "the files" are not only
PHP, and a missing interpreter takes its own scenarios out while leaving the rest working.

| Scenarios | What they need |
|---|---|
| Every PHP page - the bulk of the collection | **[PHP](https://www.php.net/downloads)** 5.5 to 8.x |
| `scenarios/python`, and one CTF page | **Python 3**, run as CGI |
| `scenarios/ruby`, and one CTF page | **Ruby**, run as CGI |
| `scenarios/perl`, and one CTF page | **Perl**, run as CGI |
| `scenarios/lua` | **Lua** - any version; the pages pick `loadstring`/`setfenv` on 5.1 and `load` on 5.2+ themselves |
| `scenarios/javascript` | **Node**, as its own server on port `3000` |
| `scenarios/java` | A **JDK between 8 and 14** - Nashorn arrived in 8 and was removed in 15 - as its own server on port `8088` |

> [!IMPORTANT]
> **PHP's built-in server runs none of the CGI pages.** `php -S` serves PHP and static files only,
> so under it the Python, Ruby, Perl and Lua scenarios are downloaded as text instead of running.
> For those you need a web server that can execute CGI - the published image uses Apache with
> `mod_cgid`, `+ExecCGI` on each of those directories, and a handler bound to each extension.
>
> For the PHP pages alone, `php -S 127.0.0.1:8080` is fine.

Four PHP pages load on any version but are only *exploitable* on an older one: `create_function()`
was removed in PHP 8, the `e` regex modifier in PHP 7, and `assert()` stopped evaluating a string
argument in PHP 8. The published image ships PHP 5.5, so every scenario works there.

Three things the scenarios need from the host rather than from an interpreter :

* **`ping`** - most pages shell out to it, and a bare `php:apache` image does not have it.
* **A writable web root** - where the **file-based** technique writes its output. The container
  re-applies this on every start, so a volume mounted over the document root does not take it
  away; a hand-rolled install has to grant it. Where the server user cannot write there, the
  technique falls back to `/tmp/` and still reports the scenario as found, so a scan that looks
  like a pass may never have exercised it at all.
* **Outbound network access** - how the **out-of-band** technique hears back from the target.

And three the published image sets up, which a hand-rolled install has to repeat :

* `hidden/blind.php` is served from **outside the document root**, through an alias - the scenario
  exists precisely so the path cannot be guessed from the URL.
* The `kid` scenario resolves a filename against a **key directory outside the document root**
  (`/var/www/keys`, holding `prod.pem` and `legacy.pem`), so those files have to exist.
* The document root the file-based technique writes into must have **no `-` in its path**. One
  scenario filters `-` out of the value, which would corrupt the path along with the payload. In the
  published image that is what `/var/www/html` is for - a dash-free symlink to the real root, which
  does carry one - so pass `--web-root=/var/www/html` rather than the path it resolves to.

### Checking you have everything

Run this from a clone to see what a local install is still missing :

```bash
for c in php python3 ruby perl lua node java ping; do
  printf '%-8s %s\n' "$c" "$(command -v $c || echo 'MISSING')"
done
```

`php` alone is enough to start. Anything else missing costs you only the scenarios in the table
above that name it.

Once it is serving, this says whether each language is really executing rather than being handed
back as source :

```bash
for u in "scenarios/regular/GET/classic.php?addr=127.0.0.1" \
         "scenarios/python/eval.py?user=CMX" \
         "scenarios/ruby/eval.rb?user=CMX" \
         "scenarios/perl/eval.pl?user=CMX" \
         "scenarios/lua/eval.lua?user=CMX"; do
  printf '%-44s %s\n' "${u%%\?*}" "$(curl -s "http://127.0.0.1:8080/$u" | grep -c '<b>')"
done
```

A `0` there means that page was served but its interpreter never ran - under `php -S`, or without
the CGI handler for that extension. The JavaScript and Java scenarios answer on `8087` and `8088`
instead.

## Using it with commix

Open <http://127.0.0.1:8080> for the index, which lists every scenario with an explanation of the
flaw behind it. Each page also says which commix option reaches it - several exist to exercise one
option and cannot be found without it.

Rather than repeat them here, the commix wiki documents what to run :

* **[Usage](https://github.com/commixproject/commix/wiki/Usage)** - every option and switch, with a
  worked example for each.
* **[Usage examples](https://github.com/commixproject/commix/wiki/Usage-examples)** - whole runs
  against real targets, this testbed included.
* **[Techniques](https://github.com/commixproject/commix/wiki/Techniques)** - what the four
  techniques are, and which one reaches which kind of page.
* **[Filters bypass examples](https://github.com/commixproject/commix/wiki/Filters-bypass-examples)** - the tamper scripts, against the weak-filter scenarios here.

## Bugs and Enhancements

For bug reports or enhancements regarding commix-testbed, please open an [issue](https://github.com/commixproject/commix-testbed/issues). A good new scenario demonstrates something the existing pages do not: a different sink, a different place the value arrives from, or a filter that fails for a reason none of the others already show.

## Links

* Testbeds guide: https://github.com/commixproject/commix/wiki/Command-injection-testbeds
* Issues tracker: https://github.com/commixproject/commix-testbed/issues
* Project site: https://commixproject.com
