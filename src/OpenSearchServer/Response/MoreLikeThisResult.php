<?php
namespace OpenSearchServer\Response;

use Buzz\Message\Response as BuzzResponse;

class MoreLikeThisResult extends ResponseResults
{
    public function __construct(BuzzResponse $response, \OpenSearchServer\Request $request)
    {
		parent::__construct($response, $request);
        $this->query = (!empty($this->jsonValues->query)) ? $this->jsonValues->query : null;
    }
}