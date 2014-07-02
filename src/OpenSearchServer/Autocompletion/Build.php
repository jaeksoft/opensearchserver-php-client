<?php
namespace OpenSearchServer\Autocompletion;

use OpenSearchServer\RequestJson;

class Build extends RequestJson
{
	/**
	 * Specify the name of autocompletion
	 * @param string $name
	 * @return OpenSearchServer\Autocompletion\Create
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
        return $this->options['index'].'/autocompletion/'.$this->options['name'];
    }
}