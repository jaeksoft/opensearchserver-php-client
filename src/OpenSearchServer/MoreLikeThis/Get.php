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
	public function template($name) {
		$this->options['template'] = $name;
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
    	if(empty($this->options['template'])) {
    		throw new \Exception('Method "template($template)" must be called before submitting request.');
    	}
        return rawurlencode($this->options['index']).'/morelikethis/template/'.rawurlencode($this->options['template']);
    }
}