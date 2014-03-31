OpenSearchServer PHP Client
===========================

This API connector is intended to be used with PHP 5 (any version >= 5.3) and [Composer](http://getcomposer.org/).
It is based on the v1 API of OpenSearchServer.

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

### How to install it

Add the repository information in your `composer.json` file:
``` 
"repositories": [
  {
    "type": "git",
    "url": "https://github.com/AlexandreToyer/opensearchserver-php-client"
  }
],
```

Add the require information in your `composer.json` file:
```
"opensearchserver/opensearchserver": "~1.4-dev"
``` 

Run `composer install` (or `php composer.phar install`).

### How to use it

Create an OSS_API instance:
```php
$oss_url = 'http://localhost:8080';
$oss_index = 'my_index';
$oss_login = 'my_login';
$oss_key = '54a51de4f27cefbcb7a771335b980567f'
$oss_api = new \Opensearchserver\OssApi($oss_url, $oss_index, $oss_login, $oss_key);
```

To make a search, we need an OssSearch instance:
```php
$oss_search = $oss_api->search();
```

Searching the keyword "open", using the search template called "search", using a time-out of 60 seconds:
```php
$xmlResult = $oss_search->query('open')
    ->template('search')
    ->execute(60);
```

Get the number of documents found (using OssResults class):
```php
$oss_result = new \Opensearchserver\OssResults($xmlResult);
$doc_found_number = $oss_result->getResultFound() - $oss_result->getResultCollapsedCount();
print 'Documents found: '.$doc_found_number."\n";
```

Iterate over the documents and print the title:
```php
for ($i = 0; $i < $doc_found_number; $i++) {
  $title = $oss_result->getField($i, 'title');
  print '#'.$i.' '.$title."\n";
}
```
