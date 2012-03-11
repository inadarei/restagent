<?php

namespace restagent;

/**
 *
 * Simple HTTP REST Client.
 * For full disclosure this is a shameless rip-off from: https://github.com/inadarei/settee/blob/master/src/classes/ZaphpaRestClient.class.php
 * That is: if you can call "stealing" from one's self "shameless" :)
 *
 * @TODO support proxying using: CURLOPT_HTTPPROXYTUNNEL
 *
 */
class Request {

  /**
   * HTTP Timeout in Milliseconds
   */
  private $timeout =  2000;
  private $base_url;
  private $data = array();
  private $headers = array();
  private $curl;

  /**
   * Public constructor
   *
   * @param null $base_url
   */
  public function __construct($base_url = '') {
    $this->base_url = (!empty($base_url)) ? rtrim($base_url, "/") : '';

    $this->curl = curl_init();
    curl_setopt($this->curl, CURLOPT_USERAGENT, "RestAgent/1.0");
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($this->curl, CURLOPT_HEADER, 0);
    curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($this->curl, CURLOPT_TIMEOUT_MS, $this->timeout);
    curl_setopt($this->curl, CURLOPT_FORBID_REUSE, false); // Connection-pool for CURL

  }
  /**
   * Class destructor cleans up any resources
   */
  function __destruct() {
    curl_close($this->curl);
  }

  /**
   * HTTP HEAD
   *
   * @return
   *     Raw HTTP Headers of the response.
   *
   * @see: http://www.php.net/manual/en/context.params.php
   *
   */
  function head($uri) {
    curl_setopt($this->curl, CURLOPT_HEADER, 1);

    $full_url = $this->get_full_url($uri);
    curl_setopt($this->curl, CURLOPT_URL, $full_url);
    curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'HEAD');
    curl_setopt($this->curl, CURLOPT_NOBODY, true);


    $response = curl_exec($this->curl);
    // Restore default values
    curl_setopt($this->curl, CURLOPT_NOBODY, false);
    curl_setopt($this->curl, CURLOPT_HEADER, false);

    $resp_code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

    if (function_exists('http_parse_headers')) {
      $headers = http_parse_headers($response);
    }
    else {
      $headers = $this->_http_parse_headers($response);
    }

