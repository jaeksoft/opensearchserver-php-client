<?php
namespace OpenSearchServer\Parser\Parse;

use OpenSearchServer\RequestFile;

class DetectType extends RequestFile
{
    private $filePath;
    
    public function __construct(array $jsonValues = null, $jsonText = null) {
        $this->setUrlPrefix('/services/rest/');
        parent::__construct($jsonValues, $jsonText);
    }

	/**
	 * Specify the name of the file to parser
	 * @param string $name
	 * @return OpenSearchServer\Parser\Parse\DetectType
	 */
	public function name($name) {
		$this->parameters['name'] = $name;
		return $this;
	}
	
	/**
	 * Specify the type of the file to parser
	 * @param string $name
	 * @return OpenSearchServer\Parser\Parse\DetectType
	 */
	public function type($type) {
		$this->parameters['type'] = $type;
		return $this;
	}
	
	/**
	 * Add a variable
	 * @param string $name Name of variable
	 * @param string $name value of variable
	 * @return OpenSearchServer\Parser\Parse\DetectType
	 */
	public function variable($name, $value) {
	    $this->parameters['p.'.$name] = $value;
	    return $this;
	}

	/**
	 * Set filepath for the file to upload
	 * Use this method if you want to send the request with the standard "submit" function
	 * @param string $fullPath Absolute path to the file to upload
	 * @return OpenSearchServer\Parser\Parse\DetectType
	 */
	public function file($fullPath) {
	    $this->jsonText = file_get_contents($fullPath);
	    return $this;
	}
	
	/**
	 * Set filepath for the file to upload
	 * Use this method if you want to send the request with the submitFile function
	 * @param string $fullPath Absolute path to the file to upload
	 * @return OpenSearchServer\Parser\Parse\DetectType
	 */
	public function filePath($fullPath) {
	    $this->filePath = $fullPath;
	    return $this;
	}

	/**
	 * Set filepath for the file to parse
	 * @param string $fullPath Absolute path to the file to parse on the server
	 * @return OpenSearchServer\Parser\Parse\DetectType
	 */
	public function path($fullPath) {
	    $this->parameters['path'] = $fullPath;
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
        return 'parser';
    }
}