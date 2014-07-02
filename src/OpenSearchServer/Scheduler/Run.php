<?php
namespace OpenSearchServer\Scheduler;

use OpenSearchServer\RequestJson;

class Run extends RequestJson
{
	/**
	 * Specify the name of scheduler job
	 * @param string $name
	 * @return OpenSearchServer\Scheduler\Run
	 */
	public function name($name) {
		$this->options['scheduler_name'] = $name;
		return $this;
	}
	
	/**
	 * Add a variable
	 * @param string $name Name of variable
	 * @param string $name value of variable
	 * @return OpenSearchServer\Scheduler\Run
	 */
	public function variable($name, $value) {
	    $this->data[$name] = $value;
	    return $this;
	}
	
	
	/******************************
	 * HELPER METHOD
	 ******************************/
	public function variables(array $variables) {
	    foreach($variables as $name => $value) {
	        $this->variable($name, $value);
	    }
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
    	if(empty($this->options['scheduler_name'])) {
    		throw new \Exception('Method "name($name)" must be called before submitting request.');
    	}
        return rawurlencode($this->options['index']).'/scheduler/'.rawurlencode($this->options['scheduler_name']).'/run';
    }
}