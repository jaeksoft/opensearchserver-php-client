OpenSearchServer PHP Client
======================================

OpenSearchServer is an Open-Source professionnal search engine offering lots of advanced features:

* **Fully integrated solution**: build your index, **crawl** your websites, filesystem or databases, configure your search queries
* **Complete user interface** in browser
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
  * **Crawlers**: web, filesystem (local, remote), database, mailboxes
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
**Configure web crawler**

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

> Here `*` is not used since real URL are given to crawler: it will use these URLs as first entry points.

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
$results = $oss_api->submit($request);

echo 'Total number of results: ' . $results->getTotalNumberFound() . '<br/>';
echo 'Number of results in this set of results: ' . $results->getNumberOfResults();

foreach($results as $key => $result) {
    echo '<hr/>Result #'.$key.': <br/>';
    echo '<li>Title: '.$result->getField('title').'</li>';
    echo '</ul>';
}  
```

# Client Documentation

**Table of contents**

* **[How to make requests](#how-to-make-requests)**
  * [Create an handler](#create-an-handler)
  * [Create a request](#create-a-request)
  * [Handle response and search results](#handle-response-and-search-results)
    * _[OpenSearchServer\Response\Response](#opensearchserverresponseresponse)_
    * _[OpenSearchServer\Response\ResponseIterable](#opensearchserverresponseresponseiterable)_
    * _[OpenSearchServer\Response\SearchResult](#opensearchserverresponsesearchresult)_
* **[Work with index](#work-with-index)**
  * [Create an empty index](#create-an-empty-index)
  * [Create an index with a template](#create-an-index-with-a-template)
  * [Get list of index on an instance](#get-list-of-index-on-an-instance)
  * [Delete an index](#delete-an-index)
  * [Check if an index exists](#check-if-an-index-exists)
* **[Configure schema](#configure-schema)**
  * [Create a field](#create-a-field)
  * [Get list of fields](#get-list-of-fields)
  * [Get details of a specific field](#get-details-of-a-specific-field)
  * [Delete a field](#delete-a-field)
  * [Set default and unique field for an index](#set-default-and-unique-field-for-an-index)
* **[Web crawler](#web-crawler)**
  * [Patterns](#patterns)
    * _[Insert inclusion patterns](#insert-inclusion-patterns)_
    * _[List inclusion patterns](#list-inclusion-patterns)_
    * _[Delete inclusion patterns](#delete-inclusion-patterns)_
    * _[Insert exclusion patterns](#insert-exclusion-patterns)_
    * _[List exclusion patterns](#list-exclusion-patterns)_
    * _[Delete exclusion patterns](#delete-exclusion-patterns)_
  * [Start crawler](#start-crawler)
  * [Stop crawler](#stop-crawler)
  * [Get crawler status](#get-crawler-status)
* **[REST crawler](#rest-crawler)**
  * [List existing REST crawlers](#list-existing-rest-crawlers)
  * [Execute a REST crawler](#execute-a-rest-crawler)
* **[Autocompletion](#autocompletion)**
  * [Create an autocompletion](#create-an-autocompletion)
  * [Build autocompletion](#build-autocompletion)
  * [Get list of existing autocompletion items](#get-list-of-existing-autocompletion-items)
  * [Query autocompletion](#query-autocompletion)
  * [Delete an autocompletion item](#delete-an-autocompletion-item)
* **[Documents](#documents)**
  * [Push documents](#push-documents)
  * [Delete documents](#delete-documents)
* **[Execute search queries](#run-search-queries)**
  * [Search options](#search-options)
  * [Search(field)](#searchfield)
    * _[Save a Search(field) query template](#save-a-searchfield-query-template)_
  * [Search(pattern)](#searchpattern)
    * _[Save a Search(pattern) query template](#save-a-searchpattern-query-template)_
* **[Search templates](#query-templates)**
  * [List search template](#list-search-templates)
  * [Get details of a search template](#get-details-of-a-search-template)
  * [Delete a search template](#delete-a-search-template)
* **[Synonyms](#synonyms)**
  * [Create a list of synonyms](#create-a-list-of-synoyms)
  * [Check if a list of synonyms exists](#check-if-a-list-of-synonyms-exists)
  * [Get existing lists of synonyms](#get-existing-lists-of-synonyms)
  * [Get synonyms of a list](#get-synonyms-of-a-list)
  * [Delete a list of synonyms](#delete-a-list-of-synonyms)

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

### Handle response and search results

Several types of responses can be returned by `submit()`. Internally this method uses a Factory that builds a response depending on the type of Request given.

3 types of responses are available.

#### OpenSearchServer\Response\Response

Main Response class.

* Methods:
  * **isSuccess():** true if everything went well, false if there was a problem during execution of request.
  * **getInfo():** some requests can return some information. For example `Index deleted: 00__test_file`.
  * **getRawContent():** return raw JSON content of response.
  * **getJsonValues():** return an array of values built from JSON response.
* Example:
  
```php
$request = new OpenSearchServer\Index\Create();
$request->index('index_name');
$response = $oss_api->submit($request);
print_r($response->isSuccess());
```

#### OpenSearchServer\Response\ResponseIterable

Extends OpenSearchServer\Response\Response. Used when response contain iterable values. This class implements `\Iterator` and can thus be used in a loop structure.

* Example: loop through suggestions of an autocompletion query:

```php
$request = new OpenSearchServer\Autocompletion\Query();
$request->index('00__test_file')
        ->name('autocomplete')
        ->query('count of')
        ->rows(10);
