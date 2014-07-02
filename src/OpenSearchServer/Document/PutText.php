<?php
namespace OpenSearchServer\Document;

use OpenSearchServer\RequestTextPlain;


class PutText extends RequestTextPlain
{
    
    public function __construct(array $jsonValues = null, $jsonText = null)
    {
    	$this->parameters['field'] = array();
		parent::__construct($jsonValues, $jsonText);
    }

    public function pattern($pattern) {
        $this->parameters['pattern'] = $pattern;
        return $this;
    }

    public function field($field) {
        $this->parameters['field'] = array_unique($this->parameters['field']);
        array_push($this->parameters['field'], $field);
        return $this;
    }

    public function langpos($pos) {
        $this->parameters['langpos'] = $pos;
        return $this;
    }

    public function charset($charset) {
        $this->parameters['charset'] = $charset;
        return $this;
    }

    public function buffersize($size) {
        $this->parameters['buffersize'] = $size;
        return $this;
    }

    public function data($data) {
        $this->data = $data;
        return $this;
    }
    
    
	/******************************
	 * HELPERS
	 ******************************/
    /**
     * Set several fields
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
    
    public function getData()
    {
        //return values or text if directly set
        if(!empty($this->jsonText)) {
    		return $this->jsonText;
    	} elseif(!empty($this->jsonValues)) {
    		return json_encode($this->jsonValues);
        }
        
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        $this->checkPathIndexNeeded();
        return rawurlencode($this->options['index']).'/document';
    }
}