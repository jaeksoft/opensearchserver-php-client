<?php
namespace OpenSearchServer\MoreLikeThis;

use OpenSearchServer\Request;

class Get extends Request
{
	/**
	 * Specify the name of more like this template
	 * @param string $name
	 * @return OpenSearchServer\MoreLikeThis\Get
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
        return self::METHOD_GET;
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
        return $this->options['index'].'/morelikethis/template/'.$this->options['name'];
    }
}