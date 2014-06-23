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
echo '<hr/><h2>Index\Create</h2>';
//create an empty index
$request = new OpenSearchServer\Index\Create();
$request->index('00__test');
$response = $oss_api->submit($request);
var_dump($response);

//create an index with FILE_CRAWLER template
$request = new OpenSearchServer\Index\Create();
$request->index('00__test_file')->template(OpenSearchServer\Request::TEMPLATE_FILE_CRAWLER);
$response = $oss_api->submit($request);
var_dump($response);

//create an index with WEB_CRAWLER template
$request = new OpenSearchServer\Index\Create();
$request->index('00__test_web')->template(OpenSearchServer\Request::TEMPLATE_WEB_CRAWLER);
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Document\Put
 * Add documents in index
 */
echo '<hr/><h2>Document\Put</h2>';
//Add document with array notation
$request = new OpenSearchServer\Document\Put();
$request->index('00__test_file');

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

//Add documents by creating Document objects
$document = new OpenSearchServer\Document\Document();
$document	->lang(OpenSearchServer\Request::LANG_FR)
			->field('title','Test The Count 2')
			->field('autocomplete','Test The Count 2')
			->field('uri', '2');

$document2 = new OpenSearchServer\Document\Document();
$document2	->lang(OpenSearchServer\Request::LANG_FR)
			->field('title','Test The Count 3')
			->field('autocomplete','Test The Count 3')
			->field('uri', '3');

$request->addDocuments(array($document, $document2));

$response = $oss_api->submit($request);
var_dump($oss_api->getLastRequest());
var_dump($response);

/**
 * ## Search\Pattern\Search
 * Execute a Search(pattern) query
 */
echo '<hr/><h2>Search\Pattern\Search</h2>';
$request = new OpenSearchServer\Search\Pattern\Search();
$request->index('00__test_file')
		->query('count')
		->patternSearchQuery('title:($$)^10 OR titleExact:($$)^10 OR titlePhonetic:($$)^10')
		->patternSnippetQuery('title:($$) OR content:($$)')
		->returnedFields(array('title', 'uri'))
		->rows(4);
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Search\Pattern\Put
 * Save a Search(pattern) template
 */
echo '<hr/><h2>Search\Pattern\Put</h2>';
//build request
$request = new OpenSearchServer\Search\Pattern\Put();
$request->index('00__test_file')
		//set operator to use when multiple keywords
		->operator(OpenSearchServer\Search\Search::OPERATOR_AND)
		//set lang of keywords
		->lang('FRENCH')
		//enable logging
		->enableLog()
		//set search pattern
		->patternSearchQuery('title:($$)^10 OR titleExact:($$)^10 OR titlePhonetic:($$)^10 OR url:($$)^5 OR urlSplit:($$)^5 OR urlExact:($$)^5 OR urlPhonetic:($$)^5 OR content:($$) OR contentExact:($$) OR contentPhonetic:($$) OR full:($$)^0.1 OR fullExact:($$)^0.1 OR fullPhonetic:($$)^0.1')
		//set snippet pattern
		->patternSnippetQuery('title:($$) OR content:($$)')
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
		->template('new_template_pattern');
//dump JSON encoded content
echo '<pre style="word-wrap: break-word;">'; print_r($request->getData()); echo '</pre>';
//send request
$response = $oss_api->submit($request);
//dump response
var_dump($response);

/**
 * ## MoreLikeThis\Create
 * Create a more like this template
 */
echo '<hr/><h2>MoreLikeThis\Create</h2>';
//build request
$request = new OpenSearchServer\MoreLikeThis\Create();
$request->index('00__test_file')
		//set lang of keywords
		->lang('FRENCH')
		//set some search fields
		->fields(array('title', 'content', 'url'))
		//set returned fields
		->returnedFields(array('title', 'url'))
		->minWordLen(1)
		->maxWordLen(100)
		->minDocFreq(1)
		->minTermFreq(1)
		->maxNumTokensParsed(5000)
		->maxQueryTerms(25)
		->boost(true)
		//->filterField('lang', 'en')
		->rows(10)
		//give this template a name
		->template('template_mlt');
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## MoreLikeThis\Search
 * Run a Morelikethis query, with or without a template
 */
echo '<hr/><h2>MoreLikeThis\Search</h2>';
$request = new OpenSearchServer\MoreLikeThis\Search();
$request->index('00__test_file')
		->likeText('count')
		->template('template_mlt');
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## MoreLikeThis\Delete
 * Delete a morelikethis template
 */
echo '<hr/><h2>MoreLikeThis\Delete</h2>';
$request = new OpenSearchServer\MoreLikeThis\Delete();
$request->index('00__test_file')
		->template('template_mlt');
$response = $oss_api->submit($request);
var_dump($oss_api->getLastRequest());
var_dump($response);



