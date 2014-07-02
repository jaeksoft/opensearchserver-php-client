<?php
namespace OpenSearchServer\Scheduler;

use OpenSearchServer\Request;

class GetStatus extends Request
{
	/**
	 * Specify the name of scheduler job
	 * @param string $name
	 * @return OpenSearchServer\Scheduler\GetStatus
	 */
	public function name($name) {
		$this->options['scheduler_name'] = $name;
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
        return self::METHOD_GET;
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