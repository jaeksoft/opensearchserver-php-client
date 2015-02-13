<?php
namespace OpenSearchServer\Crawler\Web\Patterns;

use OpenSearchServer\RequestJson;

class SetStatus extends RequestJson
{
	/**
	 * Enable or Disable the "Pattern list" control when crawling 
	 */
	public function inclusion($status = true) {
		$this->parameters['inclusion'] = ($status) ? 'true' : 'false';
		return $this;
	}
	
	/**
	 * Enable or Disable the "Exclusion list" control when crawling 
	 */
	public function exclusion($status = true) {
		$this->parameters['exclusion'] = ($status) ? 'true' : 'false';
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
    	if(empty($this->parameters['inclusion']) && empty($this->parameters['exclusion'])) {
    		throw new \Exception('Method "inclusion($status)" or "exclusion($status)" must be called before submitting request.');
    	}
        return rawurlencode($this->options['index']).'/crawler/web/patterns/status';
    }
}