<?php
namespace OpenSearchServer\Parser\Parse;

use OpenSearchServer\RequestFile;

class Upload extends RequestFile
{
    private $filePath;
    
    public function __construct(array $jsonValues = null, $jsonText = null) {
        $this->setUrlPrefix('/services/rest/');
        parent::__construct($jsonValues, $jsonText);
    }
    
	/**
	 * Specify the name of the parser
	 * @param string $name
	 * @return OpenSearchServer\Parser\Parse\Upload
	 */
	public function name($name) {
		$this->options['parser_name'] = $name;
		return $this;
	}
	
	/**
	 * Add a variable
	 * @param string $name Name of variable
	 * @param string $name value of variable
	 * @return OpenSearchServer\Parser\Parse\Upload
	 */
	public function variable($name, $value) {
	    $this->parameters['p.'.$name] = $value;
	    return $this;
	}

	/**
	 * Set filepath for the file to upload
	 * Use this method if you want to send the request with the standard "submit" function
	 * @param string $fullPath Absolute path to the file to upload
	 * @return OpenSearchServer\Parser\Parse\Upload
	 */
	public function file($fullPath) {
	    $this->jsonText = file_get_contents($fullPath);
	    return $this;
	}
	
	/**
	 * Set filepath for the file to upload
	 * Use this method if you want to send the request with the submitFile function
	 * @param string $fullPath Absolute path to the file to upload
	 * @return OpenSearchServer\Parser\Parse\Upload
	 */
	public function filePath($fullPath) {
	    $this->filePath = $fullPath;
	    return $this;
	}
	
	public function getFilePath() {
	    return $this->filePath;
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