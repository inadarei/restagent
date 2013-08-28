<?php

namespace restagent;

require_once (__DIR__ . '/TestCase.class.php');
require_once (realpath(__DIR__ . '/../restagent.lib.php'));

/**
 * Functional tests for basic endpoint processing
 */
class HttpMethodsTest extends TestCase {

  private $request;

  public function setUp() {
    parent::setUp();
    $this->request = new \restagent\Request($this->server_url);
  }

  public function test_get() {

    $http_response = $this->request->header('Content-Type', 'application/json')
      ->data(array("firstName" => "irakli", "lastName" => "Nadareishvili"))
      ->data("hobby", "programming")
      ->header("X-API-Key", "aabbccdd")
      ->header(array("foo" => "bar", "one" => "two"))
      ->get("/somepath");

    $response = array();
    
    eval('$response = ' . $http_response['data'] . ";");

    $this->assertEquals("GET", $response['server']['REQUEST_METHOD'],
      "Test of correct method transmitted for HTTP GET");

    $get = $response['get'];
    $get_vars_correct = ($get['firstName'] == 'irakli' &&
                         $get['lastName'] == 'Nadareishvili' &&
                         $get['hobby'] == 'programming');

    $this->assertEquals(true, $get_vars_correct,
      "Test of data() functioning properly for HTTP GET");

    $this->assertEquals("application/json", $response['server']['CONTENT_TYPE'],
      "Test1 (content-type) of header() functioning properly for HTTP GET");

    $this->assertEquals("bar", $response['server']['HTTP_FOO'],
      "Test2 (custom headers, passed as array) of header() functioning properly for HTTP GET");

    try {
      $this->request->header('Content-Type', 'application/json') // This is invalid
        ->data(array("firstName" => "irakli", "lastName" => "Nadareishvili"))
        ->param("foo", "bar")
        ->get("/somepath");
    } catch (RestAgentException $ex) {
      $this->assertTrue(true);
      return;
    }

    $this->fail('You should not be able to call param() when trying to make a get() call.');

  }

  public function test_full_url_get() {

    $req = new \restagent\Request;

    $resp = $req->header('Content-Type', 'text/plain')
      ->data("q", "restagent")
      ->get("http://www.bing.com/search");

    $this->assertEquals("http://www.bing.com/search?q=restagent", $resp['meta']['url']);
    $this->assertEquals(200, $resp['meta']['http_code']);

  }

  /**
   * @TODO: implement firing exception if Content-Type is set during POST request and implement
   *       a test that verifies the firing of such exception
   */
  public function test_post() {

    $http_response = $this->request
      ->data(array("firstName" => "irakli", "lastName" => "Nadareishvili"))
      ->data("hobby", "programming")
      ->header("X-API-Key", "aabbccdd")
      ->header(array("foo" => "bar", "one" => "two"))
      ->post("/somepath");

    $response = array();
    eval('$response = ' . $http_response['data'] . ";");

    $this->assertEquals($response['server']['REQUEST_METHOD'],"POST",
      "Test of correct method transmitted for HTTP POST");

    $data = $response['post'];
    $data_vars_correct = ($data['firstName'] == 'irakli' &&
      $data['lastName'] == 'Nadareishvili' &&
      $data['hobby'] == 'programming');

    $this->assertEquals(true, $data_vars_correct,
      "Test of data() functioning properly for HTTP POST");


    $this->assertEquals("application/x-www-form-urlencoded", $response['server']['CONTENT_TYPE'],
      "Test1 (content-type) of header() functioning properly for HTTP POST");

    $this->assertEquals("bar",$response['server']['HTTP_FOO'],
      "Test2 (custom headers, passed as array) of header() functioning properly for HTTP POST");
  }

