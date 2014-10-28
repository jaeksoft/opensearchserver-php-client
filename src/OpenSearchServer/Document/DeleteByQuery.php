<?php
namespace OpenSearchServer\Document;

use OpenSearchServer\RequestJson;
use OpenSearchServer\Document\Document;

class DeleteByQuery extends RequestJson
{
	/**
	 * Name of a query template to use for deletion
	 * @param string $template
	 */
	public function template($template) {
		$this->parameters['template'] = $template;
		return $this;
	}
	
	/**
	 * Query pattern to select documents to delete
	 * @param string $pattern
	 */
	public function query($pattern) {
		$this->parameters['query'] = $pattern;
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
		if(empty($this->parameters['template']) && empty($this->parameters['query'])) {
    		throw new \Exception('Method "template($queryTemplate)" or method "query($pattern)" must be called before submitting request.');
    	}
        return rawurlencode($this->options['index']).'/document';
	}
}