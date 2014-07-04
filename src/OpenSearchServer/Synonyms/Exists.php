<?php
namespace OpenSearchServer\Synonyms;

use OpenSearchServer\Request;

class Exists extends Request
{
	/**
	 * Specify the name of synonyms list to test
	 * @param string $name
	 * @return OpenSearchServer\Synonyms\Exists
	 */
	public function name($name) {
		$this->options['name'] = $name;
		return $this;
	}

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return self::METHOD_HEAD;
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