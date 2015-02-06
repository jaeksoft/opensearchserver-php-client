<?php
namespace OpenSearchServer\Parser;

use OpenSearchServer\Request;

//List being a PHP reserved word this class is named GetList
class GetList extends Request
{

    public function __construct(array $jsonValues = null, $jsonText = null) {
        $this->setUrlPrefix('/services/rest/');
        parent::__construct($jsonValues, $jsonText);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return self::METHOD_GET;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return 'parser';
    }
}