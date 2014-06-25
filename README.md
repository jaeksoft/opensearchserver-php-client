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

## How to make requests

In this PHP client requests to OpenSearchServer's API are objects. Each request object must be submitted to a global handler that is in charge of sending them to an OpenSearchServer instance and returning a response.

### Create an handler

```php
$url        = '<instance URL>';
$app_key    = '<API key>';
$login      = '<login>';
$oss_api = new OpenSearchServer\Handler(array('url' => $url, 'key' => $app_key, 'login' => $login));
```

### Create a request

Several types of objects are available, each being a mapping to one API. For instance objects of type `OpenSearchServer\Index\Create` will be used to create index and objects of type `OpenSearchServer\Search\Field\Search` will be used to search for documents.

Each request object is a child of the abstract parent class `OpenSearchServer\Request`.

For example here is the code to create a request that wil create an index when sent to an OpenSearchServer instance:
 
```php
$request = new OpenSearchServer\Index\Create();
```

After being created each type of request must be configured in a particular way, depending on its type, by calling some methods.

For example this code will tell OpenSearchServer to name the new index "first_index":

```php
$request->index('first_index');
```

**Important note:** 

* method `index()` is really important and is shared by almost every type of requests. In the case of index creation it serves to give a name to new index and in almost every other request it will be used to configure the index on which API call must be made.

> This method will not be documented further but will be displayed in code examples when needed.

Once configured request must be sent to an OpenSearchServer instance thanks to the handler created before:

```php
$response = $oss_api->submit($request);
```


## Work with index

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

###  Get list of index on an instance

```php
$request = new OpenSearchServer\Index\GetList();
$response = $oss_api->submit($request);
```

> This class does not need a call to `->index()` before submission.

## Configure a schema

In OpenSearchServer each index must have a schema. A schema is a list of fields, each with some properties.

### Create a field

```php
$request = new OpenSearchServer\Field\Create();
$request->index('index_name')
        ->name('titleStandard')
        ->indexed(true)
        ->analyzer('StandardAnalyzer')
        ->stored(true)
        ->copyOf('title');
$response = $oss_api->submit($request);
```

Available methods:

* **name(string $name)**: name of field to create.
* **indexed(boolean $indexed)**: tells whether or not this field must be indexed. Indexed field can then used in full-text searchs. 
* **analyzer(string $analyzerName)**: analyzer to use on this field. Analyzer allow to apply several transformations on indexed or searched data.
* **stored(boolean $stored)**: tells whether or not this field must be stored. Stored field can return their original values in search queries, even if some Analyzers transformed it.
* **copyOf(string/array $fields)**: field(s) from which copy value. Value is copied before transformation by analyzers. A string or an array of string can be given to this method.

### Get list of fields

```php
$request = new OpenSearchServer\Field\GetList();
$request->index('index_name');
$response = $oss_api->submit($request); 
```

### Get details of a specific field

```php
$request = new OpenSearchServer\Field\Get();
$request->index('index_name')
        ->name('titleStandard');
$response = $oss_api->submit($request);
```

Available methods:

* **name(string $name)**: name of field to get information for.

### Delete a field

```php
$request = new OpenSearchServer\Field\Delete();
$request->index('index_name')
        ->name('titleStandard');
$response = $oss_api->submit($request);
```

Available methods:

* **name(string $name)**: name of field to delete.

### Set default and unique field for an index

```php
$request = new OpenSearchServer\Field\SetDefaultUnique();
$request->index('index_name')
        ->defaultField('title')
        //remove unique field for this index
        ->uniqueField();
$response = $oss_api->submit($request);
```

Available methods:
* **defaultField(string $name)**: name of field that must be used as default field. Default field is used for search queries when no particular field is configured in the query. Empty value removes default field setting.
* **uniqueField(string $name)**:  name of field that must be used as unique field. Unique field is used as a primary key. Empty value removes unique field setting.

## Web Crawler

### Patterns

Available methods for Insert and Delete classes:

* **pattern(string $pattern)**: URL to insert or delete
* **patterns(array $patterns)**: array of URL to insert or delete

#### Insert inclusion patterns

