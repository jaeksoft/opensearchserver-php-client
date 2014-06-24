OpenSearchServer PHP Client
======================================
# For V2 API

**Warning:** this PHP client is still under heavy development.

This API connector is intended to be used with PHP 5 (any version >= 5.3) and [Composer](http://getcomposer.org/).
It is based on the V2 API of OpenSearchServer.

You can find more about the OSS API on the OSS WiKi
http://www.open-search-server.com/documentation

# How to test this development version

* Create a folder for this project

```shell
mkdir ossphp_sandbox
cd ossphp_sandbox
```

* In this folder write these lines in a file named `composer.json`:

```json
{
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/jaeksoft/opensearchserver-php-client"
        }
    ],
    "require": {
        "opensearchserver/opensearchserver": "~3.0-dev"
    }
}   
```

* Run these commands to install vendors:

```shell
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

* Create a folder where example code can be written:

```shell
mkdir web
cd web
echo "<?php include_once '../vendor/autoload.php';" > index.php
```

* Code can now be written in file `web/index.php`. Take examples from `vendor/opensearchserver/opensearchserver/examples/oss_examples.php`.

# How to use this client

Create a global handler:

```php
$url        = 'http://localhost:9090';
$app_key    = 'xxxxxxxxxxxxxxxxxx';
$login      = 'admin';
$oss_api = new OpenSearchServer\Handler(array('url'=>$url, 'key' => $app_key, 'login' => $login ));
```

Create a new object for each Request and submit it with this handler. `submit()` returns an `OpenSearchServer\Response` object.

For example:

## Create an index

```php
$request = new OpenSearchServer\Index\Create();
$request->index('00__test_file')->template(OpenSearchServer\Request::TEMPLATE_FILE_CRAWLER);
$response = $oss_api->submit($request);
```

## Index documents

```php
$request = new OpenSearchServer\Document\Put();
$request->index('00__test_file');

$document = new OpenSearchServer\Document\Document();
$document   ->lang(OpenSearchServer\Request::LANG_FR)
            ->field('title','Test The Count 2')
            ->field('autocomplete','Test The Count 2')
            ->field('uri', '2');
$document2 = new OpenSearchServer\Document\Document();
$document2  ->lang(OpenSearchServer\Request::LANG_FR)
            ->field('title','Test The Count 3')
            ->field('autocomplete','Test The Count 3')
            ->field('uri', '3');
$request->addDocuments(array($document, $document2));
$response = $oss_api->submit($request);
```

# Full API reference

**TODO**

# TODO

* Factory to work with response and get easy access to different type of results (loop through search results, ...)

===========================

OpenSearchServer PHP Client
Copyright 2008-2013 Emmanuel Keller / Jaeksoft
http://www.open-search-server.com

OpenSearchServer PHP Client is free software: you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
 
OpenSearchServer PHP Client is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.
 
You should have received a copy of the GNU Lesser General Public License
along with OpenSearchServer PHP Client.
If not, see <http://www.gnu.org/licenses/>.
 