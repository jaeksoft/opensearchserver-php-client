<?php
namespace OpenSearchServer\Autocompletion;

use OpenSearchServer\Request;

class Query extends Request
{
	/**
	 * Specify the name of autocompletion
	 * @param string $name
	 * @return OpenSearchServer\Autocompletion\Query
	 */
	public function name($name) {
		$this->options['name'] = $name;
		return $this;
	}

	/**
	 * Query to autocomplete
	 * @param string $name
	 * @return OpenSearchServer\Autocompletion\Query
	 */
	public function query($query) {
		$this->parameters['prefix'] = $query;
		return $this;
	}
	

	/**
	 * Specify number of suggestions to return
	 * @param int $rows
	 * @return OpenSearchServer\Autocompletion\Query
	 */
	public function rows($rows) {
		$this->parameters['rows']= $rows;
		return $this;
	}
	
	/******************************
	 * HELPERS AND ALIASES
	 ******************************/
	/**
	 * Alias to query()
	 * @see query()
	 */
	public function prefix($query) {
		return $this->query($query);
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
        return rawurlencode($this->options['index']).'/autocompletion/'.rawurlencode($this->options['name']);
    }
}