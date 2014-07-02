<?php
namespace OpenSearchServer\Synonyms;

use OpenSearchServer\RequestTextPlain;

class Create extends RequestTextPlain
{
	/**
	 * Specify the name of synonyms list to create/update
	 * @param string $name
	 * @return OpenSearchServer\Synonyms\Delete
	 */
	public function name($name) {
		$this->options['name'] = $name;
		return $this;
	}
	
	/**
	 * Add synonyms
	 * @param string/array $list One array entry for each group of synonyms. The synonyms within a group are separated by commas.
	 * 			Example: couch,sofa,divan
	 */
    public function addSynonyms($list) {
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
        return rawurlencode($this->options['index']).'/synonyms/'.rawurlencode($this->options['name']);
    }
}