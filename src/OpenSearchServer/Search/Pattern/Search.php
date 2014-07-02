<?php
namespace OpenSearchServer\Search\Pattern;

use OpenSearchServer\Search\Pattern\SearchPattern;

class Search extends SearchPattern
{

	/******************************
	 * INHERITED METHODS OVERRIDDEN
	 ******************************/
	/**
	 * {@inheritdoc}
	 */
	public function getMethod()
	{
		return self::METHOD_POST;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPath()
	{
    	$this->checkPathIndexNeeded();
        $path = rawurlencode($this->options['index']).'/search/pattern';
        return (!empty($this->options['template'])) ? $path.'/'.rawurlencode($this->options['template']) : $path;
	}
}