$response = $oss_api->submit($request);
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    print_r($item);
}
```

* Requests that use this type of response:
  * OpenSearchServer\Autocompletion\Query
  * OpenSearchServer\Autocompletion\GetList
  * OpenSearchServer\Crawler\Rest\GetList
  * OpenSearchServer\Index\GetList
  * OpenSearchServer\Field\GetList
  * OpenSearchServer\SearchTemplate\GetList
  * OpenSearchServer\Crawler\Web\Patterns\Exclusion\GetList
  * OpenSearchServer\Crawler\Web\Patterns\Inclusion\GetList

#### OpenSearchServer\Response\SearchResult

Extends OpenSearchServer\Response\ResponseIterable. Used for search results.

* Methods:
  * **getResults():** return array of objects of type OpenSearchServer\Response\Result
  * **getFacets():** return an array of facets for this query. Each facet is an array with key being name of field and values being array in the form `<text value> => <number of occurrences>`

Example of array of facets:

```
Array (size=3)
  'host' => 
    array (size=7)
      'http://www.bbc.com' => int 149
      'http://www.facebook.com' => int 47
  'lang' => 
    array (size=2)
      'en' => int 43
      'es' => int 12
  'contentBaseType' => 
    array (size=2)
      'pdf' => int 9
      'text/html' => int 41
```
  
  * **getQuery():** return query executed by OpenSearchServer
  * **getRows():** return number of rows asked
  * **getStart():** return starting offset
  * **getTotalNumberFound():** return total number of results found in index for this query
  * **getTime():** return query duration, in ms
  * **getCollapsedDocCount():** return number of total collapsed docs 
  * **getMaxScore():** return max score in this results set
  * **getNumberOfResults():** return number of results in this results set
* Example: this class being iterable it can also be used in a loop structure:

```php
$request = new OpenSearchServer\Search\Field\Search();
$request->index('index_name')
        ->query('house')
        //using a pre-configured query template
        ->template('search');
$results = $oss_api->submit($request);

echo 'Total number of results: ' . $results->getTotalNumberFound() . '<br/>';
echo 'Number of results in this set of results: ' . $results->getNumberOfResults();

