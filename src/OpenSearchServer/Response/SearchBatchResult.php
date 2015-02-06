<?php
namespace OpenSearchServer\Response;

use Buzz\Message\Response as BuzzResponse;

class SearchBatchResult extends ResponseResults
{
    private $results = array();
    
    public function __construct(BuzzResponse $response, \OpenSearchServer\Request $request) {
		parent::__construct($response, $request);
		//construct a SearchResult for each set of results
		foreach($this->jsonValues as $result) {
		    //create fake BuzzResponse to create SearchResult
		    $responseBuilt = new BuzzResponse();
		    $responseBuilt->setContent(json_encode($result));
		    $this->results[] = new \OpenSearchServer\Response\SearchResult($responseBuilt, $request);
		} 
    }
    
    /**
     * Return number of results set (even empty ones)
     */
    public function getNumberOfQueriesWithResult() {
        return count($this->jsonValues);
    }

    /**
     * Return all results for all queries
     */
    public function getResults() {
        return $this->results;
    }
    
    /**
     * Return results for one query
     * Position starts at 0
     * @param int $position Position of query
     */
    public function getResultsByPosition($position) {
        return ($this->results[$position]) ? $this->results[$position] : null;
    }
    
}