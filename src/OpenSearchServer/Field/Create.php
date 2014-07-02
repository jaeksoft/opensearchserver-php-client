<?php
namespace OpenSearchServer\Field;

use OpenSearchServer\RequestJson;

class Create extends RequestJson
{
	/**
	 * Specify the name of field
	 * @param string $name
	 * @return OpenSearchServer\Field\Create
	 */
	public function name($name) {
		$this->options['name'] = $name;
		//$this->data['name'] = $name;
		return $this;
	}
	
	/**
	 * Specify the name of analyzer to use on that field
	 * @param string $analyzer
	 * @return OpenSearchServer\Field\Create
	 */
	public function analyzer($analyzer = null) {
		$this->data['analyzer'] = $analyzer;
		return $this;
	}

	/**
	 * Tell whether this field must be indexed or not
	 * @param boolean $indexed
	 * @return OpenSearchServer\Field\Create
	 */
	public function indexed($indexed) {
		if($indexed === true or $indexed === false) {
			$this->data['indexed'] = ($indexed) ? 'YES' : 'NO';	
		} else {
			$this->data['indexed'] = $indexed;
		}
		return $this;
	}
	
	/**
	 * Tell whether this field must be stored or not
	 * @param string $stored
	 * @return OpenSearchServer\Field\Create
	 */
	public function stored($stored) {
		$this->data['stored'] = (string)$stored;
		return $this;
	}
	
	
	/**
	 * Tell whether this field must have term vector or not
	 * @param string $termVector
	 * @return OpenSearchServer\Field\Create
	 */
	public function termVector($termVector) {
		$this->data['termVector'] = (string)$termVector;
		return $this;
	}
	
	/**
	 * Set "copy of" fields
	 * @param array $copyOf
	 * @return OpenSearchServer\Field\Create
	 */
	public function copyOf($copyOf) {
		$this->data['copyOf'] = (array)$copyOf;
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
		if(empty($this->options['name'])) {
    		throw new \Exception('Method "name($name)" must be called before submitting request.');
    	}
        return rawurlencode($this->options['index']).'/field/'.rawurlencode($this->options['name']);
    }
}