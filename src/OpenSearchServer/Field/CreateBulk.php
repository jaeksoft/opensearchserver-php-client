<?php
namespace OpenSearchServer\Field;

use OpenSearchServer\RequestJson;

class CreateBulk extends RequestJson
{
	/******************************
	 * INHERITED METHODS OVERRIDDEN
	 ******************************/	
    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return self::METHOD_PUT;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
    	$this->checkPathIndexNeeded();
		if(empty($this->jsonValues) && empty($this->jsonText)) {
    		throw new \Exception('JSON values must be given to the object\'s constructor.');
    	}
        return rawurlencode($this->options['index']).'/field/';
    }
}