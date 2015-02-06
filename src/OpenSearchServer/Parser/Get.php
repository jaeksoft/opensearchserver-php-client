<?php
namespace OpenSearchServer\Parser;

use OpenSearchServer\Request;

class Get extends Request
{
    public function __construct(array $jsonValues = null, $jsonText = null) {
        $this->setUrlPrefix('/services/rest/');
        parent::__construct($jsonValues, $jsonText);
    }
    
	/**
	 * Specify the name of the parser
	 * @param string $name
	 * @return OpenSearchServer\Parser\Get
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
        return self::METHOD_GET;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
    	if(empty($this->options['name'])) {
    		throw new \Exception('Method "name($name)" must be called before submitting request.');
    	}
    	return 'parser/'.rawurlencode($this->options['name']);
    }
}