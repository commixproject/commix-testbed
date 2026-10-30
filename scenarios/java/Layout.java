// The page chrome the Java scenario uses.
//
// Standalone rather than sharing the PHP/Python/Ruby pages' own stylesheet: this page is served by
// its own JVM process rather than the same Apache document root, so a relative "../../css/..."
// would resolve against the wrong origin. TESTBED_URL names where the main site actually is, so
// the shared stylesheet, logo and "back" link can still point at it directly - a stylesheet link
// works cross-origin with no CORS of its own to arrange.

import java.util.HashMap;
import java.util.Map;

class Layout {
  static final String TESTBED_URL = System.getenv().getOrDefault("TESTBED_URL", "http://localhost:8081");

  // Where each verdict's own technique is explained, for the one link the page makes to the wiki.
  static final String TECHNIQUES = "https://github.com/commixproject/commix/wiki/Techniques";
  static final Map<String, String> VERDICT_LINKS = new HashMap<>();
  static {
    VERDICT_LINKS.put("Classic command injection", TECHNIQUES + "#1-results-based-classic-command-injection");
    VERDICT_LINKS.put("Classic code injection", TECHNIQUES + "#2-dynamic-code-evaluation");
    VERDICT_LINKS.put("Results-based code injection", TECHNIQUES + "#2-dynamic-code-evaluation");
    VERDICT_LINKS.put("Blind command injection", TECHNIQUES + "#blind-injections");
    VERDICT_LINKS.put("Blind code injection", TECHNIQUES + "#blind-injections");
  }

  static class Values {
    String title, crumb, kind, kindLabel, code, script, label, field;
    String explain, lesson, verdict, solve, source;
  }

  static String top(Values v) {
    return "<!DOCTYPE html><html lang=\"en\"><head><meta charset=\"utf-8\">\n" +
      "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n" +
      "<title>" + v.title + " &middot; commix-testbed</title>\n" +
      "<meta name=\"robots\" content=\"noindex\">\n" +
      "<link rel=\"shortcut icon\" href=\"" + TESTBED_URL + "/favicon.ico\">\n" +
      "<link href=\"https://fonts.googleapis.com/css2?family=Exo+2:wght@400;500;600;700&amp;display=swap\" rel=\"stylesheet\">\n" +
      "<link rel=\"stylesheet\" href=\"" + TESTBED_URL + "/css/testbed.css?v=3\">\n" +
      "</head>\n<body>\n\n" +
      "<nav class=\"nav\">\n  <div class=\"wrap\">\n" +
      "    <a class=\"brand\" href=\"" + TESTBED_URL + "/index.php\">\n" +
      "      <img src=\"" + TESTBED_URL + "/img/testbed_header_logo.png\" alt=\"commix-testbed\"></a>\n" +
      "    <a class=\"back\" href=\"" + TESTBED_URL + "/index.php\">&larr; All scenarios</a>\n" +
      "  </div>\n</nav>\n\n" +
      "<div class=\"wrap page\">\n\n" +
      "  <header class=\"sc-head\">\n" +
      "    <p class=\"crumb\">" + (v.crumb != null ? v.crumb : "Java scenarios") + "</p>\n" +
      "    <h1>" + v.title + "</h1>\n" +
      "    <span class=\"badges\"><span class=\"type t-" + v.kind + "\">" + v.kindLabel + "</span>" +
      (v.code != null ? v.code : "") + "<span class=\"lang\">Java</span></span>\n" +
      "  </header>\n\n" +
      "  <div class=\"cols\">\n" +
      "    <section class=\"panel live\">\n" +
      "      <h2>The page</h2>\n" +
      "      <form action=\"" + v.script + "\" method=\"POST\">" + v.label +
      " <input type=\"text\" name=\"" + v.field + "\">\n" +
      "      <input value=\"Submit!\" type=\"submit\"></form>\n";
  }

  static String bottom(Values v) {
    String verdictUrl = VERDICT_LINKS.get(v.verdict);
    String verdictHtml = verdictUrl != null
      ? "<a href=\"" + verdictUrl + "\">" + v.verdict + "</a>"
      : v.verdict;
    return "    </section>\n\n" +
      "    <aside class=\"panel\">\n" +
      "      <h2>What this page does</h2>\n" +
      "      <p class=\"explain\">" + v.explain + "</p>\n" +
      "      <p class=\"lesson\">" + v.lesson + "</p>\n" +
      (v.solve != null ? v.solve : "") + "\n" +
      "      <p class=\"verdict\">Identified by commix as <b>" + verdictHtml + "</b>.</p>\n" +
      "      <details class=\"src\">\n" +
      "        <summary>The vulnerable code</summary>\n" +
      "        <pre><code>" + v.source + "</code></pre>\n" +
      "      </details>\n" +
      "      <div class=\"note\"><p>This one scenario runs as a plain Java HTTP server on its own\n" +
      "      port, not as CGI under the main site's Apache - the base image ships no JVM at all.</p></div>\n" +
      "    </aside>\n" +
      "  </div>\n\n" +
      "  <footer>\n" +
      "    <p>Made in Greece with <span class=\"heart\">&hearts;</span> by\n" +
      "      <a href=\"https://github.com/stasinopoulos\">Anastasios Stasinopoulos</a> &middot;\n" +
      "      part of the <a href=\"https://commixproject.com\">Commix Project</a>.</p>\n" +
      "    <p class=\"copy\">commix-testbed is GPLv3 licensed &copy; 2015-2026.</p>\n" +
      "  </footer>\n" +
      "</div>\n\n</body>\n</html>\n";
  }
}
