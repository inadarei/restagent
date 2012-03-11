<?php

header("Content-type: text/plain", TRUE, 200);
$out = array();

$out['server'] = $_SERVER;
$out['request'] = $_REQUEST;
$out['cokies'] = $_COOKIE;
$out['get'] = $_GET;
$out['post'] = $_POST;

die(var_export($out, true));