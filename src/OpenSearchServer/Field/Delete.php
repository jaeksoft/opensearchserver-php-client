<?php
namespace OpenSearchServer\Field;

use OpenSearchServer\Request;

class Delete extends Request
{
	/**
	 * Specify the name of field
	 * @param string $name
	 * @return OpenSearchServer\Field\Create
	 */
	public function name($name) {
		$this->options['name'] = $name;
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
        return rawurlencode($this->options['index']).'/field/'.rawurlencode($this->options['name']);
    }
}