/**
 * ## Crawler\Web\Patterns\Exclusion\Insert
 * Insert patterns to exclude in web crawler
 */
echo '<hr/><h2>Crawler\Web\Patterns\Exclusion\Insert</h2>';
$request = new OpenSearchServer\Crawler\Web\Patterns\Exclusion\Insert();
$request->index('00__test_web')
		->pattern('http://www.exclude.com/*')
		->patterns(array('http://www.exclude1.com/page1', 'http://www.exclude2.net/page1'));
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Crawler\Web\Patterns\Exclusion\GetList
 * Get list of exclusion patterns
 */
echo '<hr/><h2>Crawler\Web\Patterns\Exclusion\GetList</h2>';
$request = new OpenSearchServer\Crawler\Web\Patterns\Exclusion\GetList();
$request->index('00__test_web');
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Crawler\Web\Patterns\Exclusion\Delete
 * Delete some exclusion patterns
 */
echo '<hr/><h2>Crawler\Web\Patterns\Exclusion\Delete</h2>';
$request = new OpenSearchServer\Crawler\Web\Patterns\Exclusion\Delete();
$request->index('00__test_web')
		->pattern('http://www.exclude1.com/page1');
$response = $oss_api->submit($request);
var_dump($response);


/**
 * ## Crawler\Web\Patterns\Exclusion\GetList
 * Get list of exclusion patterns
 */
echo '<hr/><h2>Crawler\Web\Patterns\Exclusion\GetList</h2>';
$request = new OpenSearchServer\Crawler\Web\Patterns\Exclusion\GetList();
$request->index('00__test_web');
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Crawler\Web\Patterns\Inclusion\Insert
 * Insert patterns to crawl with web crawler
 */
echo '<hr/><h2>Crawler\Web\Patterns\Inclusion\Insert</h2>';
$request = new OpenSearchServer\Crawler\Web\Patterns\Inclusion\Insert();
$request->index('00__test_web')
		->pattern('http://www.alexandre-toyer.fr/*')
		->patterns(array('http://www.lemonde.fr', 'http://www.20minutes.fr/'));
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Crawler\Web\Patterns\Inclusion\GetList
 * Get list of inclusion patterns
 */
echo '<hr/><h2>Crawler\Web\Patterns\Inclusion\GetList</h2>';
$request = new OpenSearchServer\Crawler\Web\Patterns\Inclusion\GetList();
$request->index('00__test_web');
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Crawler\Web\Patterns\Inclusion\Delete
 * Delete some inclusions patterns
 */
echo '<hr/><h2>Crawler\Web\Patterns\Inclusion\Delete</h2>';
$request = new OpenSearchServer\Crawler\Web\Patterns\Inclusion\Delete();
$request->index('00__test_web')
		->patterns(array('http://www.lemonde.fr', 'http://www.20minutes.fr/'));
$response = $oss_api->submit($request);
var_dump($oss_api->getLastRequest());
var_dump($response);

/**
 * ## Crawler\Web\Url\Insert
 * Insert URL to crawl
 */
echo '<hr/><h2>Crawler\Web\Url\Insert</h2>';
$request = new OpenSearchServer\Crawler\Web\Url\Insert();
$request->index('00__test_web')
		->urls(array('http://www.lemonde.fr', 'http://www.20minutes.fr'));
$response = $oss_api->submit($request);
var_dump($oss_api->getLastRequest());
var_dump($response);


/**
 * ## Autocompletion\Create
 * Create an autocompletion
 */
echo '<hr/><h2>Autocompletion\Create</h2>';
$request = new OpenSearchServer\Autocompletion\Create();
$request->index('00__test_file')
		->name('test_autocomplete')
		->field('autocomplete');
$response = $oss_api->submit($request);
var_dump($oss_api->getLastRequest());
var_dump($response);	
	
/**
 * ## Autocompletion\GetList
 * List avalaible autocompletions
 */
echo '<hr/><h2>Autocompletion\GetList</h2>';
$request = new OpenSearchServer\Autocompletion\GetList();
$request->index('00__test_file');
$response = $oss_api->submit($request);
var_dump($oss_api->getLastRequest());
var_dump($response);	
	
/**
 * ## Autocompletion\Build
 * Build autocompletion index
 */
echo '<hr/><h2>Autocompletion\Build</h2>';
$request = new OpenSearchServer\Autocompletion\Build();
$request->index('00__test_file')
		->name('test_autocomplete');
$response = $oss_api->submit($request);
var_dump($oss_api->getLastRequest());
var_dump($response);	
			
/**
 * ## Autocompletion\Query
 * Query autocompletion
 */
