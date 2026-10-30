#!/usr/bin/env lua
-- The value is evaluated as Lua rather than handed to a shell.
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

local user = form_value("user")

io.write(Layout.top{title = "eval() on the value", kind = "classic", kind_label = "Classic",
                     code = '<span class="tag">code</span>', script = "eval.lua",
                     label = "Enter your name:", field = "user"})

if user ~= "" then
  -- 'loadstring' is how 5.1 compiles a string (its own 'load' only takes a function); 5.4+ removed
  -- 'loadstring' outright and folded the same job into 'load' instead.
  local loadfn = loadstring or load
  local fn, load_err = loadfn("return 'Hello, ' .. '" .. user .. "' .. '!'")
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
  explain = "<code>load(\"return 'Hello, ' .. '\" .. user .. \"' .. '!'\")()</code> - the value is " ..
            "concatenated into a string that Lua then compiles and runs as code.",
  lesson = "Nothing here reaches a shell, so shell metacharacters mean nothing: the payload has to " ..
           "be a valid Lua expression. It also lands inside a quoted literal, so the quote has to " ..
           "be closed and reopened around it - which commix works out for itself once " ..
           "<code>--eval=lua</code> tells it what language the sink speaks.",
  verdict = "Classic code injection",
  solve = "<details class=\"solve\"><summary>Hint</summary>" ..
          "<table class=\"solve-glossary\"><tr><td><code>--eval=lua</code></td>" ..
          "<td>tells commix the sink evaluates Lua</td></tr></table></details>",
  source = "local loadfn = loadstring or load\n" ..
           "local fn = loadfn(&quot;return &#x27;Hello, &#x27; .. &#x27;&quot; .. user .. &quot;&#x27; .. &#x27;!&#x27;&quot;)\n" ..
           "print(fn())"
})
