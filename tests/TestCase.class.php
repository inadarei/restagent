<?php

namespace restagent;

/**
 * Abstract parent for RestAgent test classes.
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase {

  protected $server;

  public function setUp() {
    $this->server_url  = isset($_ENV['server_url'])  ? $_ENV['server_url']  : 'http://127.0.0.1:8080';
  }

}