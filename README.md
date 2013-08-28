RestAgent is an HTTP client library for PHP. Project's primary goals are: 

* Elegant and intuitive API. 
* Minimize boilerplate to make working with HTTP from PHP enjoyable.
* Provide rich set of functionality for RESTful interactions.

RestAgent's API is inspired by the simplicity of the API in [SuperAgent](https://github.com/visionmedia/superagent) library for Node.js by TJ Holowaychuk.

## Compatibility

PHP 5.3 or newer.

## Quick Docs:

### Issuing a Request

Issue a simple HTTP GET:

    require_once('/path/to/restagent/restagent.lib.php');
    $request = new \restagent\Request;
    
    $response = $request->get("http://example.com/user/1");

Setting base URL for requests:

    require_once('/path/to/restagent/restagent.lib.php');
    $request = new \restagent\Request('http://restagent.vm:8080/api/');
    
    $response = $request->get("/user/1");

Little more drama, please:

    $response = $request->header('Content-Type', 'application/json')
                        ->header("X-API-Key", "aabbccdd")
                        ->get("http://example.com/user/1");

Assemble and send an HTTP POST:

    $response = $request
                    ->header("X-API-Key", "aabbccdd")
                    ->data(array("firstName" => "Irakli", "lastName" => "Nadareishvili"))
                    ->data("hobby", "programming")
                    ->header(array("User-Agent" => "RestAgent/1.0 php/libcurl", "foo" => "bar"))
                    ->param("active", 1)
                    ->post("http://example.com/user");

An HTTP PUT with raw data in the HTTP BODY:

    $json = '{"name" : "irakli", "lastname" : "nadareishvili"}';
    $response = $request
                    ->header("X-API-Key", "aabbccdd")
                    ->body($json)
                    ->header(array("User-Agent" => "RestAgent/1.0 php/libcurl", "foo" => "bar"))
                    ->put("http://example.com/user");

Using custom HTTP method and setting a custom timeout:

    $response = $request
                    ->data(array("firstName" => "Irakli", "lastName" => "Nadareishvili"))
                    ->header("X-API-Key", "aabbccdd")
                    ->header(array("User-Agent" => "CERN-LineMode/2.15 libwww/2.17b3"))
                    ->method("PATCH")
                    ->timeout(500)
                    ->send("/user/1");


Where:

* header() sets an HTTP header
* data() sets data variable(s) to be passed during the request. It can be a query parameter in case of an HTTP GET, or
variables passed as part of HTTP body in case of HTTP POST, PUT etc.
* param() allows setting query parameters for non-HTTP GET calls (i.e. when data() would set passed variables in request
body rather than the URL). **Caution**: Do not use param() with HTTP GET or you will get an exception. Use data() instead!
More about this below.
* head(), get(), post(), put() and delete() calls issue a corresponding HTTP request.
* method() sets a custom HTTP method to be used in conjunction with a send() call.
* timeout() overrides the default timeout to a specified number of milliseconds.

Please note that data(), param() and header() methods take either an array or a single name/value pair as an argument.
Why? Because either can be convenient, in different cases.

Furthermore, methods like ->get() and ->post() are just convenience shortcuts on calling ->method("get")->send(...);
If you want to use any HTTP methods not included in the above list, try issuing ->method(...)->send();

### Why param(), again?

The purpose of the param() method can be confusing and may need further explanation. 

In the puristic RESTful view, we want to have a uniform way of making HTTP calls and pass variables as we
do it. Therefore it's important, as a general rule, to be able to set request data with a uniform data() method. After 
which  we can make appropriate HTTP-verb call (whether it's a GET or a PUT or whatever else), without having to worry
about the specifics of the verb's way of encoding data (in the URL or HTTP Body).

That said, sometimes we do need to add HTTP query params even during an HTTP call that encodes variables in an HTTP
body (e.g. POST or PUT). Whether we consider it RESTful or not, it's allowed in HTTP and can be a necessary
"evil" sometimes. That is why param() exists: to allow for such use-cases.

However, we do not allow using param() with HTTP GET, because data() method already does what param() would do and it would
be confusing to get in the business of deciding which method gets priority or how variables are merged if somebody
decides to set the same variable through both param() and data().

That is why an attempt to use param() during an HTTP GET call results in an exception, the exception basically
indicates: "we gave you a way to shortcut the system during POST, PUT etc, now don't be asinine and don't try to
use it for GET, where you really should not be using it".

So that's the very long story of param() and its relation to data()...

### Response Format

The head(), get(), post(), put(), delete() and send() calls return an associative array that has the following structure:

* code: http response code.
* meta: a whole bunch of meta-information about the call from CURL
* data: response content for any http method except HTTP HEAD. The latter, by definition, does not return any content
so 'data' contains parsed http response headers, instead.


## License

(The MIT License)

Copyright (c) 2012 Irakli Nadareishvili

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
'Software'), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
