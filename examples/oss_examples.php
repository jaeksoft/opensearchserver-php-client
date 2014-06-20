<?php 

//initiate API wrapper
$url 		= 'http://localhost:9090';
$app_key 	= '54a51ee4f27cbbcb7a771352b980567f';
$login 		= 'admin';
$oss_api = new OpenSearchServer\Handler(array('url'=>$url, 'key' => $app_key, 'login' => $login ));

/**
 * ## Index\Create
 * Create index
 */
$request = new OpenSearchServer\Index\Create();
$request->index('00__test_empty');
$response = $oss_api->submit($request);

$request = new OpenSearchServer\Index\Create();
$request->index('00__test_file')->template(OpenSearchServer\Request::TEMPLATE_FILE_CRAWLER);
$response = $oss_api->submit($request);

/** 
 * ## Field\Create
 * Create fields
 */
$request = new OpenSearchServer\Field\Create();
$request->index('00__test_empty')
		->name('url')
		->indexed('YES');
$response = $oss_api->submit($request);
var_dump($response);

$request = new OpenSearchServer\Field\Create();
$request->index('00__test_empty')
		->name('title')
		->indexed('YES')
		->analyzer('TextAnalyzer')
		->stored('YES');
$response = $oss_api->submit($request);
var_dump($response);

$request = new OpenSearchServer\Field\Create();
$request->index('00__test_empty')
		->name('titleStandard')
		->indexed('YES')
		->analyzer('StandardAnalyzer')
		->stored('YES')
		->copyOf('title');
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Field\GetList
 * Get list of fields in one index
 */
$request = new OpenSearchServer\Field\GetList();
$request->index('00__test_empty');
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Field\Get
 * Get details of a specific field
 */
$request = new OpenSearchServer\Field\Get();
$request->index('gendarmerie_test')
		->name('titleStandard');
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Field\Delete
 * Delete a specific field
 */
$request = new OpenSearchServer\Field\Delete();
$request->index('00__test_empty')
		->name('titleStandard');
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ##Field\SetDefaultUnique
 * Set default and unique field for an index
 */
$request = new OpenSearchServer\Field\SetDefaultUnique();
$request->index('00__test_file')
		->defaultField()
		//remove unique field for this index
		->uniqueField('uri');
$response = $oss_api->submit($request);
var_dump($response);
exit;

/**
 * ## Index\Delete
 * Delete index
 */
$request = new OpenSearchServer\Index\Delete();
$request->index('00__test_empty');
$response = $oss_api->submit($request);
$request->index('00__test_file');
$response = $oss_api->submit($request);

/**
 * ## Index\GetList
 * Get list of index on the OpenSearchServer instance
 */
$request = new OpenSearchServer\Index\GetList();
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Index\Exists
 * Check if an index exists
 */
$request = new OpenSearchServer\Index\Exists();
$request->index('00__aatest3');
$response = $oss_api->submit($request);
var_dump($response);

$request = new OpenSearchServer\Index\Exists();
$request->index('non_existent_index');
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Search\Field\Search
 * Execute a Search(field) query
 */
$request = new OpenSearchServer\Search\Field\Search();
$request->index('00__aatest3')
		->query('maison')
		->searchField('title')
		->returnedFields('title')
		->rows(4);
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Search\Field\Put
 * Save a Search(field) template
 */
//build request
$request = new OpenSearchServer\Search\Field\Put();
$request->index('00__aatest3')
		->emptyReturnsAll()
		//set operator to use when multiple keywords
		->operator(OpenSearchServer\Search\Search::OPERATOR_AND)
		//set lang of keywords
		->lang('FRENCH')
		//enable logging
		->enableLog()
		//set some search fields
		->searchFields(array('content', 'url'))
		//set a specific different search field with Term & Phrase, term boost = 5 and phrase boost = 10
		->searchField('title', OpenSearchServer\Search\Field\Search::SEARCH_MODE_TERM_AND_PHRASE, 5, 10)
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
		->snippet('content', 'b', '...', 200, 1, OpenSearchServer\Search\Search::SNIPPET_SENTENCE_FRAGMENTER)
		//give this template a name
		->template('new_template');
//dump JSON encoded content
echo '<pre>'; print_r($request->getData()); echo '</pre>';
//send request
$response = $oss_api->submit($request);
//dump response
var_dump($response);