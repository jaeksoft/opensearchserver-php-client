<?php
namespace OpenSearchServer\Analyzer;

use OpenSearchServer\RequestJson;
use OpenSearchServer\Request;

class Create extends RequestJson
{
    public function __construct(array $jsonValues = null, $jsonText = null) {
        $this->lang(Request::LANG_UNDEFINED);
        parent::__construct($jsonValues, $jsonText);
    }
    
	/**
	 * Specify the name of analyzer
	 * @param string $name
	 * @return OpenSearchServer\Analyzer\Create
	 */
	public function name($name) {
		$this->options['name'] = $name;
		return $this;
	}
	
	/**
	 * Specify the lang of analyzer
	 * @param string $lang
	 * @return OpenSearchServer\Analyzer\Create
	 */
	public function lang($lang) {
		$this->options['lang'] = $lang;
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
    	$this->checkPathIndexNeeded();
		if(empty($this->options['name'])) {
    		throw new \Exception('Method "name($name)" must be called before submitting request.');
    	}
        return rawurlencode($this->options['index']).'/analyzer/'.rawurlencode($this->options['name']).'/lang/'.rawurlencode($this->options['lang']);
    }
}