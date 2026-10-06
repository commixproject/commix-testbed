// The routes this console talks to. Nothing on the page links to any of them: the markup carries a
// single empty <div>, and these strings are the only place the endpoints are written down.
var API = {
  ping:    "/scenarios/regular/GET/classic.php?addr=127.0.0.1",
  greet:   "/scenarios/regular/GET/eval.php?user=test",
  logo:    "/img/testbed_header_logo.png"
};

function call(route) {
  return fetch(route).then(function (r) { return r.text(); });
}

document.addEventListener("DOMContentLoaded", function () {
  var app = document.getElementById("app");
  if (app) {
    app.textContent = "This console talks to " + Object.keys(API).length + " routes. View source on this file to see them.";
  }
});
