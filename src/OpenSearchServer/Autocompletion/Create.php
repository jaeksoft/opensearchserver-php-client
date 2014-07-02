<?php
namespace OpenSearchServer\Autocompletion;

use OpenSearchServer\RequestJson;

class Create extends RequestJson
{
    public function __construct(array $jsonValues = null, $jsonText = null)
    {
    	$this->parameters['rows'] = 10;
    	$this->parameters['field'] = array();
		parent::__construct($jsonValues, $jsonText);
    }
    
	/**
	 * Specify the name of autocompletion
	 * @param string $name
	 * @return OpenSearchServer\Autocompletion\Create
	 */
	public function name($name) {
		$this->options['name'] = $name;
		return $this;
	}

	/**
	 * Specify one field to use
	 * @param string $name
	 * @return OpenSearchServer\Autocompletion\Create
	 */
	public function field($field) {
	    array_push($this->parameters['field'], $field);
	    $this->parameters['field'] = array_unique($this->parameters['field']);
		return $this;
	}
	
	/**
	 * Specify number of suggestions this autocompletion will return
	 * @param int $rows
	 * @return OpenSearchServer\Autocompletion\Create
	 */
	public function rows($rows) {
		$this->parameters['rows']= $rows;
		return $this;
	}
	
	
	/******************************
	 * HELPERS
	 ******************************/	
	/**
	 * Set several fields to base autocompletion on
	 * @param array $fields
	 */
	public function fields($fields) {
		foreach($fields as $field) {
			$this->field($field);
		}
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
    	if(empty($this->parameters['field'])) {
    		throw new \Exception('Method "field($fieldname)" must be called before submitting request.');
    	}
    	if(empty($this->options['name'])) {
    		throw new \Exception('Method "name($name)" must be called before submitting request.');
    	}
        return rawurlencode($this->options['index']).'/autocompletion/'.rawurlencode($this->options['name']);
    }
}