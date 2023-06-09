<?php

$matches = $_GET = [];

// Excepcion para las  url principal sea index.html
if (in_array($_SERVER["REQUEST_URI"], ['/index.html', '/', ''])) {
  echo file_get_contents('index.html');
  die;
}

if (preg_match('/\/([^\/]+)\/([^\/]+)/', $_SERVER["REQUEST_URI"], $matches)) {
  $_GET['resource_type'] = $matches[1];
  $_GET['resource_id'] = $matches[2];

  error_log(print_r($matches, 1));
  require 'server.php';
} elseif (preg_match('/\/([^\/]+)\/?/', $_SERVER["REQUEST_URI"], $matches)) {
  $_GET['resource_type'] = $matches[1];
  error_log(print_r($matches, 1));

  require 'test.php'; //aca se delega el control
} else {

  error_log('No matches');
  http_response_code(404);//Not Found
}