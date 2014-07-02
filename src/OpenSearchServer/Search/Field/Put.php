<?php
namespace OpenSearchServer\Search\Field;

use OpenSearchServer\Search\Field\SearchField;

class Put extends SearchField
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
        return rawurlencode($this->options['index']).'/search/field/'.rawurlencode($this->options['template']);
	}
}