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

Little more drama, please:

    $response = $request->set('Content-Type', 'application/json')
                        ->set("X-API-Key", "aabbccdd")
                        ->get("http://example.com/user/1");

Assemble and send an HTTP POST:

    $response = $request
                    ->set("X-API-Key", "aabbccdd")
                    ->add(array("firstName" => "Irakli", "lastName" => "Nadareishvili"))
                    ->add("hobby", "programming")
                    ->set(array("User-Agent" => "RestAgent/1.0 php/libcurl", "foo" => "bar"))
                    ->param("active", 1)
                    ->post("http://example.com/user");

Using custom HTTP method and setting a custom timeout:

    $response = $request
                    ->add(array("firstName" => "Irakli", "lastName" => "Nadareishvili"))
                    ->set("X-API-Key", "aabbccdd")
                    ->set(array("User-Agent" => "CERN-LineMode/2.15 libwww/2.17b3"))
                    ->method("PATCH")
                    ->timeout(500)
                    ->send("/user/1");


Where:

* set() sets an HTTP header
* add() sets a variable (query parameter in case of HTTP GET, or data variable in case of POST or PUT).
* param() allows setting query parameters for non-HTTP GET calls (i.e. when add() would set passed variables in request
body rather than URL). **Caution**: Do not use param() with HTTP GET or you will get an exception. Use add() instead!
* head(), get(), post(), put() and delete() issue corresponding HTTP request.
* method() sets a custom HTTP method to be used in conjuction with a send() call.
* timeout() overrides the default timeout to a specified number of milliseconds.

Please note that add(), param() and set() methods take either an array or a single name/value pair as an argument.
Why? Because either can be convenient, in different cases.

Furthermore, methods like ->get() and ->post() are just convenience shortcuts on calling ->method("get")->send(...);
If you want to use any HTTP methods not included in the above list, try issuing ->method(...)->send();

### Why param(), again?

The purpose of the param() method can be confusing and may need further explanation. 

In the puristic RESTful view, we want to have a uniform way of making HTTP calls and pass variables as we
do it. Therefore it's important, as a general rule, to be able to set request data with a uniform add() method. After 
which  we can make appropriate HTTP-verb call (whether it's GET or PUT or whatever else), without having to worry 
about the specifics of the verb's way of encoding data (in the URL or HTTP Body).

That said, sometimes we do need to add HTTP query params even during an HTTP call that encodes variables in an HTTP
body (e.g. POST or PUT). Whether we consider it RESTful or not, it's allowed in HTTP and can be a necessary
"evil" sometimes. That is why param() exists: to allow for such use-cases.

However, we do not allow using param() with HTTP GET, because add() method already does what param() would do and it would
be confusing to get in the business of deciding which method gets priority or how variables are merged if somebody
decides to set the same variable through both param() and add().

That is why an attempt to use param() during an HTTP GET call results in an exception, the exception basically
indicates: "we gave you a way to shortcut the system during POST, PUT etc, now don't be asinine and don't try to
use it for GET, where you really should not be using it".

So that's the very long story of param() and its relation to add()...

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
