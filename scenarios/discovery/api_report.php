<?php
// The same surface reached with a JSON body, the way a described API is usually called.
header("Content-Type: text/plain; charset=utf-8");
$body = file_get_contents("php://input");
$sent = json_decode($body, true);
$host = is_array($sent) && isset($sent['host']) ? $sent['host'] : '';
if ($host === '') {
  http_response_code(400);
  echo "host is required\n";
  exit;
}
echo shell_exec("/bin/ping -c 4 ".$host." 2>&1");