foreach($results as $key => $result) {
    echo '<hr/>Result #'.$key.': <br/>';
    print_r($result);
}
```

This class creates object of type **OpenSearchServer\Response\Result**:
* Methods:
  * **getPos():**
  * **getScore():**
  * **getCollapsedCount():**
  * **getField($fieldName, $returnFirstValueOnly = true):** return value of a field 
    * `$fieldName` is the name of the field to return. `$returnFirstValueOnly` can be set to false to get every values of a multivalued field. Often fields will have only one value, thus default value for this parameter is `true`. 
  * **getSnippet($fieldName, $returnFirstValueOnly = true):** return value of a snippet 
    * `$fieldName` is the name of the field from which the snippet has been created. `$returnFirstValueOnly` can be set to false to get every snippets if several snippets where asked.
  * **getAvailableFields($returnAllWithoutValues = false):** return all available fields for this result.
    *  `$returnAllWithoutValues` can be set to true to return fields that have been configured as `returnedFields` in the query even if they have no value for this result.
  * **getAvailableSnippets($returnAllWithoutValues = false):** return all available snippets for this result.
    *  `$returnAllWithoutValues` can be set to true to return snippets that have been asked the query even if they have no value for this result.
* Example:

```php    
$request = new OpenSearchServer\Search\Field\Search();
$request->index('index_name')
        ->query('house')
        //using a pre-configured query template
        ->template('search');
$results = $oss_api->submit($request);

echo 'Total number of results: ' . $results->getTotalNumberFound() . '<br/>';
echo 'Number of results in this set of results: ' . $results->getNumberOfResults();

foreach($results as $key => $result) {
    echo '<hr/>Result #'.$key.': <br/>';
    echo 'Available fields:</br>- ';
    echo implode('<br/>- ', $result->getAvailableFields());
    echo '<br/>Available snippets:</br>- ';
    echo implode('<br/>- ', $result->getAvailableSnippets());
    echo '<ul>';
    echo '<li>Title:'.$result->getSnippet('title').'</li>';
    echo '<li>Url:'.$result->getField('url').'</li>';
    echo '</ul>';
}    
```
  
## Work with index

### Create an empty index

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/index/create.html)

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

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/index/list.html)

```php
$request = new OpenSearchServer\Index\GetList();
$response = $oss_api->submit($request);
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    print_r($item);
}
```

> This class does not need a call to `->index()` before submission.


### Delete an index

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/index/delete.html)

```php
$request = new OpenSearchServer\Index\Delete();
$request->index('index_name');
$response = $oss_api->submit($request);
```

### Check if an index exists

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/index/exists.html)

```php
$request = new OpenSearchServer\Index\Exists();
$request->index('index_name');
$response = $oss_api->submit($request);
```

## Monitor

Several instance wide monitoring properties can be retrieved:

```php
$request = new OpenSearchServer\Monitor\Monitor();
$request->full();
$response = $oss_api->submit($request);
echo '<ul>';
foreach($response as $propName => $value) {
    echo '<li>'.$propName.': '.$value.'</li>';
}
echo '</ul>';
```

This would display for example:

---
* availableProcessors: 4
* freeMemory: 38656976
* memoryRate: 12.958230285108
* maxMemory: 1879048192
* totalMemory: 298319872
* indexCount: 59
* freeDiskSpace: 24181137408
* freeDiskRate: 23.061161199939
* java.runtime.name: Java(TM) SE Runtime Environment
* sun.boot.library.path: C:\Program Files\Java\jre7\bin
* java.vm.version: 24.51-b03
* java.vm.vendor: Oracle Corporation
* java.vendor.url: http://java.oracle.com/
...
---

## Configure schema

In OpenSearchServer each index must have a schema. A schema is a list of fields, each with some properties.

### Create a field

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/field/create_update.html)

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

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/field/list.html)

```php
$request = new OpenSearchServer\Field\GetList();
$request->index('index_name');
$response = $oss_api->submit($request); 
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    print_r($item);
}
```

### Get details of a specific field

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/field/get.html)

```php
$request = new OpenSearchServer\Field\Get();
$request->index('index_name')
        ->name('titleStandard');
