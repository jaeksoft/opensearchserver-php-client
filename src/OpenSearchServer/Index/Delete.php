<?php
namespace OpenSearchServer\Index;

use OpenSearchServer\Request;

class Delete extends Request
{
    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return self::METHOD_DELETE;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
    	$this->checkPathIndexNeeded(); 
        return rawurlencode($this->options['index']);
    }
}