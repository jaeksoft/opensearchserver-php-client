<?php
namespace OpenSearchServer\Crawler\Rest;

use OpenSearchServer\RequestJson;

class Execute extends RequestJson
{
	/**
	 * Specify the name of REST crawler to execute
	 * @param string $name
	 * @return OpenSearchServer\Crawler\Rest\Execute
	 */
	public function name($name) {
		$this->options['name'] = $name;
		return $this;
	}
	
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
    	if(empty($this->options['name'])) {
    		throw new \Exception('Method "name($name)" must be called before submitting request.');
    	}
        return rawurlencode($this->options['index']).'/crawler/rest/'.rawurlencode($this->options['name']).'/run';
    }
}