$response = $oss_api->submit($request);
```

Available methods:

* **name(string $name)**: name of field to get information for.

### Delete a field

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/field/delete.html)

```php
$request = new OpenSearchServer\Field\Delete();
$request->index('index_name')
        ->name('titleStandard');
$response = $oss_api->submit($request);
```

Available methods:

* **name(string $name)**: name of field to delete.

### Set default and unique field for an index

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/field/set_default_unique.html)

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

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/WEB_crawler/inclusion_patterns/insert.html)

```php
$request = new OpenSearchServer\Crawler\Web\Patterns\Inclusion\Insert();
$request->index('index_name')
        ->pattern('http://www.website1.com/*')
        ->patterns(array('http://www.cnn.com/sport/*', 'http://www.cbc.com/news/particular-page.html'));
$response = $oss_api->submit($request);
```

#### List inclusion patterns

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/WEB_crawler/inclusion_patterns/list.html)

```php
$request = new OpenSearchServer\Crawler\Web\Patterns\Inclusion\GetList();
$request->index('index_name');
$response = $oss_api->submit($request);
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    print_r($item);
}
```

#### Delete inclusion patterns

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/WEB_crawler/inclusion_patterns/delete.html)

```php
$request = new OpenSearchServer\Crawler\Web\Patterns\Inclusion\Delete();
$request->index('index_name')
        ->pattern('http://www.website1.com/*');
$response = $oss_api->submit($request);
```

#### Insert exclusion patterns

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/WEB_crawler/exclusion_patterns/insert.html)

```php
$request = new OpenSearchServer\Crawler\Web\Patterns\Exclusion\Insert();
$request->index('index_name')
        ->pattern('http://www.exclude.com/*')
        ->patterns(array('http://www.exclude1.com/page1', 'http://www.exclude2.net/page1'));
$response = $oss_api->submit($request);
```

#### List exclusion patterns

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/WEB_crawler/exclusion_patterns/list.html)

```php
$request = new OpenSearchServer\Crawler\Web\Patterns\Exclusion\GetList();
$request->index('index_name');
$response = $oss_api->submit($request);
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    print_r($item);
}
```

#### Delete exclusion patterns

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/WEB_crawler/exclusion_patterns/delete.html)

```php
$request = new OpenSearchServer\Crawler\Web\Patterns\Exclusion\Delete();
$request->index('index_name')
        ->pattern('http://www.exclude1.com/page1');
$response = $oss_api->submit($request);
```

### Start crawler

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/WEB_crawler/start.html)

```php
$request = new OpenSearchServer\Crawler\Web\Start();
$request->index('index_name');
$response = $oss_api->submit($request);
```

### Stop crawler

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/WEB_crawler/stop.html)

```php
$request = new OpenSearchServer\Crawler\Web\Stop();
$request->index('index_name');
$response = $oss_api->submit($request);
```

### Get crawler status

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/WEB_crawler/status.html)

```php
$request = new OpenSearchServer\Crawler\Web\GetStatus();
$request->index('index_name');
$response = $oss_api->submit($request);
```


## REST crawler

### List existing REST crawlers

```php
$request = new OpenSearchServer\Crawler\Rest\GetList();
$request->index('00__test_file');
$response = $oss_api->submit($request);
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    print_r($item);
}
```

### Execute a REST crawler

```php
$request = new OpenSearchServer\Crawler\Rest\Execute();
$request->index('00__test_file')
        ->name('test__crawler');
$response = $oss_api->submit($request);
```

Available methods:

* **name(string $name)**: name of REST crawler to execute.


## Autocompletion

### Create an autocompletion

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/auto-completion/create_update.html)

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

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/auto-completion/build.html)


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

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/auto-completion/list.html)

Several autocompletion items can be built, each with particular fields for some specific purpose.

```php
$request = new OpenSearchServer\Autocompletion\GetList();
$request->index('index_name');
$response = $oss_api->submit($request);
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    print_r($item);
}
```

### Query autocompletion

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/auto-completion/query.html)

```php
$request = new OpenSearchServer\Autocompletion\Query();
$request->index('index_name')
        ->name('autocomplete')
        ->query('Three Musk')
        ->rows(10);
