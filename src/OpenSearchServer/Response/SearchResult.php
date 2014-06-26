<?php
namespace OpenSearchServer\Response;

use Buzz\Message\Response as BuzzResponse;

class SearchResult extends ResponseIterable
{
    protected $position;
    
    protected $query;
    protected $rows;
    protected $start;
    protected $numFound;
    protected $time;
    protected $collapsedDocCount;
    protected $maxScore;
    
    
    public function __construct(BuzzResponse $response, \OpenSearchServer\Request $request)
    {
		parent::__construct($response, $request);
        //build array of results objects
		if(!empty($this->jsonValues->documents)) {
		    foreach($this->jsonValues->documents as $result) {
		        $this->values[] = new Result($result);
		    }
		}
		
        $this->query = (!empty($this->jsonValues->query)) ? $this->jsonValues->query : null;
        $this->rows = (!empty($this->jsonValues->rows)) ? $this->jsonValues->rows : null;
        $this->start = (!empty($this->jsonValues->start)) ? $this->jsonValues->start : null;
        $this->numFound = (!empty($this->jsonValues->numFound)) ? $this->jsonValues->numFound : null;
        $this->time = (!empty($this->jsonValues->time)) ? $this->jsonValues->time : null;
        $this->collapsedDocCount = (!empty($this->jsonValues->collapsedDocCount)) ? $this->jsonValues->collapsedDocCount : null;
        $this->maxScore = (!empty($this->jsonValues->maxScore)) ? $this->jsonValues->maxScore : null;
    }
    
    public function getResults() {
        return $this->values;
    }

    public function getQuery() {
        return $this->query;
    }
    
    public function getRows() {
        return $this->rows;
    }
    
    public function getStart() {
        return $this->start;
    }
    
    /** 
     * Return total number of results found in index for this query
     */
    public function getTotalNumberFound() {
        return $this->numFound;
    }
    
    /**
     * Return query duration, in ms
     */
    public function getTime() {
        return $this->time;
    }

    /**
     * Return number of total collapsed docs 
     */
    public function getCollapsedDocCount() {
        return $this->collapsedDocCount;
    }
    
    /**
     * Return max score in this results set
     */
    public function getMaxScore() {
        return $this->maxScore;
    }
    
    /**
     * Return number of results in this results set
     */
    public function getNumberOfResults() {
        return count($this->values);
    }
    
    
}