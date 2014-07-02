<?php
namespace OpenSearchServer\SearchTemplate;

use OpenSearchServer\Request;

//List being a PHP reserved word this class is named GetList
class GetList extends Request
{
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
    	$this->checkPathIndexNeeded();
        return rawurlencode($this->options['index']).'/search/template';
    }
}