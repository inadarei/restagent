RestAgent is an HTTP client library for PHP. Project's primary goals are: 

* Elegant and intuitive API. 
* Minimize boilerplate to make working with HTTP from PHP enjoyable.
* Providing rich set of functionality for RESTful interactions. 

RestAgent's API is inspired by the simplicity of the API in [SuperAgent](https://github.com/visionmedia/superagent) library for Node.js by TJ Holowaychuk.

## Compatibility

PHP 5.3 or newer.

## Quick Docs:

Issue a simple HTTP GET:

    $request = new \restagent\Request;
    $request->get("http://example.com/user/1", function($response) {
       print_r($response);
       exit();
    });

Assemble and send an HTTP POST:

    $request = new \restagent\Request;
    $request->set('Accept', 'application/json')
            ->add(array("firstName" => "irakli", "lastName" => "Nadareishvili"))
            ->add("hobby", "programming")
            ->set("X-API-Key", "aabbccdd")
            ->set(array("foo" => "bar", "one" => "two")
            ->post("http://example.com/user", 'myCallback');

    function myCallback($response) {
        print_r($response);
        exit();
    }

You can see in the example above that both add(), as well as set() methods take either an array or a single name/value
pair as an argument. Why? Because it is convenient.

Similarly, second argument of any HTTP-verb based methog ("get", "post", "put" or "delete") can take either a closure
function, a callback function's name or an array where first element is an object and second: a method on that object.
Furthermore, methods like ->get() and ->post() are just a convenience shortcut on calling ->method("get")->send(...); If
you want to use any HTTP methods not included in the above list, try issuing ->method(...)->send();

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
