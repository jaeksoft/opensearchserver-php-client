<?php
namespace OpenSearchServer\Response;

use Buzz\Message\Response as BuzzResponse;

class SearchResult extends ResponseResults
{
    protected $rows;
    protected $start;
    protected $numFound;
    protected $time;
    protected $collapsedDocCount;
    protected $maxScore;
    protected $facets = array();
    
    
    public function __construct(BuzzResponse $response, \OpenSearchServer\Request $request)
    {
		parent::__construct($response, $request);

		//handle facets
		if(!empty($this->jsonValues->facets)) {
		    $this->buildFacetsArray($this->jsonValues->facets);
		}
		
        $this->query = (!empty($this->jsonValues->query)) ? $this->jsonValues->query : null;
        $this->rows = (!empty($this->jsonValues->rows)) ? $this->jsonValues->rows : null;
        $this->start = (!empty($this->jsonValues->start)) ? $this->jsonValues->start : null;
        $this->numFound = (!empty($this->jsonValues->numFound)) ? $this->jsonValues->numFound : null;
        $this->time = (!empty($this->jsonValues->time)) ? $this->jsonValues->time : null;
        $this->collapsedDocCount = (!empty($this->jsonValues->collapsedDocCount)) ? $this->jsonValues->collapsedDocCount : null;
        $this->maxScore = (!empty($this->jsonValues->maxScore)) ? $this->jsonValues->maxScore : null;
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
        return max(0, $this->numFound);
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
    
    /**
     * Return facets array 
     */
    public function getFacets() {
        return $this->facets;
    }
    
    
    /**
     * Build array of facets based on JSON results
     * @param array $facetsJson Array of JSON values for facets
     */
    private function buildFacetsArray($facetsJson) {
        foreach($facetsJson as $facetObj) {
            //build array of term with <term> => <number of occurences>
            $terms = array();
            foreach($facetObj->terms as $termObj) {
                $terms[$termObj->term] = $termObj->count;
            }
            $this->facets[$facetObj->fieldName] = $terms;
        }
    }
}