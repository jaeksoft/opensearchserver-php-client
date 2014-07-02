<?php
namespace OpenSearchServer\Search\Pattern;

use OpenSearchServer\Search\Pattern\SearchPattern;

class Put extends SearchPattern
{
	
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
		if(empty($this->options['template'])) {
    		throw new \Exception('Method "template($templateName)" must be called before submitting request.');
    	}
        return rawurlencode($this->options['index']).'/search/pattern/'.rawurlencode($this->options['template']);
	}
}