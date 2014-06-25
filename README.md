OpenSearchServer PHP Client
======================================

OpenSearchServer is an Open-Source professionnal search engine offering lots of advanced features:

* Fully integrated solution: build your index, crawl your websites, filesystem or databases, configure your search queries
* Complete user interface in browser
* **Search features:** 
  * **Full-text, boolean** and **phonetic** search
  * Outer and inner **join**
  * Clusters with **faceting** & collapsing
  * **Filtered** search (date, distance)
  * **Geolocation** using square or radius
  * Several **spell-checking** algorithms
  * **Relevance customization**
  * Suggestion (auto-completion)
* **Indexation features:**
  * **17 languages**
  * Special **analysis** for each language
  * Numerous **filters**: n-gram, lemmatization, shingle, elisions, stripping diacritic, Etc.
  * Automatic language detection
  * **Named entity** recognition
  * **Synonyms** (word and multi-terms)
  * Automatic **classifications**
  
Find out all the awesome features offered by OpenSearchServer on our website: http://www.opensearchserver.com/


======================================

**Warning:** this PHP client is still under heavy development.

This API connector is intended to be used with PHP 5 (any version >= 5.3) and [Composer](http://getcomposer.org/).
It is based on the V2 API of OpenSearchServer.


# Setup

* Create a folder for your project

```shell
mkdir ossphp_sandbox
cd ossphp_sandbox
```

* Create a file named `composer.json` with this content:

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

# Quick start

A global handler must be created. It will be used to submit every request to your OpenSearchServer instance:

```php
$url        = '<instance URL>';
$app_key    = '<API key>';
$login      = '<login>';
$oss_api = new OpenSearchServer\Handler(array('url' => $url, 'key' => $app_key, 'login' => $login));
```

Each API request is wrapped in a particular class. Requests must be instanciated, configured and then passed to `$oss_api->submit()` that will return an `OpenSearchServer\Response` object.

**Create an index**

This code creates an index based on our "WEB_CRAWLER" template, which will automatically create a schema allowing to easily crawl a website.

```php
$request = new OpenSearchServer\Index\Create();
$request->index('first_index')->template(OpenSearchServer\Request::TEMPLATE_WEB_CRAWLER);
$response = $oss_api->submit($request);
```
**Configure crawler**

Add some allowed patterns:

```php
$request = new OpenSearchServer\Crawler\Web\Patterns\Inclusion\Insert();
$request->index('first_index')
        ->patterns('http://www.my-website.com/*', 'http://www.your-website.net/*');
$oss_api->submit($request);
```

> Note character `*`: it means crawler will be allowed to follow any URL starting by these patterns.

Add some start URLs for crawler:

```php
$request = new OpenSearchServer\Crawler\Web\Url\Insert();
$request->index('first_index')
        ->urls('http://www.my-website.com', 'http://www.your-website.net');
$oss_api->submit($request);
```

> Here `*` is not used since we real URL are given to crawler: it will use these URLs as first entry points.

Start crawler:

```php
$request = new OpenSearchServer\Crawler\Web\Start();
$request->index('first_index');
$oss_api->submit($request);
```

**Index documents**

While crawler is running some documents can still be manually indexed:

```php
$document = new OpenSearchServer\Document\Document();
$document   ->lang(OpenSearchServer\Request::LANG_FR)
            ->field('title','The Count Of Monte Cristo')
            ->field('url', '1');

$document2 = new OpenSearchServer\Document\Document();
$document2  ->lang(OpenSearchServer\Request::LANG_FR)
            ->field('title','The Three Musketeers')
            ->field('url', '2');

$request = new OpenSearchServer\Document\Put();
$request->index('first_index')
        ->addDocuments(array($document, $document2));
$oss_api->submit($request);
```

> Here objects of type `OpenSearchServer\Document\Document()` are being used to create documents, but some other options exist: passing array, passing JSON, ... See documentation below to know all about this.

**Search:**

It is quite easy then to search for documents:

```php
$request = new OpenSearchServer\Search\Field\Search();
$request->index('first_index')
        ->query('The Count')
        ->searchField('title')
        ->returnedFields('title');
$response = $oss_api->submit($request);
```

# Client Documentation

## Create index

### Create an empty index

```php
$request = new OpenSearchServer\Index\Create();
$request->index('index_name');
$response = $oss_api->submit($request);
```


### Create an index with a template

Two pre-configured templates are offered with OpenSearchServer: WEB_CRAWLER and FILE_CRAWLER. Each template comes with pre-configured schema, queries, renderers.

Template `WEB_CRAWLER`:

```php
$request = new OpenSearchServer\Index\Create();
$request->index('00__test_file')->template(OpenSearchServer\Request::TEMPLATE_WEB_CRAWLER);
$response = $oss_api->submit($request);
```

Template `FILE_CRAWLER`:

```php
$request = new OpenSearchServer\Index\Create();
$request->index('00__test_file')->template(OpenSearchServer\Request::TEMPLATE_FILE_CRAWLER);
$response = $oss_api->submit($request);
```

## Configure a schema

In OpenSearchServer each index must have a schema. A schema is a list of fields, each with some properties.

### Create a field

```php
$request = new OpenSearchServer\Field\Create();
$request->index('index_name')
        ->name('titleStandard')
        ->indexed('YES')
        ->analyzer('StandardAnalyzer')
        ->stored('YES')
        ->copyOf('title');
$response = $oss_api->submit($request);
```

Available methods:

* **name**: name of field to create.
* **indexed**: tells whether or not this field must be indexed. Indexed field can then used in full-text searchs. 
* **analyzer**: analyzer to use on this field. Analyzer allow to apply several transformations on indexed or searched data.
* **stored**: tells whether or not this field must be stored. Stored field can return their original values in search queries, even if some Analyzers transformed it. 

# TODO

* Factory to work with response and get easy access to different type of results (loop through search results, ...).
* Register repository on packagist.

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
 