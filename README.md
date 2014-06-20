OpenSearchServer PHP Client for V2 API
======================================

**Warning:** this PHP client is still under heavy development.

This API connector is intended to be used with PHP 5 (any version >= 5.3) and [Composer](http://getcomposer.org/).
It is based on the V2 API of OpenSearchServer.

You can find more about the OSS API on the OSS WiKi
http://www.open-search-server.com/documentation

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
            "url": "https://github.com/AlexandreToyer/opensearchserver-php-client"
        }
    ],
    "require": {
        "php": ">=5.3.3",
        "kriswallsmith/buzz": ">=0.6",
        "symfony/options-resolver": ">=2.1",
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
echo '<?php include_once '../vendor/autoload.php';' > index.php
```

* Code can now be written in file `web/index.php`. Take examples from `vendor/opensearchserver/opensearchserver/examples/oss_examples.php`.