  public function test_post_disallow_content_type() {
    $this->request->header('Content-Type', 'application/x-www-form-urlencoded') // This is valid
      ->data(array("firstName" => "irakli", "lastName" => "Nadareishvili"))
      ->post("/somepath");

    $this->assertEquals(true, true,
      "Test1 (Indicating Content-Type: application/x-www-form-urlencoded in HTTP POST is allowed");

    $this->request->header('Content-Type', 'multipart/form-data') // This is valid
      ->data(array("firstName" => "irakli", "lastName" => "Nadareishvili"))
      ->post("/somepath");

    $this->assertEquals(true, true,
      "Test1 (Indicating Content-Type: multipart/form-data in HTTP POST is allowed");

    try {
      $this->request->header('Content-Type', 'application/json') // This is invalid
        ->data(array("firstName" => "irakli", "lastName" => "Nadareishvili"))
        ->post("/somepath");
    } catch (RestAgentException $ex) {
      $this->assertTrue(true);
      return;
    }

    $this->fail('Setting a bogus Content-Type should not have passed.');

  }
  
  public function test_post_raw_body() {

    $json = '{"name" : "irakli", "lastname" : "nadareishvili"}';
        
    $http_response = $this->request
      ->body($json)
      ->header("X-API-Key", "aabbccdd")
      ->header(array("foo" => "bar", "one" => "two"))
      ->post("/somepath");

    $response = array();
    eval('$response = ' . $http_response['data'] . ";");

    $this->assertEquals($response['_RAW_HTTP_DATA'], $json,
        "Test of correct method transmitted for HTTP POST");  
        
    try {
      $this->request
        ->body($json)
        ->data(array("firstName" => "irakli", "lastName" => "Nadareishvili")) // this is invalid after ->body()
        ->post("/somepath");
    } catch (RestAgentException $ex) {
      $this->assertTrue(true);
      return;
    }
    
    $this->fail('You should not be able to call data() after setting body with body().');  
        
  }


  public function test_put() {

    $http_response = $this->request
      ->data(array("firstName" => "irakli", "lastName" => "Nadareishvili"))
      ->data("hobby", "programming")
      ->header("X-API-Key", "aabbccdd")
      ->header(array("foo" => "bar", "one" => "two"))
      ->put("/somepath");

    $response = array();
    eval('$response = ' . $http_response['data'] . ";");

    $this->assertEquals("PUT", $response['server']['REQUEST_METHOD'],
      "Test of correct method transmitted for HTTP PUT");

    $data = $response['PARSED_HTTP_DATA'];
    $data_vars_correct = ($data['firstName'] == 'irakli' &&
      $data['lastName'] == 'Nadareishvili' &&
      $data['hobby'] == 'programming');

    $this->assertEquals(true, $data_vars_correct,
      "Test of data() functioning properly for HTTP PUT");

    $this->assertEquals("application/x-www-form-urlencoded", $response['server']['CONTENT_TYPE'],
      "Test1 (content-type) of header() functioning properly for HTTP PUT");

    $this->assertEquals("bar", $response['server']['HTTP_FOO'],
      "Test2 (custom headers, passed as array) of header() functioning properly for HTTP PUT");

  }

  public function test_delete() {

    $http_response = $this->request
      ->data(array("firstName" => "irakli", "lastName" => "Nadareishvili"))
      ->data("hobby", "programming")
      ->header("X-API-Key", "aabbccdd")
      ->header(array("foo" => "bar", "User-Agent" => "CERN-LineMode/2.15 libwww/2.17b3"))
      ->delete("/somepath");

    $response = array();
    eval('$response = ' . $http_response['data'] . ";");

    $this->assertEquals("DELETE", $response['server']['REQUEST_METHOD'],
      "Test of correct method transmitted for HTTP DELETE");

    $data = $response['PARSED_HTTP_DATA'];
    $data_vars_correct = ($data['firstName'] == 'irakli' &&
                          $data['lastName'] == 'Nadareishvili' &&
                          $data['hobby'] == 'programming');

    $this->assertEquals(true, $data_vars_correct,
      "Test of data() functioning properly for HTTP DELETE");

    $this->assertEquals("/somepath", $response['server']['REQUEST_URI'],
      "Test of request_uri functioning properly for HTTP DELETE");

    $this->assertEquals("application/x-www-form-urlencoded", $response['server']['CONTENT_TYPE'],
      "Test1 (content-type) of header() functioning properly for HTTP DELETE");

    $this->assertEquals("CERN-LineMode/2.15 libwww/2.17b3", $response['server']['HTTP_USER_AGENT'],
      "Test2 (custom headers, passed as array) of header() functioning properly for HTTP DELETE");

  }

