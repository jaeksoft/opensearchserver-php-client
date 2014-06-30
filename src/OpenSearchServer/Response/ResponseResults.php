<?php
namespace OpenSearchServer\Response;

use Buzz\Message\Response as BuzzResponse;

abstract class ResponseResults extends ResponseIterable
{
    protected $query;

    public function __construct(BuzzResponse $response, \OpenSearchServer\Request $request)
    {
        parent::__construct($response, $request);
        //build array of results objects
        if(!empty($this->jsonValues->documents)) {
            foreach($this->jsonValues->documents as $result) {
                $this->values[] = new Result($result);
            }
        }
    }

    public function getResults() {
        return $this->values;
    }
    
    public function getQuery() {
        return $this->query;
    }
    /**
     * Return number of results in this results set
     */
    public function getNumberOfResults() {
        return count($this->values);
    }
}