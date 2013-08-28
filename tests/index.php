<?php

header("Some-foo-header: to/check/with/head/request");
header("Content-type: text/plain", TRUE, 200);
$out = array();

$out['server'] = $_SERVER;
$out['request'] = $_REQUEST;
$out['cokies'] = $_COOKIE;
$out['get'] = $_GET;
$out['post'] = $_POST;

$method = $_SERVER['REQUEST_METHOD'];
if ($method != "GET") {
  $contents = file_get_contents("php://input");
  $parsed_contents = null;
  parse_str($contents, $parsed_contents);
  $out['PARSED_HTTP_DATA']= $parsed_contents;
  $out['_RAW_HTTP_DATA'] = $contents;
}

die(var_export($out, true));