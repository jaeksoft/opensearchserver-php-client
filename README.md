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
    "require": {
        "opensearchserver/opensearchserver": "3.0.*"
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

## Table of contents

* **[How to make requests](#how-to-make-requests)**
  * [Create an handler](#create-an-handler)
  * [Create a request](#create-a-request)
    * _[Create request by using an array of JSON parameters](#create-request-by-using-an-array-of-json-parameters)_
    * _[Create request by using JSON text](#create-request-by-using-json-text)_
  * [Handle response and search results](#handle-response-and-search-results)
    * _[OpenSearchServer\Response\Response](#opensearchserverresponseresponse)_
    * _[OpenSearchServer\Response\ResponseIterable](#opensearchserverresponseresponseiterable)_
    * _[OpenSearchServer\Response\SearchResult](#opensearchserverresponsesearchresult)_
    * _[OpenSearchServer\Response\MoreLikeThisResult](#opensearchserverresponsemorelikethisresult)_
    * _[OpenSearchServer\Response\SpellCheckResult](#opensearchserverresponsespellcheckresult)_
* **[Work with index](#work-with-index)**
  * [Create an empty index](#create-an-empty-index)
  * [Create an index with a template](#create-an-index-with-a-template)
  * [Get list of index on an instance](#get-list-of-index-on-an-instance)
  * [Delete an index](#delete-an-index)
  * [Check if an index exists](#check-if-an-index-exists)
* **[Instance monitoring](#instance-monitoring)**
* **[Configure schema](#configure-schema)**
  * [Create a field](#create-a-field)
  * [Create full schema](#create-full-schema-at-once)
  * [Get list of fields](#get-list-of-fields)
  * [Get details of a specific field](#get-details-of-a-specific-field)
  * [Delete a field](#delete-a-field)
  * [Set default and unique field for an index](#set-default-and-unique-field-for-an-index)
* **[Analyzers](#analyzers)**
  * [Create an analyzer](#create-an-analyzer)
  * [Get list of analyzers](#get-list-of-analyzers)
  * [Get details of a specific analyzer](#get-details-of-a-specific-analyzer)
* **[Web crawler](#web-crawler)**
  * [Patterns](#patterns)
    * _[Insert inclusion patterns](#insert-inclusion-patterns)_
    * _[List inclusion patterns](#list-inclusion-patterns)_
    * _[Delete inclusion patterns](#delete-inclusion-patterns)_
    * _[Insert exclusion patterns](#insert-exclusion-patterns)_
    * _[List exclusion patterns](#list-exclusion-patterns)_
    * _[Delete exclusion patterns](#delete-exclusion-patterns)_
    * _[Set status for inclusion and exclusion lists](#set-status-for-inclusion-and-exclusion-lists)_
  * [Inject URL in URL database](#inject-url-in-url-database)
  * [Force crawling of URL](#force-crawling-of-url)
  * [Start web crawler](#start-web-crawler)
  * [Stop web crawler](#stop-web-crawler)
  * [Get web crawler status](#get-web-crawler-status)
* **[File crawler](#file-crawler)**
  * [Start file crawler](#start-file-crawler)
  * [Stop file crawler](#stop-file-crawler)
  * [Get file crawler status](#get-file-crawler-status)
  * [File repositories](#file-repositories)
    * _[Local file](#local-file)_
    * _[FTP](#ftp)_
    * _[SMB/CIFS](#smbcifs)_
    * _[Swift](#swift)_
* **[REST crawler](#rest-crawler)**
  * [List existing REST crawlers](#list-existing-rest-crawlers)
  * [Execute a REST crawler](#execute-a-rest-crawler)
* **[Parse files](#parse-files)**
  * [List existing parsers](#list-existing-parsers)
  * [Get details about a specific parser](#get-details-about-a-specific-parser)
  * [Parse a file by uploading it](#parse-a-file-by-uloading-it)
  * [Parse a file located on the server](#parse-a-file-located-on-the-server)
  * [Parse a file and let OpenSearchServer detect its type](#parse-a-file-and-let-opensearchserver-detect-its-type)
* **[Autocompletion](#autocompletion)**
  * [Create an autocompletion](#create-an-autocompletion)
  * [Build autocompletion](#build-autocompletion)
  * [Get list of existing autocompletion items](#get-list-of-existing-autocompletion-items)
  * [Query autocompletion](#query-autocompletion)
  * [Delete an autocompletion item](#delete-an-autocompletion-item)
* **[Documents](#documents)**
  * [Push documents](#push-documents)
    * _[Add document with array notation](#add-document-with-array-notation)_
    * _[Add documents by creating OpenSearchServer\Document\Document objects](#add-documents-by-creating-opensearchserverdocumentdocument-objects)_
    * _[Add documents by pushing text file](#add-documents-by-pushing-text-file)_
  * [Delete documents](#delete-documents)
  * [Delete documents by query](#delete-documents-using-an-existing-query-template-or-using-a-query-pattern)
* **[Execute search queries](#run-search-queries)**
  * [Search options](#search-options)
  * [Search(field)](#searchfield)
    * _[Save a Search(field) query template](#save-a-searchfield-query-template)_
  * [Search(pattern)](#searchpattern)
    * _[Save a Search(pattern) query template](#save-a-searchpattern-query-template)_
* **[Search templates](#search-templates)**
  * [List search template](#list-search-templates)
  * [Get details of a search template](#get-details-of-a-search-template)
  * [Delete a search template](#delete-a-search-template)
* **[Search in batch](#search-in-batch)**
* **[Synonyms](#synonyms)**
  * [Create a list of synonyms](#create-a-list-of-synoyms)
  * [Check if a list of synonyms exists](#check-if-a-list-of-synonyms-exists)
  * [Get existing lists of synonyms](#get-existing-lists-of-synonyms)
  * [Get synonyms of a list](#get-synonyms-of-a-list)
  * [Delete a list of synonyms](#delete-a-list-of-synonyms)
* **[Stop words](#stop-words)**
  * [Create a list of stop words](#create-a-list-of-stop-words)
  * [Check if a list of stop words exists](#check-if-a-list-of-stop-words-exists)
  * [Get existing lists of stop words](#get-existing-lists-of-stop-words)
  * [Get stop words of a list](#get-stop-words-of-a-list)
  * [Delete a list of stop words](#delete-a-list-of-stop-words)
* **[More like this queries](#more-like-this-queries)**
  * [Create a more like this query template](#create-a-more-like-this-query-template)
  * [Delete a more like this query template](#delete-a-more-like-this-query-template)
  * [Get list of more like this query templates](#get-list-of-more-like-this-query-templates)
  * [Get details of a more like this query template](#get-details-of-a-more-like-this-query-template)
  * [Execute a more like this search](#execute-a-more-like-this-search)
* **[Spellcheck queries](#spellcheck-queries)**
  * [Get list of spellcheck query templates](#get-list-of-spellcheck-query-templates)
  * [Delete a spellcheck query template](#delete-a-spellcheck-query-template)
  * [Execute a spellcheck search](#execute-a-spellcheck-search)
* **[Scheduler](#scheduler)**
  * [Get status of a scheduler job](#get-status-of-a-scheduler-job)
  * [Execute a scheduler job](#execute-a-scheduler-job)
* **[Replication](#replication)**
  * [Get list of replications](#get-list-of-replications)
  * [Get details about one replication](#get-details-about-one-replication)
  * [Create or update a replication](#create-or-update-a-replication)
  * [Start a replication](#start-a-replication)
  * [Delete a replication](#delete-a-replication)

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

#### Create request by using an array of JSON parameters

JSON body of request can be given as an array of JSON parameters to the constructor. If values are given this way every data set by calling specific methods on this request will be ignored.

However some methods must still be called to set index on which work and every parameters used directly in request's URL. 

Example: create a Search field template:

```php
$json = array();
$json['query'] = "test with values";
$json['searchFields'] = array(
    array(
        "field" => "title",
        "mode" => "PHRASE",
        "boost" => 3
    ),
    array(
        "field" => "content",
        "mode" => "PHRASE",
        "boost" => 4
    )
);
$request = new OpenSearchServer\Search\Field\Put($json);
$request->index('00__test_web')
        ->template('test_with_json_values');
$response = $oss_api->submit($request);
```

#### Create request by using JSON text

JSON body of request can be given as a JSON strings to the constructor. If values are given this way every data set by calling specific methods on this request or by giving JSON array values will be ignored.

However some methods must still be called to set index on which work and every parameters used directly in request's URL.

Example: create a Search field template:

```php
$json = <<<JSON
{
    "query": "open search server",
    "start": 0,
    "rows": 10,
    "lang": "ENGLISH",
    "operator": "AND",
    "collapsing": {
        "max": 2,
        "mode": "OFF",
        "type": "OPTIMIZED"
    },
    "returnedFields": [
        "url"
    ],
    "snippets": [
        {
            "field": "title",
            "tag": "em",
            "separator": "...",
            "maxSize": 200,
            "maxNumber": 1,
            "fragmenter": "NO"
        },
        {
            "field": "content",
            "tag": "em",
            "separator": "...",
            "maxSize": 200,
            "maxNumber": 1,
           "fragmenter": "SENTENCE"
        }
    ],
    "enableLog": false,
    "searchFields": [
        {
            "field": "title",
            "mode": "PHRASE",
            "boost": 3
        },
        {
            "field": "content",
            "mode": "PHRASE",
            "boost": 4
        },
        {
            "field": "titleExact",
            "mode": "PHRASE",
            "boost": 5
        },
        {
            "field": "contentExact",
            "mode": "PHRASE",
            "boost": 6
        }
    ]
}
JSON;
$request = new OpenSearchServer\Search\Field\Put(null, $json);
$request->index('00__test_web')
        ->template('test_with_json_text');
$response = $oss_api->submit($request);
```

### Handle response and search results

Several types of responses can be returned by `submit()`. Internally this method uses a Factory that builds a response depending on the type of Request given.

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
  * OpenSearchServer\MoreLikeThis\GetList
  * OpenSearchServer\Analyzer\GetList
  * OpenSearchServer\Synonyms\GetList
  * OpenSearchServer\Crawler\Rest\GetList
  * OpenSearchServer\Crawler\Web\Patterns\Exclusion\GetList
  * OpenSearchServer\Crawler\Web\Patterns\Inclusion\GetList
  * OpenSearchServer\Parser\GetList
  
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

Example: this class being iterable it can also be used in a loop structure:

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

### OpenSearchServer\Response\MoreLikeThisResult
  
This kind of response looks like OpenSearchServer\Response\SearchResult but with fewer features, since results returned by MoreLikeThis query are simpler.

* Methods:
  * **getResults():** return array of objects of type OpenSearchServer\Response\Result  
  * **getQuery():** return query

### OpenSearchServer\Response\SpellCheckResult

This response is returned by SpellCheck queries. It is used to access spell check suggestions for each asked field.

Example:

```php
$request = new OpenSearchServer\SpellCheck\Search();
$request->index('index_name')
        ->query('houze')
        ->template('spellcheck');
$response = $oss_api->submit($request);
var_dump($response->getBestSpellSuggestion('title'));
var_dump($response->getSpellSuggestionsArray('title'));
```

Available methods:


* **getSpellSuggestionsArray(string $fieldname):**  return the spell suggestions for one field as array, key is searched word and value is array of suggestions: key is suggestion and value frequency. Array will be sorted with more frequent suggestions at the beginning.
  * Example of result

```  
  array (size=3)
  'houze' => 
    array (size=2)
      'house' => int 12
      'houzz' => int 6
```
   
* **getBestSpellSuggestion(string $fieldname):** return best spell suggestion for this field.
* **getSpellSuggest(string $fieldname):** helper method, alias to `getBestSpellSuggestion()`. 

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

## Instance monitoring

Several instance-wide monitoring properties can be retrieved:

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
* ...
* ...

---

Available methods:

* **full(boolean $full):** if set to true return every available properties. Otherwise return only some basic properties.

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

### Create full schema at once

Schema can be totally created at once using some JSON Text or JSON array of values with object of type `OpenSearchServer\Field\CreateBulk`.

```php
$json = <<<JSON
[
        {
            "name": "uniqueId",
            "indexed": "YES",
            "stored": "NO"
        },
        {
            "name": "title",
            "indexed": "YES",
            "stored": "YES",
            "analyzer": "TextAnalyzer"
        },
        {
            "name": "description",
            "indexed": "YES",
            "stored": "YES",
            "analyzer": "TextAnalyzer"
        },
        {
            "name": "descriptionStandard",
            "indexed": "YES",
            "stored": "NO",
            "analyzer": "StandardAnalyzer",
            "copyOf": [
                "description"
            ]
        }
]
JSON;
$request = new OpenSearchServer\Field\CreateBulk(null, $json);
$request->index('00__test_schema');
$response = $oss_api->submit($request);
```

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

## Analyzers

### Create an analyzer

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/analyzers/create_update.html)

Analyzer can be created or updated using some JSON Text or JSON array of values with object of type `OpenSearchServer\Analyzer\Create`.

```php
$json = <<<JSON
{
  "queryTokenizer":{"name":"KeywordTokenizer"},
  "indexTokenizer":{"name":"KeywordTokenizer"},
  "filters":[
    {
      "name":"ShingleFilter",
      "properties":{
        "max_shingle_size":"5",
        "token_separator":" ",
        "min_shingle_size":"1"
      },
      "scope":"QUERY_INDEX"
    },
    {
      "name":"PrefixSuffixStopFilter",
      "properties":{
        "prefixList":"English stop words",
        "ignore_case":"true",
        "token_separator":" ",
        "suffixList":"English stop words"
      },
      "scope":"QUERY_INDEX"
    } 
  ]
}
JSON;
$request = new OpenSearchServer\Analyzer\Create(null, $json);
$request->index('index_name')
        ->name('TestAnalyzer');
$response = $oss_api->submit($request);
var_dump($response->isSuccess());
```

Available methods:

* **name(string $name)**: name of the analyzer to create or update.
* **lang(string $lang)**: lang of the analyzer to create or update.

### Get list of analyzers

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/analyzers/list.html)

```php
$request = new OpenSearchServer\Analyzer\GetList();
$request->index('index_name');
$response = $oss_api->submit($request); 
foreach($response as $key => $analyzer) {
    echo $analyzer->name . ' - ' . $analyzer->lang. '<br/>';
}
```

### Get details of a specific analyzer

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/analyzers/get.html)

```php
$request = new OpenSearchServer\Analyzer\Get();
$request->index('index_name')
        ->name('TextAnalyzer')
        ->lang(OpenSearchServer\Request::LANG_FR);
$response = $oss_api->submit($request);
```

Available methods:

* **name(string $name)**: name of the analyzer to get information for.
* **lang(string $lang)**: lang of the analyzer to get information for.


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

#### Set status for inclusion and exclusion lists

```php
$request = new OpenSearchServer\Crawler\Web\Patterns\SetStatus();
$request->index('00__test_web')
        ->inclusion(false)
        ->exclusion(true);
$response = $oss_api->submit($request);
var_dump($response);
```

### Inject URL in URL database

In addition to inserting pattern it is also needed to tell crawler which URL it should use to start crawling. It will then discover automatically new URLs to crawl.

```php
$request = new OpenSearchServer\Crawler\Web\Url\Insert();
$request->index('index_name')
        ->urls(array('http://www.cnn.com/sport', 'http://website1.com'));
$response = $oss_api->submit($request);
```

### Force crawling of URL

Same as "Manual crawl" in OpenSearchServer's interface. Given URL must be in inclusion patterns.

```php
$request = new OpenSearchServer\Crawler\Web\Crawl();
$request->index('index_name')
        ->url('http://www.cnn.com/sport');
$response = $oss_api->submit($request);
```

If you want OSS to return the crawled data (content of the page and all extracted fields), use method `returnData(true)`:

```php
$request = new OpenSearchServer\Crawler\Web\Crawl();
$request->index('index_name')
        ->url('http://www.cnn.com/sport')
        ->returnData();
$response = $oss_api->submit($request);
var_dump($response);
```

### Start web crawler

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/WEB_crawler/start.html)

```php
$request = new OpenSearchServer\Crawler\Web\Start();
$request->index('index_name');
$response = $oss_api->submit($request);
```

### Stop web crawler

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/WEB_crawler/stop.html)

```php
$request = new OpenSearchServer\Crawler\Web\Stop();
$request->index('index_name');
$response = $oss_api->submit($request);
```

### Get web crawler status

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/WEB_crawler/status.html)

```php
$request = new OpenSearchServer\Crawler\Web\GetStatus();
$request->index('index_name');
$response = $oss_api->submit($request);
```

## File crawler

### Start file crawler

```php
$request = new OpenSearchServer\Crawler\File\Start();
$request->index('index_name');
$response = $oss_api->submit($request);
```

Available method:

* **run($runType):** run crawler `once` or `forever`. Defaults to `forever`.

### Stop file crawler

```php
$request = new OpenSearchServer\Crawler\File\Stop();
$request->index('index_name');
$response = $oss_api->submit($request);
```

### Get file crawler status

```php
$request = new OpenSearchServer\Crawler\File\GetStatus();
$request->index('index_name');
$response = $oss_api->submit($request);
var_dump($response->getInfo());
```

### File repositories

Every type of location share some common methods for insertion:

```php
...
        ->path('/files/invoices')
        ->ignoreHiddenFile(true)
        ->includeSubDirectory(true)
        ->enabled(true)
        ->delay(100);
...
```

Available methods:

* **path(string $path)**
* **ignoreHiddenFile(boolean $ignoreHiddenFile)**
* **includeSubDirectory(boolean $includeSubDirectory)**
* **enabled(string $path):** enable/disable this location
* **delay(int $delay):** delay between each access to a file, in ms.

#### Local file

##### Insert local file location

```php
$request = new OpenSearchServer\Crawler\File\Repository\LocalFile\Insert();
$request->index('index_name')
        ->path('/archives/pdf')
        ->ignoreHiddenFile(true)
        ->includeSubDirectory(true)
        ->enabled(true)
        ->delay(100);
$response = $oss_api->submit($request);
```

##### Delete local file location

```php
$request = new OpenSearchServer\Crawler\File\Repository\LocalFile\Delete();
$request->index('index_name')
        ->path('/archives/pdf');
$response = $oss_api->submit($request);
```

One method must be called to target location to delete.

Available method:

* **path(string $path):** path of location to delete

#### FTP

##### Insert FTP location

```php
$request = new OpenSearchServer\Crawler\File\Repository\Ftp\Insert();
$request->index('index_name')
        ->path('/archives/office_documents')
        ->ignoreHiddenFile(true)
        ->includeSubDirectory(true)
        ->enabled(true)
        ->delay(100)
        ->username('user')
        ->password('p455w0rD')
        ->host('ftp.host.net')
        ->ssl(true);
$response = $oss_api->submit($request);
```

Available methods:

* **username(string $username)** 
* **password(string $password)** 
* **host(string $host)** 
* **ssl(boolean $isSsl):** if set to true uses FTP over SSL. 

##### Delete FTP location

```php
$request = new OpenSearchServer\Crawler\File\Repository\Ftp\Delete();
$request->index('index_name')
        ->path('/archives/office_documents')
        ->username('user')
        ->host('ftp.host.net')
        ->ssl(true);
$response = $oss_api->submit($request);
```

Several methods must be called to target location to delete.

Available methods:

* **path(string $path):** path of location to delete
* **username(string $username)** 
* **host(string $host)** 
* **ssl(boolean $isSsl):** set to true if location to delete uses FTP over SSL 

#### SMB/CIFS

##### Insert SMB/CIFS location

```php
$request = new OpenSearchServer\Crawler\File\Repository\Smb\Insert();
$request->index('index_name')
        ->path('/archives/pdf')
        ->ignoreHiddenFile(true)
        ->includeSubDirectory(true)
        ->enabled(true)
        ->delay(100)
        ->username('user')
        ->password('p455w0rD')
        ->domain('mydomain')
        ->host('myhost.net');
$response = $oss_api->submit($request);
```

Available methods:

* **username(string $username)** 
* **password(string $password)** 
* **domain(string $domain)** 
* **host(string $host)** 

##### Delete SMB/CIFS location

```php
$request = new OpenSearchServer\Crawler\File\Repository\Smb\Delete();
$request->index('index_name')
        ->path('/archives/pdf')
        ->username('user')
        ->domain('mydomain')
        ->host('myhost.net');
$response = $oss_api->submit($request);
```

Several methods must be called to target location to delete.

Available methods:

* **path(string $path):** path of location to delete
* **username(string $username)** 
* **domain(string $domain)**
* **host(string $host)** 

#### Swift

##### Insert Swift location

```php
$request = new OpenSearchServer\Crawler\File\Repository\Swift\Insert();
$request->index('index_name')
        ->path('/archives/pdf')
        ->ignoreHiddenFile(true)
        ->includeSubDirectory(true)
        ->enabled(true)
        ->delay(100)
        ->username('user')
        ->password('p455w0rD')
        ->tenant('mytenant')
        ->container('container_main')
        ->authUrl('http://auth.example.com')
        ->authType(OpenSearchServer\Crawler\File\Repository\Swift\Insert::AUTH_KEYSTONE);
$response = $oss_api->submit($request);
```

Available methods:

* **username(string $username)** 
* **password(string $password)** 
* **tenant(string $tenant)** 
* **container(string $container)** 
* **authUrl(string $authUrl)** 
* **authType(string $authType):** can be `KEYSTONE` or `IAM`.

##### Delete Swift location

```php
$request = new OpenSearchServer\Crawler\File\Repository\Swift\Delete();
$request->index('index_name')
        ->path('/archives/pdf')
        ->username('user')
        ->container('container_main');
$response = $oss_api->submit($request);
```

Several methods must be called to target location to delete.

Available methods:

* **path(string $path):** path of location to delete
* **username(string $username)** 
* **container(string $container)**

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


## Parse files

OpenSearchServer is able to parse files from lots of different types. Parsers allow for extraction of information inside documents.

Methods for this API do not require an `index` to work with, since parsing is "index-free". Those API do not index any data, they simply 
parse files and send back parsed data.

### List existing parsers

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/parsers/list.md)

```php
$request = new OpenSearchServer\Parser\GetList();
$response = $oss_api->submit($request);
foreach($response as $value) {
    var_dump($value);
}
```

### Get details about a specific parser

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/parsers/get.md)

```php
$request = new OpenSearchServer\Parser\Get();
$request->name('html');
$response = $oss_api->submit($request);
var_dump($response->getJsonValues());
echo 'Information returned by HTML parser:';
var_dump($response->getJsonValues()->fields);
```

### Parse a file by uploading it

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/parsers/parse_upload_file.md)

Use this API to send a file to OpenSearchServer and get back parsed data.

```php
$request = new OpenSearchServer\Parser\Parse\Upload();
$request->name('pdf')
        ->file(__DIR__.'/BookPdf.pdf');
$response = $oss_api->submit($request);
var_dump($response->getJsonValues());
```

Another way to send the file is by using a particular method of the Handler. 
This method does not use the Buzz HTTP client but rather directly makes a CURL call, using CurlFile to send the file.

The file path must be given using method `filePath()` instead of `file()`, and request must be submitted using `submitFile()` instead of `submit()`.

This method is more direct and will use less memory than the previous one.

```php
$request = new OpenSearchServer\Parser\Parse\Upload();
$request->name('pdf')
        ->filePath(__DIR__.'/BookPdf.pdf');
$response = $oss_api->submitFile($request);
var_dump($response->getJsonValues());
```

### Parse a file located on the server

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/parsers/parse_local_file.md)

Use this API to ask OpenSearchServer to parse a file located on its server, and get back parsed data.

```php
$request = new OpenSearchServer\Parser\Parse\Local();
$request->name('pdf')
        ->file('E:/_temp/BookPdf.pdf');
$response = $oss_api->submit($request);
var_dump($response->getJsonValues());
```

### Parse a file and let OpenSearchServer detect its type

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/parsers/parse_detect_mime.md)

Use this API to send a file or to parse a file located on the server: OpenSearchServer will try to automatically detect its MIME type to apply the correct parser on it.

```php
$request = new OpenSearchServer\Parser\Parse\DetectType();
$request->path('E:/_temp/report.docx');
$response = $oss_api->submit($request);
var_dump($response);
```

Available methods:

* **name(string $name):** name of the file. Optionnal.
* **type(string $type):** type of the file. Optionnal.
* **path(string $path):** path of the file located on the server. Optionnal.
* **file(string $fullPath):** path of the file to send to the parser. Optionnal.
* **filePath(string $fullPath):** path of the file to send to the parser if request is submitted using `submitFile()`. Optionnal.
* **variable(string $variable, string $value):** one property of the parser. Optionnal.

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

* **name(string $name):** name of autocompletion item to create.
* **field(string $name):** name of field in main schema from which suggestion are returned.
* fields(array $fields):** helper method, calls `field()` for each item in array.

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

* **name(string $name):** name of autocompletion item to build.


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

### Add document with array notation

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
            'name' => 'title',
            'value' => 'Multiple value for field title'
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

#### Add documents by creating OpenSearchServer\Document\Document objects

```php
$document = new OpenSearchServer\Document\Document();
$document->lang(OpenSearchServer\Request::LANG_FR)
         ->field('title','Test The Count 2')
         ->field('title','One field can be indexed with multiple values')
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
* **field(string $name, string $value, int $boost)**: give value to a field, with an optionnal boost. You would probably prefer to use boost at query time. This method can be called several times with same `$name` argument to index multiple values for one field.  

#### Add documents by pushing text file

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/document/put_text.html)

Text files in CSV or TTL can be pushed to OpenSearchServer, with a regexp pattern to match fields.

```php
$data = <<<TEXT
4;The Three Musketeers;In 1625 France, d'Artagnan-a poor young nobleman-leaves his family in Gascony and travels to Paris with the intention of joining the Musketeers of the Guard. However, en route, at an inn in Meung-sur-Loire, an older man derides d'Artagnan's horse and, feeling insulted, d'Artagnan demands to fight a duel with him. The older man's companions beat d'Artagnan unconscious with a pot and a metal tong that breaks his sword. His letter of introduction to Monsieur de Tréville, the commander of the Musketeers, is stolen. D'Artagnan resolves to avenge himself upon the man, who is later revealed to be the Comte de Rochefort, an agent of Cardinal Richelieu, who is in Meung to pass orders from the Cardinal to Milady de Winter, another of his agents.;en
5;Twenty Years After;The action begins under Queen Anne of Austria regency and Cardinal Mazarin ruling. D'Artagnan, who seemed to have a promising career ahead of him at the end of The Three Musketeers, has for twenty years remained a lieutenant in the Musketeers, and seems unlikely to progress, despite his ambition and the debt the queen owes him;en
6;The Vicomte de Bragelonne;The principal heroes of the novel are the musketeers. The novel's length finds it frequently broken into smaller parts. The narrative is set between 1660 and 1667 against the background of the transformation of Louis XIV from child monarch to Sun King.;en";
TEXT;
$request = new OpenSearchServer\Document\PutText();
$request->index('00__test_file')
        ->pattern('(.*?);(.*?);(.*?);(.*?)')
        ->data($data)
        ->langpos(4)
        ->buffersize(100)
        ->charset('UTF-8')
        ->fields(array('uri', 'title', 'content', 'lang'));
$response = $oss_api->submit($request);
```

Available methods:

* **pattern(string $pattern):** REGEXP pattern to use for mapping values to fields.
* **data(string $data):**
* **langpos(int $pos):** position of language in mapped fields (index start at 1)
* **buffersize(int $bufferSize)**
* **charset(string $charset):** charset of sent text
* **field(string $fieldname):** one field mapping. Can be called several times to map several fields.
* **fields(array $fields):** help method. Calls `field()` for each item in array.



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

### Delete documents using an existing query template or using a query pattern

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/document/delete_by_query.md)

```php
$request = new OpenSearchServer\Document\DeleteByQuery();
$request->index("00__test")
        ->query('title:[* TO *]');
$response = $oss_api->submit($request);
```

```php
$request = new OpenSearchServer\Document\DeleteByQuery();
$request->index("00__test")
        //query template deleteMoreThanOneWeek can define some relative date filters
        ->template('deleteMoreThanOneWeek');
$response = $oss_api->submit($request);
```

Available methods:

_One or the other of these 2 methods must be used:_

* **template($queryTemplate)**: name of a query template to use for deletion.
* **query($pattern)**: query pattern for selecting documents to delete.

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
        //add a level of sorting for documents with same date
        ->sort('url', OpenSearchServer\Search\Search::SORT_ASC)
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
  * **sort(string $field, string $direction):** add a sorting on one field. Can be called multiple times to successively sort on different fields.
  * **sorts(array $sorts, string $direction):** helper method. Calls `sort()` for each item in array.
* Scoring options
  * **scoring(string $field, int $weight, boolean $ascending, type $type):** add one level of scoring.
* Boosting queries
  * **boostingQuery(string $queryPattern, int $boost):** add one boosting query.  
* Facetting options
  * **facet(string $field, int $min = 0, boolean $multi = false, boolean $postCollapsing = false):** compute facet for one field: this will return count of every different values for this field. Facets can be used through `->getFacets()` when workingh with an `OpenSearchServer\Response\SearchResult` object. You can find more details in the [proper section](#opensearchserverresponsesearchresult).
* Filtering options
  * **queryFilter(string $filter):** add a filter with a pattern query. For example : `lang:en`.
  * **negativeFilter(string $filter):** add a negative query filter.
  * **geoFilter(string $shape, string $unit, int $distance):** add a geo filter.
  * **negativeGeoFilter(string $shape, string $unit, int $distance):** add a negative geo filter
  * **relativeDateFilter(string $field, string $fromUnit, int $fromInterval, string $toUnit, int $toInterval, string $dateFormat, boolean $isNegative):**: add a RelativeDateFilter. This filter allows dynamic date filtering.
    * This filter can be used to simplify date filtering, or when saving a Search template. If a template is saved with a relative date filter it will always force a date filter base on current date. For example if set with values `fromUnit` = days, `fromInterval` = 30, `toUnit` = days, `toInterval` = 0 and `dateFormat` = `yyyyMMdd` this search template will always filter documents whose chosen filtered field contains a date in last 30 days. For instance if $field is "fileSystemDate" and if a search is run on february 1st of 2014 it will translates to this filter: `fileSystemDate:[20140101 TO 20140201]`.  
    * Parameters:
      * `$field`: name of field on which apply filter.
      * `$fromUnit`: unit to use for first boundary. Values can be `OpenSearchServer\Search\Field\Search::RELATIVE_DATE_FILTER_UNIT_DAYS`, `OpenSearchServer\Search\Field\Search::RELATIVE_DATE_FILTER_UNIT_HOURS` and `OpenSearchServer\Search\Field\Search::RELATIVE_DATE_FILTER_UNIT_MINUTES`.
      * `$fromInterval`: interval to use for first boundary.
      * `$toUnit`: unit to use for second boundary. Values can be `OpenSearchServer\Search\Field\Search::RELATIVE_DATE_FILTER_UNIT_DAYS`, `OpenSearchServer\Search\Field\Search::RELATIVE_DATE_FILTER_UNIT_HOURS` and `OpenSearchServer\Search\Field\Search::RELATIVE_DATE_FILTER_UNIT_MINUTES`.
      * `$toInterval`: interval to use for first boundary.
      * `$dateFormat`: format to use to render dates. Full date format is `yyyyMMddHHmmss` (`OpenSearchServer\Search\Field\Search::RELATIVE_DATE_FILTER_DATEFORMAT`).
      * `$isNegative`: whether this filter must be negative or not.
  * **negativeRelativeDateFilter(string $field, string $fromUnit, int $fromInterval, string $toUnit, int $toInterval, string $dateFormat, boolean $isNegative):**: add a negative RelativeDateFilter.
  * **filter(string $field):** helper method, alias to `queryFilter()`.
  * **filterField(string $field, string / array $filter, string $join, boolean $addQuotes):** other way to add a query filter.
    * Parameters:
      * `$field`: name of field on which apply filter.
      * `$filter`: value(s) on which filter.
      * `$join`: if $filter is an array of values, type of join to use between values: OR or AND.
      * `$addQuotes`: whether to add quotes around filtered values or not.
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
        ->name('template_name');
$response = $oss_api->submit($request);
```

### Delete a search template

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/search_template/delete.html)
 
```php
$request = new OpenSearchServer\SearchTemplate\Delete();
$request->index('index_name')
        ->name('template_name');
$response = $oss_api->submit($request);
```

## Search in batch

Multiple queries can be sent at once. Results will be returned for each query, unless parameter `mode` is set to
`first`: then queries will stop as soon as one return results.

Queries must be created as usual: Search field, Search pattern, with or without template.

```php
// Build batch request
$requestBatch = new OpenSearchServer\SearchBatch\SearchBatch();
$requestBatch->index('articles');

// Create some queries with different types
// A Search Field query
$request = new OpenSearchServer\Search\Field\Search();
$request->query('lorem')
        ->emptyReturnsAll()
        ->operator(OpenSearchServer\Search\Search::OPERATOR_AND)
        ->searchField('title', OpenSearchServer\Search\Field\Search::SEARCH_MODE_TERM_AND_PHRASE, 5, 10)
        ->returnedFields(array('title', 'date'));

// A Search Pattern query
$request2 = new OpenSearchServer\Search\Pattern\Search();
$request2->query('lorem')
         ->patternSearchQuery('title:($$)^10 OR titleExact:($$)^10 OR titlePhonetic:($$)^10')
         ->patternSnippetQuery('title:($$) OR content:($$)')
         ->returnedFields(array('title', 'date'))
         ->rows(4);

// A Search Field query using a pre-saved query template
$request3 = new OpenSearchServer\Search\Field\Search();
$request3->query('lorem')
         ->template('search');

// Add the queries to the batch and send the request
$requestBatch->addQueries(array(
                array($request, OpenSearchServer\SearchBatch\SearchBatch::ACTION_CONTINUE), 
                array($request2, OpenSearchServer\SearchBatch\SearchBatch::ACTION_STOP_IF_FOUND),
                array($request3)
              ));
$response = $oss_api->submit($requestBatch);

echo 'This batch returned ' . $response->getNumberOfQueriesWithResult() . ' set of results.';
echo "\n".'<hr/> Results from the second set:'."\n";
$results = $response->getResultsByPosition(1);
foreach($results as $result) {
    var_dump($result);
}
echo "\n".'<hr/> Results from the third set:'."\n";
$results = $response->getResultsByPosition(2);
foreach($results as $result) {
    var_dump($result);
}
```

Available methods:

* **mode(string $mode)**: `OpenSearchServer\SearchBatch\SearchBatch::MODE_FIRST`, or `OpenSearchServer\SearchBatch\SearchBatch::MODE_ALL` (default value) or `OpenSearchServer\SearchBatch\SearchBatch::MODE_MANUAL`.
* **addQuery(OpenSearchServer\Search $query, string $modeManualAction)**: add one query to the batch. Parameter `$modeManualAction`  is the batch action to use for the query, if mode is "manual"
* **addQueries(array $queries)**: add several queries to the batch. Parameter `$queries` is an array of array: each sub array contains one required item, the query, and a second optionnal item, the batchAction to use for this query if mode is "manual".

Response for this request will be of type `SearchBatchResult`.

Available methods for `SearchBatchResult`:

* **getNumberOfQueriesWithResult()**: return number of results set (even empty ones).
* **getResults()**: return all results for all queries.
* **getResultsByPosition(int $position)**: return results for one query. Position starts at 0. This method return object of type [`SearchResult`](#opensearchserverresponsesearchresult).

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


## Stop words

### Create a list of stop words

```php
$request = new OpenSearchServer\StopWords\Create();
$request->index('index_name')
        ->name('Stopwords')
        ->addStopWords(array(
            'of',
            'the',
            'by'
        ));
$response = $oss_api->submit($request);
```

Available methods:

* **name(string $name):** name of list to create
* **addSynonyms(array/string $list):** stop words to add. One array entry for each stop word.

### Check if a list of stop words exists

```php
$request = new OpenSearchServer\StopWords\Exists();
$request->index('index_name')
        ->name('___not_an_existing_list___');
$response = $oss_api->submit($request);
var_dump($response->isSuccess());
```

Available methods:

* **name(string $name):** name of list to check

### Get existing lists of stop words

```php
$request = new OpenSearchServer\StopWords\GetList();
$request->index('index_name');
$response = $oss_api->submit($request);
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    print_r($item);
}
```

### Get stop words of a list

```php
$request = new OpenSearchServer\StopWords\Get();
$request->index('index_name')
        ->name('StopWords');
$response = $oss_api->submit($request);
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    print_r($item);
}
```

Available methods:

* **name(string $name):** name of list to get

### Delete a list of stop words

```php
$request = new OpenSearchServer\StopWords\Delete();
$request->index('index_name')
        ->name('StopWords');
$response = $oss_api->submit($request);
```

Available methods:

* **name(string $name):** name of list to delete

## More like this queries

### Create a more like this query template

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/more-like-this/template_create_update.html)

```php
$request = new OpenSearchServer\MoreLikeThis\Create();
$request->index('index_name')
        //set lang of keywords
        ->lang('FRENCH')
        //set some search fields
        ->fields(array('title', 'content', 'uri'))
        //set returned fields
        ->returnedFields(array('title', 'uri'))
        ->minWordLen(1)
        ->maxWordLen(100)
        ->minDocFreq(1)
        ->minTermFreq(1)
        ->maxNumTokensParsed(5000)
        ->maxQueryTerms(25)
        ->boost(true)
        ->filterField('lang', 'en')
        ->rows(10)
        //give this template a name
        ->template('template_mlt');
$response = $oss_api->submit($request);
```

Available methods:

* **template(string $template):** name of more like this query template to create
* **docQuery(string $docQuery):** query to match document
* **likeText(string $likeText):** searched text
* **analyzerName(string $analyzer):** name of analyzer to apply on searched text
* **fields(array $fields):** array of fieldnames to use
* **minWordLen(int $value):** minimum length of words
* **maxWordLen(int $value):** maximum length of words
* **minDocFreq(int $value):** minimum frequency of document
* **minTermFreq(int $value):** minimum frequency of term
* **maxNumTokensParsed(int $value):** number of max token to parse
* **maxQueryTerms(int $value):** number of max query terms to use
* **boost(boolean $value):** enable boost
* **stopWords(string $value):** name of an existing stop words list 
* **returnedFields(array $fields):** An array of fieldnames to return with results
* Filtering options
  * **queryFilter(string $filter):** add a filter with a pattern query. For example : `lang:en`.
  * **negativeFilter(string $filter):** add a negative query filter.
  * **geoFilter(string $shape, string $unit, int $distance):** add a geo filter.
  * **negativeGeoFilter(string $shape, string $unit, int $distance):** add a negative geo filter
  * **filter(string $field):** helper method, alias to `queryFilter()`.
  * **filterField(string $field, string / array $filter, string $join, boolean $addQuotes):** other way to add a query filter.
    * Parameters:
      * `$field`: name of field on which apply filter.
      * `$filter`: value(s) on which filter.
      * `$join`: if $filter is an array of values, type of join to use between values: OR or AND.
      * `$addQuotes`: whether to add quotes around filtered values or not.
  
### Delete a more like this query template

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/more-like-this/delete.html)

```php
$request = new OpenSearchServer\MoreLikeThis\Delete();
$request->index('index_name')
        ->template('template_mlt');
$response = $oss_api->submit($request);
```

Available methods:

* **template(string $template):** name of more like this query template to delete.

### Get list of more like this query templates

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/more-like-this/list.html)

```php
$request = new OpenSearchServer\MoreLikeThis\GetList();
$request->index('index_name');
$response = $oss_api->submit($request);
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    print_r($item);
}
```
  
### Get details of a more like this query template

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/more-like-this/get.html)

```php
$request = new OpenSearchServer\MoreLikeThis\Get();
$request->index('index_name')
        ->template('template_mlt');
$response = $oss_api->submit($request);
```

Available methods:

* **template(string $template):** name of more like this query template to retrieve.

### Execute a more like this search

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/more-like-this/template_query.html)

```php
$request = new OpenSearchServer\MoreLikeThis\Search();
$request->index('index_name')
        ->likeText('count')
        ->template('template_mlt');
$response = $oss_api->submit($request);
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    print_r($item->getField('title'));
}
```

Available methods:

* **template(string $template):** name of more like this query template to use

Every other methods of `OpenSearchServer\MoreLikeThis\Create` can be used there.


## Spellcheck queries

It is not possible at the moment to create Spellcheck query templates through API. Spellcheck query templates can be listed, deleted and used for a search.

### Get list of spellcheck query templates

```php
$request = new OpenSearchServer\SpellCheck\GetList();
$request->index('index_name');
$response = $oss_api->submit($request);
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    print_r($item);
}
```

### Delete a spellcheck query template

```php
$request = new OpenSearchServer\SpellCheck\Delete();
$request->index('index_name')
        ->template('spellcheck');
$response = $oss_api->submit($request);
```

### Execute a spellcheck search

```php
$request = new OpenSearchServer\SpellCheck\Search();
$request->index('index_name')
        ->query('house')
        ->template('spellcheck');
$response = $oss_api->submit($request);
var_dump($response->getBestSpellSuggestion('title'));
var_dump($response->getSpellSuggestionsArray('title'));
```

## Scheduler

### Get status of a scheduler job

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/scheduler/get_status.md)

```php
$request = new OpenSearchServer\Scheduler\GetStatus();
$request->index('index_name')
        ->name('test job');
$response = $oss_api->submit($request);
```

Available method:

* **name(string $name):** name of scheduler job

### Execute a scheduler job

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/scheduler/run.md)

```php
$request = new OpenSearchServer\Scheduler\Run();
$request->index('index_name')
        ->name('test job')
        ->variable('url', 'http://www.opensearchserver.com');
$response = $oss_api->submit($request);
```

Available method:

* **name(string $name):** name of scheduler job
* **variable(string $name, string $value):** some tasks can receive variables.
* **variables(array $variables):** helper method, calls `variable()` for each item in array. 

## Replication

### Get list of replications

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/replication/list.md)


```php
$request = new OpenSearchServer\Replication\GetList();
$request->index('articles');
$response = $oss_api->submit($request);
foreach($response as $key => $item) {
    echo '<br/>Item #'.$key .': ';
    var_dump($item);
}
```

### Get details about one replication

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/replication/get.md)

```php
$request = new OpenSearchServer\Replication\Get();
$request->index('articles')
        ->name('http://localhost:9090/articles_test_repl');
$response = $oss_api->submit($request);
```

Available method:

* **name(string $name):** name of the replication

### Create or update a replication

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/replication/create_update.md)

```php
$request = new OpenSearchServer\Replication\Create();
$request->index('articles')
        ->replicationType(OpenSearchServer\Request::REPL_MAIN_INDEX)
        ->remoteUrl('http://localhost:9090')
        ->remoteIndexName('articles_test_repl');
$response = $oss_api->submit($request);
```

Available method:

* **name(string $name):** name of the replication
* **replicationType(string $value):** type of replication (use constants defined in `OpenSearchServer\Request`).
* **remoteUrl(string $value):** URL of the target OpenSearchServer instance
* **remoteLogin(string $value):** login for the target instance
* **remoteApiKey(string $value):** API key
* **remoteIndexName(string $value):** target index name
* **secTimeOut(string $value):** timeout in secondes


### Start a replication

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/replication/run.md)

```php
$request = new OpenSearchServer\Replication\Run();
$request->index('articles')
        ->name('http://localhost:9090/articles_test_repl');
$response = $oss_api->submit($request);
```

Available method:

* **name(string $name):** name of the replication

### Delete a replication

[Go to API documentation for this method](http://www.opensearchserver.com/documentation/api_v2/replication/delete.md)

```php
$request = new OpenSearchServer\Replication\Delete();
$request->index('articles')
        ->name('http://localhost:9090/articles_test_repl');
$response = $oss_api->submit($request);
```

Available method:

* **name(string $name):** name of the replication

===========================

OpenSearchServer PHP Client
Copyright 2008-2014 Emmanuel Keller / Jaeksoft
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
 