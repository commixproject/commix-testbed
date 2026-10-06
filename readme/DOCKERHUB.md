<!-- Paste this into the repository description on hub.docker.com.
     Docker Hub cannot resolve relative paths, so every image is an absolute raw.githubusercontent URL. -->

<p align="center">
  <img alt="commix-testbed" src="https://raw.githubusercontent.com/commixproject/commix-testbed/master/img/testbed_header_logo.png" height="120" />
</p>

**commix-testbed** is a collection of deliberately vulnerable web pages, written by **[Anastasios Stasinopoulos](https://github.com/stasinopoulos)** (**[@ancst](https://x.com/ancst)**), used to exercise **[commix](https://github.com/commixproject/commix)**'s detection and exploitation features - and to learn what **[command](https://owasp.org/www-community/attacks/Command_Injection)** (and **[code](https://owasp.org/www-community/attacks/Code_Injection)**) injection actually looks like in code.

Every page contains the same underlying bug: a request value reaches a shell, or a string the application evaluates as code. What changes from page to page is everything around it - where the value lands, what the application does to it first, and how much of the result comes back. That is what separates an injection point you find in seconds from one that is nearly invisible, and it is why a scanner needs more than one technique.

Each scenario carries an explanation of what the code gets wrong and what the mistake teaches, so the collection reads as a tour of the bug class rather than a pile of targets.

![Screenshot](https://raw.githubusercontent.com/commixproject/commix-testbed/master/img/commix-testbed.png)

> [!WARNING]
> **These pages execute whatever they are given.** Keep the container on a network you control, and
> do not publish the port beyond your own machine.
>
> It is a target, not a demo you leave running.

## Quick start

    $ docker run --rm -p 8080:80 commixproject/commix-testbed

Then open <http://127.0.0.1:8080> for the index, which lists every scenario alongside an
explanation of the flaw behind it.

Point commix at any of them:

    $ python3 commix.py --url="http://127.0.0.1:8080/scenarios/regular/GET/classic.php?addr=127.0.0.1"

## What is inside

* **Regular parameters** - `GET`, `POST` and `PUT` bodies, JSON and SOAP/XML documents, Base64 and hex encodings, single and double quoting, HTTP Basic and Digest authentication, and a page where only two of three parameters are validated.
* **Code evaluation sinks** - `eval()`, `assert()`, `preg_replace()` with an attacker-controlled pattern, `create_function()`, and `str_replace()` sanitising into an evaluated string.
* **Weak filters** - denylisted metacharacters, whitespace rejection, anchored patterns that constrain only one end, lax domain validation, and a command-name blocklist. Each one is wrong in a different way.
* **HTTP headers** - `User-Agent`, `Cookie`, `Referer` and `X-Forwarded-Host`, plus a custom `X-Forwarded-For` header read for the access log.
* **Python scenarios** - the same classic, blind and code-injection bugs written in Python and served as CGI, so a run is not only exercising PHP.

Scenarios are labelled with the injection type commix reports them as - classic or blind, command or
code injection. See the
**[techniques](https://github.com/commixproject/commix/wiki/Techniques)** wiki page for what those
mean and which technique reaches each one.

## Tags

| Tag | Contents |
|---|---|
| `latest` | Whatever the testbed currently is. Moves with every release, so a rebuild can bring new scenarios with it. |
| `1.0` | The rebuilt testbed: 77 scenarios across PHP and Python, each carrying an explanation of the flaw behind it, on a web root the file-based technique can write to. Pinned, so it will not change under a run you are part-way through. |

Pin `1.0` for anything repeatable - a workshop, a CI job, a tutorial someone follows months from
now. Use `latest` to see what has been added since.

## What the image ships

The image runs PHP 5.5 on Apache, and that is deliberate: `create_function()` was removed in PHP 8,
the `e` regex modifier in PHP 7, and `assert()` stopped evaluating a string argument in PHP 8. On a
modern interpreter four scenarios would load but could never be exploited. Here every one of them
works.

`ping`, `python3` and the tools commix reaches for after it lands are present, the web root is
writable so the **file-based** technique can write its output and read it back, and CGI is enabled
for the Python scenarios. The **out-of-band** technique needs the container to be able to reach the
internet.

## Links

* Source: https://github.com/commixproject/commix-testbed
* Issues: https://github.com/commixproject/commix-testbed/issues
* Testbeds guide: https://github.com/commixproject/commix/wiki/Command-injection-testbeds
* Project site: https://commixproject.com