```php
$request = new OpenSearchServer\Crawler\Web\Patterns\Inclusion\Insert();
$request->index('index_name')
        ->pattern('http://www.website1.com/*')
        ->patterns(array('http://www.cnn.com/sport/*', 'http://www.cbc.com/news/particular-page.html'));
$response = $oss_api->submit($request);
```

#### List inclusion patterns

```php
$request = new OpenSearchServer\Crawler\Web\Patterns\Inclusion\GetList();
$request->index('index_name');
$response = $oss_api->submit($request);
```

#### Delete inclusion patterns

```php
$request = new OpenSearchServer\Crawler\Web\Patterns\Inclusion\Delete();
$request->index('index_name')
        ->pattern('http://www.website1.com/*');
$response = $oss_api->submit($request);
```

#### Insert exclusion patterns

```php
$request = new OpenSearchServer\Crawler\Web\Patterns\Exclusion\Insert();
$request->index('index_name')
        ->pattern('http://www.exclude.com/*')
        ->patterns(array('http://www.exclude1.com/page1', 'http://www.exclude2.net/page1'));
$response = $oss_api->submit($request);
```

#### List exclusion patterns

```php
$request = new OpenSearchServer\Crawler\Web\Patterns\Exclusion\GetList();
$request->index('index_name');
$response = $oss_api->submit($request);
```

#### Delete exclusion patterns

```php
$request = new OpenSearchServer\Crawler\Web\Patterns\Exclusion\Delete();
$request->index('index_name')
        ->pattern('http://www.exclude1.com/page1');
$response = $oss_api->submit($request);
```

### Start crawler

```php
$request = new OpenSearchServer\Crawler\Web\Start();
$request->index('index_name');
$response = $oss_api->submit($request);
```

### Stop crawler

```php
$request = new OpenSearchServer\Crawler\Web\Stop();
$request->index('index_name');
$response = $oss_api->submit($request);
```

### Get crawler status

```php
$request = new OpenSearchServer\Crawler\Web\GetStatus();
$request->index('index_name');
$response = $oss_api->submit($request);
```

## Autocompletion

### Create an autocompletion

Autocompletion are "sub-index" for OpenSearchServer. They need to be created and configured with fields to use for suggestions.

```php
$request = new OpenSearchServer\Autocompletion\Create();
$request->index('00__test_file')
        ->name('autocomplete')
        ->field('autocomplete');
$response = $oss_api->submit($request);
```

Available methods:

* **name(string $name)**: name of autocompletion item to create.
* **field(string $name)**: name of field in main schema from which suggestion are returned.

*TODO*: a bug need to be fixed for this class to be able to set several fields. See https://github.com/jaeksoft/opensearchserver/issues/709.

### Build autocompletion

Autocompletion sub-index need to be re-built frequently, when content on main index changes. This can be automatized with OpenSearchServer's Schedulers or done by calling this API.

```php

$request = new OpenSearchServer\Autocompletion\Build();
$request->index('index_name')
        ->name('autocomplete');
$response = $oss_api->submit($request);
``` 

Available methods:

* **name(string $name)**: name of autocompletion item to build.


### Get list of existing autocompletion items

Several autocompletion items can be built, each with particular fields for some specific purpose.

```php
$request = new OpenSearchServer\Autocompletion\GetList();
$request->index('index_name');
$response = $oss_api->submit($request);
```

### Query autocompletion

```php
$request = new OpenSearchServer\Autocompletion\Query();
$request->index('index_name')
        ->name('autocomplete')
        ->query('Three Musk')
        ->rows(10);
$response = $oss_api->submit($request);
```

Available methods:

* **name(string $name)**: name of autocompletion item to query.
* **query(string $keywords)**: keywords to use for suggestions. Usually beginning of a word.
* **rows(int $numberOfRows)**: number of suggestions to return.

### Delete an autocompletion item

```php
$request = new OpenSearchServer\Autocompletion\Delete();
$request->index('index_name')
        ->name('autocomplete');
$response = $oss_api->submit($request);
```
Available methods:

* **name(string $name)**: name of autocompletion item to delete.

# TODO

* Factory to work with responses and get easy access to different type of results (loop through search results, ...).
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
 