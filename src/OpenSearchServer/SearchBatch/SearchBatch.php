<?php
namespace OpenSearchServer\SearchBatch;

use OpenSearchServer\RequestJson;
use OpenSearchServer\Search;

class SearchBatch extends RequestJson
{	
    const ACTION_STOP_IF_FOUND = 'STOP_IF_FOUND';
    const ACTION_CONTINUE = 'CONTINUE';
    
    // Mode first: as soon as a query returns result the batch stops
    const MODE_FIRST = 'first';
    // Mode all: all queries are executed
    const MODE_ALL = 'all';
    // Mode manual: each query can be configured with an action: CONTINUE or STOP_IF_FOUND
    const MODE_MANUAL = 'manual';
    
    public function __construct(array $jsonValues = null, $jsonText = null) {
		$this->data['queries'] = array();
		//default mode
		$this->data['mode'] = self::MODE_ALL;
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
	 * @param String $modeManualAction Batch action to use for the query, if mode is "manual"
	 */
	public function addQuery(Search $query, $modeManualAction = null) {
        if($modeManualAction && $this->data['mode'] != 'manual') {
            throw new \InvalidArgumentException('Query mode must be set to "manual" before using a batchAction for queries.');
        }
	    //add query data
	    $queryArray = json_decode($query->getData(), true);
	    //add batchAction if any
	    if($modeManualAction) {
	        $queryArray['batchAction'] = $modeManualAction;
	    }
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
	/**
	 * Call query() for each item in the array
	 * @param array $queries An array of array: each sub array contains one required item, the query, and a second optionnal item,
	 * the batchAction to use for this query if mode is "manual"
	 */
	public function addQueries(array $queries = array()) {
	    foreach($queries as $queryInfo) {
	        $batchAction = (isset($queryInfo[1])) ? $queryInfo[1] : null;
	        $this->addQuery($queryInfo[0], $batchAction);
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