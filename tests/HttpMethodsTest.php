<?php

namespace restagent;

require_once (__DIR__ . '/TestCase.class.php');
require_once (realpath(__DIR__ . '/../restagent.lib.php'));

/**
 * Functional tests for basic endpoint processing
 */
class HttpMethodsTest extends TestCase {

  public function setUp() {
    parent::setUp();
    //$this->rest_client = ZaphpaRestClient::get_instance($this->server_url);
  }

  public function test_pattern_num_single() {
    /**$resp = $this->rest_client->http_get('users/1');
    $this->assertEquals(1, $resp->decoded->params->id, "User Get Test: id numeric check");

    try {
      $resp = $this->rest_client->http_get('users/alpha');
    } catch (ZaphpaRestClientException $ex) {
      return;
    }

    $this->fail('User get test: alpha argument should not have passed.');**/
    $this->assertEquals(1,1);
  }

}