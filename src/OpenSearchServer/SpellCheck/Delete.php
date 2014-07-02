<?php
namespace OpenSearchServer\SpellCheck;

use OpenSearchServer\RequestJson;

class Delete extends RequestJson
{
	/**
	 * Specify the name of search template to delete
	 * @param string $name
	 * @return OpenSearchServer\SpellCheck\Delete
	 */
	public function template($name) {
		$this->options['template'] = $name;
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
    	if(empty($this->options['template'])) {
    		throw new \Exception('Method "template($name)" must be called before submitting request.');
    	}
        return rawurlencode($this->options['index']).'/spellcheck/'.rawurlencode($this->options['template']);
    }
}