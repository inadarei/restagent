# 1. Make sure you have the latest PEAR PHPUnit installed:

Run following commands from a shell:

```
sudo pear channel-discover pear.phpunit.de
sudo pear channel-discover pear.symfony-project.com
sudo pear channel-discover components.ez.no
sudo pear update-channels
sudo pear upgrade-all
sudo pear install --alldeps phpunit/PHPUnit
```

## 2. Set up a testing URL

First, we'll set up a new custom domain so as not to conflict with any pre-existing servers.

```
$ sudo sh -c "echo '\n127.0.0.1  restagent.vm' >> /etc/hosts"
```

Now, if you're using PHP 5.4 or higher, you can simply run the built-in webserver like so and skip to (3): 
```
php -S restagent.vm:8080 -t /path/to/restagent/tests
```
More information is available on [php.net](http://php.net/manual/en/features.commandline.webserver.php).

Otherwise, if you prefer using Apache, Nginx, etc. (pick your poison), you'll need to set up a virtualhost 
so that it points to the Restagent test router in `/path/to/restagent/tests/index.php` and can process requests to 
`http://restagent.vm:8080`. If you prefer to use a different URL, simply modify the value of `server_url` 
in `/path/to/restagent/tests/phpunit.xml`.

For instance, for Nginx:
```
server {
    listen   8080;
    server_name  restagent.vm;
    root /path/to/restagent/tests/;

    index index.php;

    #  php file handling
    location ~ \.php$ {
      fastcgi_pass   127.0.0.1:9000;
      fastcgi_index  index.php;

      include fastcgi_params;
      fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
      fastcgi_param SERVER_NAME $http_host;
      fastcgi_ignore_client_abort on;
    }

    location / {
        try_files  $uri $uri/ /index.php?q=$uri&$args;
        index      index.php;
    }

    gzip on;
    gzip_comp_level 2;
    gzip_proxied any;
    gzip_min_length  1000;
    gzip_disable     "MSIE [1-6]\."
    gzip_types text/plain text/css application/json application/x-javascript text/xml application/xml application/xml+rss text/javascript;
}
```

## 3. Run the tests
```
$ cd /path/to/restagent/tests
$ phpunit . 
```