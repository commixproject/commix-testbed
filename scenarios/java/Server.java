// The value is evaluated as JavaScript, through Java's own Nashorn engine, rather than handed to
// a shell.
//
// Standalone rather than CGI like the other pages: the testbed's base image ships no JVM at all,
// and a Java app is ordinarily its own long-running server rather than a process Apache launches
// per request - so that is what this is, the same way the Node scenario is.

import com.sun.net.httpserver.HttpExchange;
import com.sun.net.httpserver.HttpHandler;
import com.sun.net.httpserver.HttpServer;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.InetSocketAddress;
import java.net.URLDecoder;
import java.nio.charset.StandardCharsets;
import javax.script.ScriptEngine;
import javax.script.ScriptEngineManager;

public class Server implements HttpHandler {
  static String readBody(InputStream in) throws IOException {
    ByteArrayOutputStream out = new ByteArrayOutputStream();
    byte[] buf = new byte[4096];
    int n;
    while ((n = in.read(buf)) != -1) out.write(buf, 0, n);
    return out.toString("UTF-8");
  }

  static String param(String raw, String name) {
    if (raw == null) return "";
    for (String pair : raw.split("&")) {
      int eq = pair.indexOf('=');
      String key = eq < 0 ? pair : pair.substring(0, eq);
      try {
        key = URLDecoder.decode(key, "UTF-8");
      } catch (Exception e) { /* leave undecoded */ }
      if (key.equals(name)) {
        String value = eq < 0 ? "" : pair.substring(eq + 1);
        try {
          return URLDecoder.decode(value, "UTF-8");
        } catch (Exception e) {
          return value;
        }
      }
    }
    return "";
  }

  static String nashornScenario(String rule) {
    Layout.Values v = new Layout.Values();
    v.title = "Discount rule (Nashorn)";
    v.kind = "classic";
    v.kindLabel = "Classic";
    v.code = "<span class=\"tag\">code</span>";
    v.script = "/";
    v.label = "Discount rule, e.g. price*0.9:";
    v.field = "rule";

    StringBuilder out = new StringBuilder(Layout.top(v));

    if (rule != null && !rule.isEmpty()) {
      try {
        ScriptEngine engine = new ScriptEngineManager().getEngineByName("nashorn");
        Object value = engine.eval(rule);
        out.append("<b>").append(String.valueOf(value).replace("<", "&lt;")).append("</b>");
      } catch (Exception e) {
        String msg = e.getMessage() == null ? e.toString() : e.getMessage();
        out.append("<b>").append(msg.replace("<", "&lt;")).append("</b>");
      }
    }

    v.explain = "The rule is run with Java's own <code>ScriptEngineManager</code>, through the " +
      "engine registered for <code>\"nashorn\"</code> - the kind of thing a pricing engine or a " +
      "rules feature embedded in a Java application reaches for when the rule itself is meant to " +
      "read like a short, ordinary expression.";
    v.lesson = "Nashorn parses real JavaScript, but it is not a sandbox - unless the embedding " +
      "application builds its own <code>ClassFilter</code> and passes it to the engine " +
      "explicitly, every class on the JVM's own classpath is reachable from the script exactly " +
      "as if it were a global variable. <code>java.lang.ProcessBuilder</code> starts a real " +
      "process and <code>java.nio.file.Files</code> reads a real file, both without any " +
      "<code>require()</code> or import the way Node would need one.";
    v.verdict = "Classic code injection";
    v.solve = "<details class=\"solve\"><summary>Hint</summary>" +
      "<p>The rule is run whole, with nothing around it - start from a number " +
      "(<code>--data=\"rule=0\"</code>), not a bare word, since the seed value has to be valid " +
      "JavaScript on its own before anything gets appended to it.</p>" +
      "<table class=\"solve-glossary\"><tr><td><code>--eval=javascript</code></td>" +
      "<td>tells commix the sink evaluates JavaScript - Nashorn parses the same syntax the " +
      "grammar already writes</td></tr>" +
      "<tr><td><code>--tamper=nashornexec</code></td>" +
      "<td>rewrites the grammar's <code>require()</code> calls, which only exist in Node, into a " +
      "shim reaching the same job through Java interop instead</td></tr></table></details>";
    v.source = "ScriptEngine engine = new ScriptEngineManager().getEngineByName(&quot;nashorn&quot;);\n" +
      "Object value = engine.eval(rule);";

    out.append(Layout.bottom(v));
    return out.toString();
  }

  @Override
  public void handle(HttpExchange exchange) throws IOException {
    String raw = "GET".equals(exchange.getRequestMethod())
      ? exchange.getRequestURI().getRawQuery()
      : readBody(exchange.getRequestBody());

    String out = nashornScenario(param(raw, "rule"));
    byte[] bytes = out.getBytes(StandardCharsets.UTF_8);
    exchange.getResponseHeaders().set("Content-Type", "text/html");
    exchange.sendResponseHeaders(200, bytes.length);
    try (OutputStream os = exchange.getResponseBody()) {
      os.write(bytes);
    }
  }

  public static void main(String[] args) throws IOException {
    int port = Integer.parseInt(System.getenv().getOrDefault("PORT", "8088"));
    HttpServer server = HttpServer.create(new InetSocketAddress("0.0.0.0", port), 0);
    server.createContext("/", new Server());
    server.setExecutor(null);
    server.start();
  }
}
