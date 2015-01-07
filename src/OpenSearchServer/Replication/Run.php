<?php
namespace OpenSearchServer\Replication;

use OpenSearchServer\Request;

class Run extends Request
{
	/**
	 * Specify the name of the replication
	 * @param string $name
	 * @return OpenSearchServer\Replication\Get
	 */
	public function name($name) {
		$this->parameters['name'] = $name;
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
    	if(empty($this->parameters['name'])) {
    		throw new \Exception('Method "name($name)" must be called before submitting request.');
    	}
    	return rawurlencode($this->options['index']).'/replication/run';
    }
}