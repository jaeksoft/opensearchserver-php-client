<?php
namespace OpenSearchServer\Replication;

use OpenSearchServer\Request;

class GetList extends Request
{
	/******************************
	 * INHERITED METHODS OVERRIDDEN
	 ******************************/
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
    	return rawurlencode($this->options['index']).'/replication';
    }
}