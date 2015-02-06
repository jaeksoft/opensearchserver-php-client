<?php
namespace OpenSearchServer\SearchBatch;

use OpenSearchServer\RequestJson;
use OpenSearchServer\Search;

class SearchBatch extends RequestJson
{	
    public function __construct(array $jsonValues = null, $jsonText = null) {
		$this->data['queries'] = array();
		//default mode
		$this->data['mode'] = 'all';
		parent::__construct($jsonValues, $jsonText);
    }
    
	/**
	 * Mode: first or all
	 * @param string $mode
	 * @return OpenSearchServer\SearchBatch\SearchBatch
	 */
	public function mode($mode) {
		$this->data['mode'] = $mode;
		return $this;
	}
	
	/**
	 * Add one query to the batch
	 * @param Search $query
	 */
	public function addQuery(Search $query) {
	    //add query data
	    $queryArray = json_decode($query->getData(), true);
        //add template if any
	    $template = $query->getTemplate();
	    if(!empty($template)) {
	        $queryArray['template'] = $template;
	        $suffix = 'Template';
	    } else { 
            $suffix = '';	        
	    }
    	//add type of query
    	$type = get_class($query);
	    switch($type) {
	        case 'OpenSearchServer\Search\Field\Search':
    	        $queryArray['type'] = 'SearchField'.$suffix;
        	    break;
	        case 'OpenSearchServer\Search\Pattern\Search':
        	    $queryArray['type'] = 'SearchPattern'.$suffix;
        	    break;
	    }
	    $this->data['queries'][] = $queryArray;
	}
	
	/******************************
	 * ALIAS AND HELPERS
	 ******************************/	
	public function addQueries(array $queries = array()) {
	    foreach($queries as $query) {
	        $this->addQuery($query);
	    }
	}
	
	/******************************
	 * INHERITED METHODS OVERRIDDEN
	 ******************************/	
	
    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return self::METHOD_POST;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
    	$this->checkPathIndexNeeded();
        return rawurlencode($this->options['index']).'/search/batch';
    }
}