  public function test_send() {

    //-- Using "PATCH" as a custom method

    $http_response = $this->request
      ->data(array("firstName" => "irakli", "lastName" => "Nadareishvili"))
      ->data("hobby", "programming")
      ->header("X-API-Key", "aabbccdd")
      ->header(array("foo" => "bar", "User-Agent" => "CERN-LineMode/2.15 libwww/2.17b3"))
      ->param("active", 1)
      ->param(array("param1" => "foo", "param2" => "bar"))
      ->method("PATCH")
      ->timeout(1500)
      ->send("/somepath");

    $response = array();
    eval('$response = ' . $http_response['data'] . ";");

    $this->assertEquals("PATCH", $response['server']['REQUEST_METHOD'],
      "Test of correct method transmitted for HTTP PATCH  when using send()");

    $data = $response['PARSED_HTTP_DATA'];
    $data_vars_correct = ($data['firstName'] == 'irakli' &&
      $data['lastName'] == 'Nadareishvili' &&
      $data['hobby'] == 'programming');

    $this->assertEquals(true, $data_vars_correct,
      "Test of data() functioning properly for HTTP PATCH issued via send()");

    $get = $response['get'];
    $get_vars_correct = ($get['active'] == 1 &&
      $get['param1'] == 'foo' &&
      $get['param2'] == 'bar');

    $this->assertEquals(true, $get_vars_correct,
      "Test of param() functioning properly for HTTP PATCH");

    $this->assertEquals("/somepath?active=1&param1=foo&param2=bar", $response['server']['REQUEST_URI'],
      "Test of request_uri functioning properly for HTTP PATCH");

    $this->assertEquals("application/x-www-form-urlencoded", $response['server']['CONTENT_TYPE'],
      "Test1 (content-type) of header() functioning properly for HTTP PATCH");

    $this->assertEquals("CERN-LineMode/2.15 libwww/2.17b3", $response['server']['HTTP_USER_AGENT'],
      "Test2 (custom headers, passed as array) of header() functioning properly for HTTP PATCH");

    try {
      $this->request->header('Content-Type', 'application/json') // This is invalid
        ->data(array("firstName" => "irakli", "lastName" => "Nadareishvili"))
        ->send("/somepath");
    } catch (RestAgentException $ex) {
      $this->assertTrue(true);
      return;
    }

    $this->fail('You should not be able to call send() without sending method with method() first.');


  }

  public function test_send_denies_head_method() {

    try {
      $this->request
        ->data(array("firstName" => "irakli", "lastName" => "Nadareishvili"))
        ->data("hobby", "programming")
        ->header("X-API-Key", "aabbccdd")
        ->param(array("param1" => "foo", "param2" => "bar"))
        ->method("HEAD")
        ->send("/somepath");
    } catch (RestAgentException $ex) {
      $this->assertTrue(true);
      return;
    }

    $this->fail('You should not be able to call send() with HEAD as a method.');
  }

  public function test_head() {

    $response = $this->request
      ->data(array("firstName" => "irakli", "lastName" => "Nadareishvili"))
      ->data("hobby", "programming")
      ->header("X-API-Key", "aabbccdd")
      ->header(array("foo" => "bar", "User-Agent" => "CERN-LineMode/2.15 libwww/2.17b3"))
      ->head("/somepath");

    $this->assertEquals($response['data']['Content-Type'],"text/plain",
      "Test of Content-type header for HTTP HEAD");

    $this->assertEquals($response['data']['Some-Foo-Header'],"to/check/with/head/request",
      "Test of Some-Foo-Header for HTTP HEAD ");

  }

}