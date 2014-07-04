<?php
namespace OpenSearchServer\SpellCheck;

use OpenSearchServer\RequestJson;

class Search extends RequestJson
{
	/**
	 * Specify the name of search template to use
	 * @param string $name
	 * @return OpenSearchServer\SpellCheck\Search
	 */
	public function template($name) {
		$this->options['template'] = $name;
		return $this;
	}
	

	/**
	 * Specify the query
	 * @param string $query
	 * @return OpenSearchServer\SpellCheck\Search
	 */
	public function query($query = NULL) {
		$this->parameters['query'] = $query;
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
    		throw new \Exception('Method "template($name)" must be called before submitting request.');
    	}
        return rawurlencode($this->options['index']).'/spellcheck/'.rawurlencode($this->options['template']);
        
	}
}