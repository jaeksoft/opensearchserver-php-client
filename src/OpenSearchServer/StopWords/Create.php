<?php
namespace OpenSearchServer\StopWords;

use OpenSearchServer\RequestTextPlain;

class Create extends RequestTextPlain
{
	/**
	 * Specify the name of stopwords list to create/update
	 * @param string $name
	 * @return OpenSearchServer\StopWords\Delete
	 */
	public function name($name) {
		$this->options['name'] = $name;
		return $this;
	}
	
	/**
	 * Add synonyms
	 * @param string/array $list One array entry for each stop word
	 */
    public function addStopWords($list) {
		$this->data = array_unique(array_merge($this->data, (array)$list));
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
    public function getData()
    {
        //return values or text if directly set
        if(!empty($this->jsonText)) {
    		return $this->jsonText;
    	} elseif(!empty($this->jsonValues)) {
    		return json_encode($this->jsonValues);
        }
        
    	if(!empty($this->data)) {
        	return implode("\n", $this->data);
        }
        return null;
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
        return rawurlencode($this->options['index']).'/stopwords/'.rawurlencode($this->options['name']);
    }
}