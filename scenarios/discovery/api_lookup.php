<?php
// An API endpoint, named only by the specification the service publishes. Nothing links here.
header("Content-Type: text/plain; charset=utf-8");
$host = isset($_GET['host']) ? $_GET['host'] : '';
if ($host === '') {
  http_response_code(400);
  echo "host is required\n";
  exit;
}
echo shell_exec("/bin/ping -c 4 ".$host." 2>&1");
