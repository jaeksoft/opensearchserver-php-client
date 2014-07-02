<?php
namespace OpenSearchServer\Document;

use OpenSearchServer\RequestJson;
use OpenSearchServer\Document\Document;

class Delete extends RequestJson
{
	/**
	 * Name of field on which base deletion
	 * @param string $fieldname
	 */
	public function field($fieldname) {
		$this->options['field'] = $fieldname;
		return $this;
	}
	
	/**
	 * Add a value to delete document
	 * @param string $value
	 */
	public function value($value) {
		$this->data[] = $value;
		return $this;
	}
	
	/******************************
	 *     HELPER AND ALIASES
	 ******************************/
	/**
	 * Add several values
	 * @param array $values Array of values
	 */
	public function values($values) {
		foreach((array)$values as $value) {
			$this->value($value);
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
		return self::METHOD_DELETE;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getPath()
	{
    	$this->checkPathIndexNeeded();
		if(empty($this->options['field'])) {
    		throw new \Exception('Method "field($fieldname)" must be called before submitting request.');
    	}
        return rawurlencode($this->options['index']).'/document/'.rawurlencode($this->options['field']);
	}
}