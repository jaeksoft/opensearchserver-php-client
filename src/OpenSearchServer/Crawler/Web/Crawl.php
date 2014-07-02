<?php
namespace OpenSearchServer\Crawler\Web;

use OpenSearchServer\Request;

class Start extends Request
{
	public function url($url) {
		$this->parameters['url'] = $url;
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
        return self::METHOD_DELETE;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
    	$this->checkPathIndexNeeded();
    	if(empty($this->parameters['url'])) {
    		throw new \Exception('Method "url($url)" must be called before submitting request.');
    	}
        return rawurlencode($this->options['index']).'/crawler/web/crawl';
    }
}