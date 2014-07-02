<?php
namespace OpenSearchServer\Monitor;

use OpenSearchServer\Request;

class Monitor extends Request
{
    public function __construct(array $jsonValues = null, $jsonText = null)
    {
		parent::__construct($jsonValues, $jsonText);
		//this request does not use "/services/rest/index/"
		$this->setUrlPrefix('/services/rest/');
    }
    
    /**
     * Get full monitoring information or only basic one
     * @param boolean $full
     */
    public function full($full = true) {
        $this->parameters['full'] =  ($full) ? 'true' : 'false';
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
        return 'monitor/json';
    }
}