$response = $oss_api->submit($request);
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    print_r($item);
}
```

Available methods:

* **name(string $name)**: name of autocompletion item to query.
* **query(string $keywords)**: keywords to use for suggestions. Usually beginning of a word.
* **rows(int $numberOfRows)**: number of suggestions to return.

### Delete an autocompletion item

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/auto-completion/delete.html)

```php
$request = new OpenSearchServer\Autocompletion\Delete();
$request->index('index_name')
        ->name('autocomplete');
$response = $oss_api->submit($request);
```
Available methods:

* **name(string $name)**: name of autocompletion item to delete.

## Documents

### Push documents

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/document/put_json.html)

Add document with array notation:

```php
$request = new OpenSearchServer\Document\Put();
$request->index('index_name');
$request->addDocument(array(
    'lang' => OpenSearchServer\Request::LANG_FR,
    'fields' => array(
        array(
            'name' => 'uri',
            'value' => '1'
        ),
        array(
            'name' => 'title',
            'value' => 'The Count Of Monte-Cristo, Alexandre Dumas'
        ),
        array(
            'name' => 'autocomplete',
            'value' => 'The Count Of Monte-Cristo, Alexandre Dumas'
        ),
        array(
            'name' => 'content',
            'value' => '"Very true," said Monte Cristo; "it is unnecessary, we know each other so well!"
"On the contrary," said the count, "we know so little of each other."
"Indeed?" said Monte Cristo, with the same indomitable coolness; "let us see. Are you not the soldier Fernand who deserted on the eve of the battle of Waterloo? Are you not the Lieutenant Fernand who served as guide and spy to the French army in Spain? Are you not the Captain Fernand who betrayed, sold, and murdered his benefactor, Ali? And have not all these Fernands, united, made Lieutenant-General, the Count of Morcerf, peer of France?"
"Oh," cried the general, as if branded with a hot iron, "wretch,—to reproach me with my shame when about, perhaps, to kill me! No, I did not say I was a stranger to you.'
),
        )
    ));
$response = $oss_api->submit($request);
```

Add documents by creating OpenSearchServer\Document\Document objects:

```php
$document = new OpenSearchServer\Document\Document();
$document->lang(OpenSearchServer\Request::LANG_FR)
         ->field('title','Test The Count 2')
         ->field('autocomplete','Test The Count 2')
         ->field('uri', '2');

$document2 = new OpenSearchServer\Document\Document();
$document2->lang(OpenSearchServer\Request::LANG_FR)
          ->field('title','Test The Count 3')
          ->field('autocomplete','Test The Count 3')
          ->field('uri', '3');

$request = new OpenSearchServer\Document\Put();
$request->index('index_name')
        ->addDocuments(array($document, $document2));
$response = $oss_api->submit($request);
```

Available methods:

* **addDocument(array / OpenSearchServer\Document\Document $document)**: add a document in list of documents to add in the index.
* **addDocuments(array $documents)**: helper method. Add several document, call `addDocument()` for each item in array.

Available methods for object of type OpenSearchServer\Document\Document:

* **lang(string $lang)**: set lang of indexation. Used by some Analyzers to transform text.
* **field(string $name, string $value, int $boost)**: give value to a field, with an optionnal boost. You would probably prefer to use boost at query time.

### Delete documents

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/document/delete_by_JSON.html)

```php
$request = new OpenSearchServer\Document\Delete();
$request->index('index_name')
        ->field('id')
        ->value('3')
        ->values(array('4','5','6'));
