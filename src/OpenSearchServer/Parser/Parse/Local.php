<?php
namespace OpenSearchServer\Parser\Parse;

use OpenSearchServer\RequestJson;

class Local extends RequestJson
{
    
    public function __construct(array $jsonValues = null, $jsonText = null) {
        $this->setUrlPrefix('/services/rest/');
        parent::__construct($jsonValues, $jsonText);
    }
    
	/**
	 * Specify the name of the parser
	 * @param string $name
	 * @return OpenSearchServer\Parser\Parse\Local
	 */
	public function name($name) {
		$this->options['parser_name'] = $name;
		return $this;
	}
	
	/**
	 * Add a variable
	 * @param string $name Name of variable
	 * @param string $name value of variable
	 * @return OpenSearchServer\Parser\Parse\Local
	 */
	public function variable($name, $value) {
	    $this->parameters['p.'.$name] = $value;
	    return $this;
	}
	
	/**
	 * Set filepath for the file to parse
	 * @param string $fullPath Absolute path to the file to parse on the server
	 * @return OpenSearchServer\Parser\Parse\Local
	 */
	public function file($fullPath) {
	    $this->parameters['path'] = $fullPath;
	    return $this;
	}
	
	/******************************
	 * HELPER METHOD
	 ******************************/
	public function variables(array $variables) {
	    foreach($variables as $name => $value) {
	        $this->variable($name, $value);
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
    
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
    	if(empty($this->options['parser_name'])) {
    		throw new \Exception('Method "name($name)" must be called before submitting request.');
    	}
        return 'parser/'.rawurlencode($this->options['parser_name']);
    }
}