echo '<hr/><h2>Autocompletion\Query</h2>';
$request = new OpenSearchServer\Autocompletion\Query();
$request->index('00__test_file')
		->name('test_autocomplete')
		->query('count of')
		->rows(10);
$response = $oss_api->submit($request);
var_dump($oss_api->getLastRequest());
var_dump($response);	
			
/**
 * ## Autocompletion\Delete
 * Delete autocompletion index
 */
echo '<hr/><h2>Autocompletion\Delete</h2>';
$request = new OpenSearchServer\Autocompletion\Delete();
$request->index('00__test_file')
		->name('test_autocomplete');
$response = $oss_api->submit($request);
var_dump($oss_api->getLastRequest());
var_dump($response);	
			
/**
 * ## Document\Delete
 * Delete document
 */
echo '<hr/><h2>Document\Delete</h2>';
$request = new OpenSearchServer\Document\Delete();
$request->index('00__test_file')
		//delete document where field "uri" = "2"
		->field('uri')
		->value('2');
$response = $oss_api->submit($request);
var_dump($oss_api->getLastRequest());
var_dump($response);

/** 
 * ## Field\Create
 * Create fields
 */
echo '<hr/><h2>Field\Create</h2>';
//create 3 fields in newly created empty index
$request = new OpenSearchServer\Field\Create();
$request->index('00__test')
		->name('url')
		->indexed('YES');
$response = $oss_api->submit($request);

var_dump($oss_api->getLastRequest());
var_dump($response);

$request = new OpenSearchServer\Field\Create();
$request->index('00__test')
		->name('title')
		->indexed('YES')
		->analyzer('TextAnalyzer')
		->stored('YES');
$response = $oss_api->submit($request);
var_dump($response);

$request = new OpenSearchServer\Field\Create();
$request->index('00__test')
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
echo '<hr/><h2>Field\GetList</h2>';
$request = new OpenSearchServer\Field\GetList();
$request->index('00__test');
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Field\Get
 * Get details of a specific field
 */
echo '<hr/><h2>Field\Get</h2>';
$request = new OpenSearchServer\Field\Get();
$request->index('00__test')
		->name('titleStandard');
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Field\Delete
 * Delete a specific field
 */
echo '<hr/><h2>Field\Delete</h2>';
$request = new OpenSearchServer\Field\Delete();
$request->index('00__test')
		->name('titleStandard');
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Field\SetDefaultUnique
 * Set default and unique field for an index
 */
echo '<hr/><h2>Field\SetDefaultUnique</h2>';
$request = new OpenSearchServer\Field\SetDefaultUnique();
$request->index('00__test_file')
		->defaultField('title')
		//remove unique field for this index
		->uniqueField();
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Index\GetList
 * Get list of index on the OpenSearchServer instance
 */
echo '<hr/><h2>Index\GetList</h2>';
$request = new OpenSearchServer\Index\GetList();
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Index\Exists
 * Check if an index exists
 */
echo '<hr/><h2>Index\Exists</h2>';
$request = new OpenSearchServer\Index\Exists();
$request->index('00__test');
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
echo '<hr/><h2>Search\Field\Search</h2>';
$request = new OpenSearchServer\Search\Field\Search();
$request->index('00__test_file')
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
echo '<hr/><h2>Search\Field\Put</h2>';
//build request
$request = new OpenSearchServer\Search\Field\Put();
$request->index('00__test_file')
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
echo '<pre style="word-wrap: break-word;">'; print_r($request->getData()); echo '</pre>';
//send request
$response = $oss_api->submit($request);
//dump response
var_dump($response);

/**
 * ## SearchTemplate\GetList
 * Get list of existing search templates
 */
echo '<hr/><h2>SearchTemplate\GetList</h2>';
$request = new OpenSearchServer\SearchTemplate\GetList();
$request->index('00__test_file');
$response = $oss_api->submit($request);
print_r($response);

/**
 * ## SearchTemplate\Get
 * Get a search template
 */
echo '<hr/><h2>SearchTemplate\Get</h2>';
$request = new OpenSearchServer\SearchTemplate\Get();
$request->index('00__test_file')
		->name('new_template');
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## SearchTemplate\Delete
 * Delete a search template
 */
echo '<hr/><h2>SearchTemplate\Delete</h2>';
$request = new OpenSearchServer\SearchTemplate\Delete();
$request->index('00__test_file')
		->name('new_template');
$response = $oss_api->submit($request);
var_dump($response);

/**
 * ## Index\Delete
 * Delete index
 */
echo '<hr/><h2>Index\Delete</h2>';
$request = new OpenSearchServer\Index\Delete();
$request->index('00__test_web');
$response = $oss_api->submit($request);
var_dump($response);
$request->index('00__test_file');
$response = $oss_api->submit($request);
var_dump($response);
$request->index('00__test');
$response = $oss_api->submit($request);
var_dump($response);