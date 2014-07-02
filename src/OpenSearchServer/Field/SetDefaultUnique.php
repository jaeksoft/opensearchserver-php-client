<?php
namespace OpenSearchServer\Field;

use OpenSearchServer\Request;

class SetDefaultUnique extends Request
{
	/**
	 * Specify the name of default field.
	 * To reset the field, just pass an empty string.
	 * @param string $default
	 * @return OpenSearchServer\Field\Create
	 */
	public function defaultField($default = null) {
		$this->parameters['default'] = (string)$default;
		return $this;
	}
	
	/**
	 * Specify the name of unique field.
	 * To reset the field, just pass an empty string.
	 * @param string $fieldName
	 * @return OpenSearchServer\Field\Create
	 */
	public function uniqueField($unique = null) {
		$this->parameters['unique'] = (string)$unique;
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
        return self::METHOD_POST;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
    	$this->checkPathIndexNeeded();
    	if(empty($this->parameters['default']) && empty($this->parameters['unique'])) {
    		throw new \Exception('Method "defaultField($fieldName)" or "uniqueField($fieldName)" must be called before submitting request.');
    	}
        return rawurlencode($this->options['index']).'/field';
        
    }
}