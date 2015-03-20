<?php
namespace OpenSearchServer\Index;

use OpenSearchServer\RequestJson;

class Get extends RequestJson
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
        return rawurlencode($this->options['index']);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
    	return array('info' => 'true');
    }
}