$response = $oss_api->submit($request);
```

Available methods:

* **field(string $name)**: name of field on which base deletion.
* **value(string $value)**: value of the field to delete.
* **values(array $values)**: helper method. Call `value()` for each item in array.

## Execute search queries

### Search options

Two types of search queries exist in OpenSearchServer : Search field and Search pattern.

They both offer lots of common options and only differ in the way of specfiying searched fields:

```php
$request = new ...;
$request->index('index_name')
        ->emptyReturnsAll()
        ->query('house')
        //set operator to use when multiple keywords
        ->operator(OpenSearchServer\Search\Search::OPERATOR_AND)
        //set lang of keywords
        ->lang('FRENCH')
        //enable logging
        ->enableLog()
        //set returned fields
        ->returnedFields(array('title', 'url'))
        //set static filter
        ->filter('status:1')
        //set another static filter, different way
        ->filterField('year', '[0 TO 1990]')
        //set another static filter, with yet a different way
        ->filterField('category', array('files', 'archives'))
        //set number of results
        ->rows(5)
        //configure sorting
        ->sort('date', OpenSearchServer\Search\Search::SORT_DESC)
        //set facets (min 1, multivalued field)
        ->facet('category', 1, true)
        //set snippets
        ->snippet('title')
        ->snippet('content', 'b', '...', 200, 1, OpenSearchServer\Search\Search::SNIPPET_SENTENCE_FRAGMENTER);
$results = $oss_api->submit($request);
```

Available methods:

* General options:
  * **query(string $query):** search keywords
  * **emptyReturnsAll(boolean $value):** if set to true and keywords are empty will return every documents of the index
  * **operator(string $operator):** Set the default operator: OR or AND
  * **lang(string $lang):**
  * **enableLogs(boolean value):** Enale logging of this query
  * **returnedFields(array $fields):** An array of fieldnames to return with results
  * **rows(int $rows):**
  * **template(string $name):** set name of query template to use. If set, query will use given registered query template but will override every parameters defined in the query object.
  * **snippet():**
* Sorting options
  * **sort(string $field, string $direction):**
  * **sorts(array $sorts, string $direction):** helper method. Calls `sort()` for each item in array.
* Scoring options
  * **scoring(string $field, int $weight, boolean $ascending, type $type):**  
* Facetting options
  * **facet(string $field, int $min = 0, boolean $multi = false, boolean $postCollapsing = false):**
* Filtering options
  * **queryFilter(string $filter):**
  * **negativeFilter(string $filter):**
  * **geoFilter(string $shape, string $unit, int $distance):**
  * **negativeGeoFilter(string $shape, string $unit, int $distance):**
  * **filter(string $field):**
  * **filterField(string $field, string $filter, string $join, boolean $addQuotes):**
* Collapsing options
  * **collapsing(string $field, int $max, string $mode, string $type):**
* Join options
  * **join(string $indexName, string $queryTemplate, string $queryString, string $localField, string $foreignField, string $type, boolean $returnFields, boolean $returnScores, boolean $returnFacets):**

### Search(field)

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/searching_using_fields/search.html)

Fields that must be searched are specified precisely in this kind of query:

```php
$request = new OpenSearchServer\Search\Field\Search();
$request->index('index_name')
        ...
        //set some search fields
        ->searchFields(array('content', 'url'))
        //set a specific different search field with Term & Phrase, term boost = 5 and phrase boost = 10
        ->searchField('title', OpenSearchServer\Search\Field\Search::SEARCH_MODE_TERM_AND_PHRASE, 5, 10)
        ...
$results = $oss_api->submit($request);
```

Available methods:

* **searchField(string $field, string $mode, int $boost, int $phraseBoost):**
* **searchFields(array $fields, string $mode, int $boost, int $phraseBoost):** helper method. Calls `searchField()` for each item in array.

#### Save a Search(field) query template

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/searching_using_fields/template_create_update.html)

Query template can be registered to be used later without having to give every parameters. They can also be edited with the administration interface.


```php
$request = new OpenSearchServer\Search\Field\Put();
$request->index('index_name')
        ->emptyReturnsAll()
        ->operator(OpenSearchServer\Search\Search::OPERATOR_AND)
        ->searchFields(array('content', 'url'))
        ...
        ->template('template_name');
