<?php
namespace OpenSearchServer\Crawler\Web;

use OpenSearchServer\Request;

class Start extends Request
{
	/******************************
	 * INHERITED METHODS OVERRIDDEN
	 ******************************/	
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
        return $this->options['index'].'/crawler/web/run';
    }
}