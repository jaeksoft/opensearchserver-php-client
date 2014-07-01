<?php
namespace OpenSearchServer\Crawler\File;

use OpenSearchServer\Request;

class RequestFileCrawler extends Request
{
    public function __construct(array $jsonValues = null)
    {
        //this API does not start with same prefix
        $this->setUrlPrefix('/services/rest/');
        parent::__construct($jsonValues);
    }
}