$results = $oss_api->submit($request);
```

### Search(pattern)

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/searching_using_patterns/search.html)

With this kind of query searched fields are configured with a pattern language:

```php
$request = new OpenSearchServer\Search\Pattern\Search();
$request->index('index_name')
        ...
        //configure search pattern
        ->patternSearchQuery('title:($$)^10 OR titleExact:($$)^10 OR titlePhonetic:($$)^10')
        //configure pattern to use for snippets
        ->patternSnippetQuery('title:($$) OR content:($$)')
        ...
$results = $oss_api->submit($request);
```

#### Save a Search(pattern) query template

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/searching_using_patterns/template_create_update.html)

Query template can be registered to be used later without having to give every parameters. They can also be edited with the administration interface.


```php
$request = new OpenSearchServer\Search\Pattern\Put();
$request->index('index_name')
        ->emptyReturnsAll()
        ->operator(OpenSearchServer\Search\Search::OPERATOR_AND)
        ->patternSearchQuery('title:($$)^10 OR titleExact:($$)^10 OR titlePhonetic:($$)^10')
        ...
        ->template('template_name');
$results = $oss_api->submit($request);
```
## Search templates

As shown above it is possible to save several search templates for future use. 

### List search templates

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/search_template/list.html)

```php
$request = new OpenSearchServer\SearchTemplate\GetList();
$request->index('index_name');
$response = $oss_api->submit($request);
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    print_r($item);
}
```

### Get details of a search template

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/search_template/get.html)

```php
$request = new OpenSearchServer\SearchTemplate\Get();
$request->index('index_name')
        ->name('template_name')
$response = $oss_api->submit($request);
```

### Delete a search template

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/search_template/delete.html)
 
```php
$request = new OpenSearchServer\SearchTemplate\Delete();
$request->index('index_name')
        ->name('template_name')
$response = $oss_api->submit($request);
```

## Synonyms


### Create a list of synonyms

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/synonyms/create_update.html)
 
```php
$request = new OpenSearchServer\Synonyms\Create();
$request->index('index_name')
        ->name('hyperonyms')
        ->addSynonyms('couch,divan,sofa')
        ->addSynonyms(array(
            'car,vehicle,transportation device',
            'keyboard,electronic device'
        ));
$response = $oss_api->submit($request);
```

Available methods:

* **name(string $name):** name of list to create
* **addSynonyms(array/string $list):** synonyms to add. One array entry for each group of synonyms. The synonyms within a group are separated by commas.
                    Example: couch,sofa,divan

### Check if a list of synonyms exists

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/synonyms/check.html)
 
```php
$request = new OpenSearchServer\Synonyms\Exists();
$request->index('index_name')
        ->name('___not_an_existing_list___');
$response = $oss_api->submit($request);
var_dump($response->isSuccess());
```

Available methods:

* **name(string $name):** name of list to check

### Get existing lists of synonyms

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/search_template/delete.html)
 
```php
$request = new OpenSearchServer\Synonyms\GetList();
$request->index('index_name');
$response = $oss_api->submit($request);
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    print_r($item);
}
```

### Get synonyms of a list

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/synonyms/get.html)
 
```php
$request = new OpenSearchServer\Synonyms\Get();
$request->index('index_name')
        ->name('hyperonyms');
$response = $oss_api->submit($request);
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    print_r($item);
}
```

Available methods:

* **name(string $name):** name of list to get

### Delete a list of synonyms

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/search_template/delete.html)
 
```php
$request = new OpenSearchServer\Synonyms\Delete();
$request->index('index_name')
        ->name('hyperonyms');
$response = $oss_api->submit($request);
```

Available methods:

* **name(string $name):** name of list to delete


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
 