    return $headers;
  }

  /**
   * Backup PHP impl. for when PECL http_parse_headers() function is not available
   *
   * @param  $header
   * @return array
   * @source http://www.php.net/manual/en/function.http-parse-headers.php#77241
   */
  private function _http_parse_headers( $header ) {
    $retVal = array();
    $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
    foreach( $fields as $field ) {
      if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
        $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
        if( isset($retVal[$match[1]]) ) {
          $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
        } else {
          $retVal[$match[1]] = trim($match[2]);
        }
      }
    }
    return $retVal;
  }

  /**
   * HTTP GET
   */
  function get($uri) {
    $data = (is_array($this->data)) ? http_build_query($this->data) : null;
    if (!empty($data)) {
      $uri .= "?$data";
    }
    return $this->http_request('GET', $uri);
  }

  /**
   * HTTP PUT
   */
  function put($uri) {
    return $this->http_request('PUT', $uri, $this->data);
  }

  /**
   * HTTP POST
   */
  function post($uri) {
    return $this->http_request('POST', $uri, $this->data);
  }

  /**
   * HTTP DELETE
   */
  function delete($uri, $data = array()) {
    return $this->http_request('DELETE', $uri, $data);
  }

  /**
   * Generic implementation of a HTTP Request.
   *
   * @param $http_method
   * @param  $uri
   * @param array $data
   * @return
   *  an array containing json and decoded versions of the response.
   */
  private function http_request($http_method, $uri, $data = array()) {
    $data = (!empty($data) && is_array($data)) ? http_build_query($data) : '';

    if (!empty($data)) {
      $this->set('Content-Length', strlen($data));
      curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
    }

    if ($http_method == 'POST' && isset($this->headers['Content-Type'])) {
      unset($this->headers['Content-Type']);
    }

    // $this->headers is an associative array, to allow for overrides in set(), but
    // curl_setopt() takes indexed array, so we need to convert.
    $idxed_headers = array();
    foreach ($this->headers as $name => $value) {
      $idxed_headers[] = "$name: $value";
    }

    curl_setopt($this->curl, CURLOPT_HTTPHEADER, $idxed_headers);

    $full_url = $this->get_full_url($uri);
    curl_setopt($this->curl, CURLOPT_URL, $full_url);
    curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $http_method);

    $response = curl_exec($this->curl);
    //$this->check_status($response, $full_url);

    return $response;
  }

  /**
   * Get full URL from a partial one
   */
  private function get_full_url($uri) {
    // We do not want "/", "?", "&" and "=" separators to be encoded!!!
    $uri = str_replace(array('%2F', '%3F', '%3D', '%26'), array('/', '?', '=', '&'), urlencode($uri));
    return $this->base_url . $uri;
  }

  /**
   * Set an HTTP Head
   */
  public function set() {
    if (func_num_args() == 1) {
      $args = func_get_arg(0);
      if (!is_array($args)) {
        throw new RestAgentException("If you only pass one argument to set() it must be an array");
      }

      foreach ($args as $name => $value) {
        $this->headers[$name] = $value;
      }
      return $this;
    }

    if (func_num_args() == 2) {
      $name = func_get_arg(0);
      $value = func_get_arg(1);
      if (!is_string($name) || !(is_string($value) || is_numeric($value) || is_bool($value))) {
        throw new RestAgentException("If you only pass two arguments to set(), first one must be a string and the second
                                      one must be: a string, a number, or a boolean");
      }
      $this->headers[$name] = $value;
      return $this;
    }

    throw new RestAgentException("set() method only accepts either one or two arguments");
  }

  /**
   * Set a variable (query param or a data var)
   */
  public function add() {
    if (func_num_args() == 1) {
      $args = func_get_arg(0);
      if (!is_array($args)) {
        throw new RestAgentException("If you only pass one argument to add() it must be an array");
      }

      foreach ($args as $name => $value) {
        $this->data[$name] = $value;
      }
      return $this;
    }

    if (func_num_args() == 2) {
      $name = func_get_arg(0);
      $value = func_get_arg(1);
      if (!is_string($name) || !(is_string($value) || is_numeric($value) || is_bool($value))) {
        throw new RestAgentException("If you only pass two arguments to set(), first one must be a string and the second
                                      one must be: a string, a number, or a boolean");
      }
      $this->data[$name] = $value;
      return $this;
    }

    throw new RestAgentException("add() method only accepts either one or two arguments");
  }

  /**
   * Check http status for safe return codes
   *
   * @throws RestAgentException
   */
  private function check_status($response, $full_url) {
    $resp_code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

    if ($resp_code < 199 || $resp_code > 399 || !empty($response['decoded']->error)) {
      $msg = "Server returned: \"HTTP 1.1. $resp_code\" \nURL: $full_url \nERROR: " . $response['json'];
      throw new RestAgentException($msg);
    }
  }

  /**
   * @param  $path
   *    Full path to a file (e.g. as returned by PHP's realpath function).
   * @return void
   */
  public function file_mime_type ($path)  {
    $ftype = 'application/octet-stream';

    if (function_exists("finfo_file")) {
      $finfo = new finfo(FILEINFO_MIME_TYPE | FILEINFO_SYMLINK);
      $fres = $finfo->file($path);
      if (is_string($fres) && !empty($fres)) {
        $ftype = $fres;
      }
    }

    return $ftype;
  }

  /**
   * @param  $content
   *    content of a file in a string buffer format.
   * @return void
   */
  public function content_mime_type ($content)  {
    $ftype = 'application/octet-stream';

    if (function_exists("finfo_file")) {
      $finfo = new finfo(FILEINFO_MIME_TYPE | FILEINFO_SYMLINK);
      $fres = $finfo->buffer($content);
      if (is_string($fres) && !empty($fres)) {
        $ftype = $fres;
      }
    }

    return $ftype;
  }

}

class RestAgentException extends \Exception {}


