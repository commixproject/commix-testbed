#!/usr/bin/env lua
-- The value is evaluated as Lua inside a restricted environment, rather than a plain load().
local script_dir = arg[0]:match("(.*/)") or "./"
package.path = script_dir .. "?.lua;" .. package.path
local Layout = require("layout")

local function url_decode(value)
  value = value:gsub("+", " ")
  value = value:gsub("%%(%x%x)", function(hex) return string.char(tonumber(hex, 16)) end)
  return value
end

local function form_value(name)
  local raw = ""
  if (os.getenv("REQUEST_METHOD") or "") == "POST" then
    raw = io.read(tonumber(os.getenv("CONTENT_LENGTH") or "0")) or ""
  else
    raw = os.getenv("QUERY_STRING") or ""
  end
  for pair in raw:gmatch("[^&]+") do
    local key, value = pair:match("([^=]*)=?(.*)")
    if key and url_decode(key) == name then
      return url_decode(value or "")
    end
  end
  return ""
end

local function html_escape(text)
  return (text:gsub("<", "&lt;"))
end

-- 'os'/'io' left out on purpose - the rule is meant to compute, not touch the system. 'debug' stays
-- in, since nothing about it looks dangerous the way 'os'/'io' obviously would.
local SANDBOX_ENV = {
  print = print, pairs = pairs, ipairs = ipairs, tostring = tostring, tonumber = tonumber,
  string = string, math = math, table = table, debug = debug,
}

-- 5.1 binds an environment to an already-compiled function with 'setfenv'; 5.2+ folds the same job
-- into 'load' itself, as its fourth argument.
local function sandboxed_load(code)
  if setfenv then
    local fn, err = loadstring(code)
    if fn then setfenv(fn, SANDBOX_ENV) end
    return fn, err
  else
    return load(code, "rule", "t", SANDBOX_ENV)
  end
end

local rule = form_value("rule")

io.write(Layout.top{title = "Discount rule (sandboxed)", kind = "classic", kind_label = "Classic",
                     code = '<span class="tag">code</span>', script = "sandbox.lua",
                     label = "Discount rule, e.g. price*0.9:", field = "rule"})

if rule ~= "" then
  local fn, load_err = sandboxed_load("return " .. rule)
  local out
  if fn then
    local ok, result = pcall(fn)
    out = tostring(result)
  else
    out = tostring(load_err)
  end
  io.write("<b>" .. html_escape(out) .. "</b>")
end

io.write(Layout.bottom{
  explain = "The rule is run with a restricted environment handed to <code>load</code>/<code>setfenv</code> " ..
            "in place of the real one - the kind of thing a pricing engine reaches for to keep a rule " ..
            "from touching anything else on the system.",
  lesson = "Leaving <code>os</code> and <code>io</code> out of that environment blocks the obvious " ..
           "calls, but <code>debug</code> reads straight past it - the standard library modules a " ..
           "restricted environment never mentions are still sitting in <code>package.loaded</code>, " ..
           "loaded there once at the interpreter's own startup, and " ..
           "<code>debug.getregistry()._LOADED</code> reaches that table directly, with no 'os' or " ..
           "'io' global anywhere in sight.",
  verdict = "Classic code injection",
  solve = "<details class=\"solve\"><summary>Hint</summary>" ..
          "<p>The rule is run whole, with nothing around it - start from a parenthesised number " ..
          "(<code>--data=\"rule=(0)\"</code>), not a bare digit, since this language's own '..' " ..
          "reads a digit immediately followed by two dots as the start of a malformed number, not " ..
          "as that digit concatenated with whatever follows.</p>" ..
          "<table class=\"solve-glossary\"><tr><td><code>--eval=lua</code></td>" ..
          "<td>tells commix the sink evaluates Lua</td></tr>" ..
          "<tr><td><code>--tamper=luadebugescape</code></td>" ..
          "<td>reaches commix's own <code>os</code>/<code>io</code> calls through " ..
          "<code>debug.getregistry()._LOADED</code>, for a payload evaluated with a restricted " ..
          "environment that leaves them out</td></tr></table></details>",
  source = "local fn = sandboxed_load(&quot;return &quot; .. rule)\n" ..
           "print(fn())"
})
