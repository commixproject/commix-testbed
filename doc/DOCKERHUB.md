<p align="center">
  <img alt="commix-testbed" src="https://raw.githubusercontent.com/commixproject/commix-testbed/master/img/testbed_header_logo.png" height="120" />
</p>

**commix-testbed** is a collection of deliberately vulnerable web pages, written by **[Anastasios Stasinopoulos](https://github.com/stasinopoulos)** (**[@ancst](https://x.com/ancst)**), used to exercise **[commix](https://github.com/commixproject/commix)**'s detection and exploitation features - and to learn what **[command](https://owasp.org/www-community/attacks/Command_Injection)** (and **[code](https://owasp.org/www-community/attacks/Code_Injection)**) injection actually looks like in code.

Every page contains the same underlying bug: a request value reaches a shell, or a string the application evaluates as code. What changes from page to page is everything around it - where the value lands, what the application does to it first, and how much of the result comes back. That is what separates an injection point you find in seconds from one that is nearly invisible, and it is why a scanner needs more than one technique.

Each scenario carries an explanation of what the code gets wrong and what the mistake teaches, so the collection reads as a tour of the bug class rather than a pile of targets.

![Screenshot](https://raw.githubusercontent.com/commixproject/commix-testbed/master/img/commix-testbed.png)

> **Warning - these pages execute whatever they are given.** Keep the container on a network you control, and
> do not publish the port beyond your own machine.
>
> It is a target, not a demo you leave running.

## Quick start

    $ docker run --rm -p 8080:80 commixproject/commix-testbed

Then open <http://127.0.0.1:8080> for the index, which lists every scenario alongside an
explanation of the flaw behind it. Each page also says which commix option reaches it - several
exist to exercise one option and cannot be found without it.

The commix wiki documents what to run against them: **[Usage](https://github.com/commixproject/commix/wiki/Usage)**,
**[Usage examples](https://github.com/commixproject/commix/wiki/Usage-examples)**,
**[Techniques](https://github.com/commixproject/commix/wiki/Techniques)** and
**[Filters bypass examples](https://github.com/commixproject/commix/wiki/Filters-bypass-examples)**.

> **Note** - the document root is `/var/www/commix-testbed.com/public_html`, and the **file-based**
> technique writes there as it stands. `/var/www/html` is a dash-free symlink to the same directory:
> pass `--web-root=/var/www/html` for the one scenario that filters `-` out of the value, which
> would otherwise corrupt that path along with the payload.

## What is inside

Around 130 pages. Every one carries the same underlying bug and differs in everything around it.

| Family | Pages | What they vary |
|---|---|---|
| Regular parameters | 49 | `GET`, `POST` and `PUT` bodies, JSON, GraphQL and SOAP/XML documents, Base64 and hex encodings, quoting, HTTP Basic and Digest auth |
| Stateful and multi-step | 23 | things carried between requests: sessions, anti-CSRF tokens, nonces, second-order sinks, lockout, chunked bodies, an out-of-band-only sink |
| Weak filters | 19 | denylisted metacharacters, whitespace rejection, anchored patterns, lax domain validation, command-name and function-name blocklists |
| CTF challenges | 10 | real named challenges recreated faithfully, filter and language included |
| HTTP headers | 15 | `User-Agent`, `Cookie`, `Referer`, `Host`, `X-Forwarded-Host` and a custom `X-Forwarded-For` |
| Discovery | 5 | surfaces a scan cannot see: an unnamed parameter, routes only in a script, a published OpenAPI description |
| Python / Ruby / Perl / Lua | 9 | the same sinks one language over, served as CGI |

Scenarios are labelled with the injection type commix reports them as - classic or blind, command or
code injection, so all four combinations are covered.

## Tags

| Tag | Contents |
|---|---|
| `latest` | Whatever the testbed currently is. Moves with every release, so a rebuild can bring new scenarios with it. |
| `2.0` | The current testbed: around 130 scenarios across PHP, Python, Ruby, Perl and Lua, adding CTF recreations, discovery pages, stateful flows and the weak-filter set. Pinned. |

Pin `2.0` for anything repeatable - a workshop, a CI job, a tutorial someone follows months from
now. Use `latest` to see what has been added since.

## What the image ships

The image runs **PHP 5.5 on Apache**, and that is deliberate: `create_function()` was removed in PHP
8, the `e` regex modifier in PHP 7, and `assert()` stopped evaluating a string argument in PHP 8. On
a modern interpreter four scenarios would load but could never be exploited. Here every one works.

Also present: **Python 3**, **Ruby**, **Perl** and **Lua**, with CGI enabled for each of their
directories, and **`ping`**, which most pages shell out to. The web root is writable so the
**file-based** technique can write its output and read it back - re-applied on every start, so
mounting a volume over the document root does not quietly take it away; the **out-of-band**
technique needs the container to be able to reach the internet.

## Two scenarios are not in this image

The JavaScript (Node) and Java (Nashorn) pages run as servers of their own, because this image's
base is Ubuntu 14.04 - chosen for that old PHP - and its glibc predates any Node build that still
runs, while the newest JDK it can install is 7, which has no Nashorn.

To bring up everything at once, use the compose file from the repository instead :

    $ git clone https://github.com/commixproject/commix-testbed.git && cd commix-testbed
    $ docker compose up -d

That serves the site on `8080`, the JavaScript scenario on `8087` and the Java one on `8088`.

## Links

* Source: https://github.com/commixproject/commix-testbed
* Issues: https://github.com/commixproject/commix-testbed/issues
* Testbeds guide: https://github.com/commixproject/commix/wiki/Command-injection-testbeds
* Project site: https://commixproject.com
