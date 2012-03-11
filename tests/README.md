# Make sure you have the latest PEAR PHPUnit installed:
  * sudo pear channel-discover pear.phpunit.de
  * sudo pear channel-discover pear.symfony-project.com
  * sudo pear channel-discover components.ez.no
  * sudo pear update-channels
  * sudo pear upgrade-all
  * sudo pear install --alldeps phpunit/PHPUnit

# Set up a proper virtualhost for testing

In Apache, NginX, etc. (choose your poison) set up a virtualhost so that it points to
test http controller under: tests/server.php and can  process requests to
http://restagent.vm:8080/ (or  modify the value of the base url in the phpunit.xml file)

For instance, for Nginx:
<pre>
... snippet ...
server {
    listen   8080;
    server_name  restagent.vm;
    root /path/to/restagent/code/tests;

    index  index.php server.php;

location / {
      if (!-e $request_filename) {
        rewrite ^/(.*)$ /server.php?q=$1 last;
      }
}
...
</pre>

# Run all tests with:
  * cd tests
  * phpunit .
