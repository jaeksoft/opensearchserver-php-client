<?php
namespace OpenSearchServer;

class Request
{
    /** 
     * Request methods
     */
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_GET     = 'GET';
    const METHOD_HEAD    = 'HEAD';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';
    const METHOD_DELETE  = 'DELETE';
    const METHOD_PATCH   = 'PATCH';
    
    /**
     * Languages
     */
    const LANG_FR = 'FRENCH';
    const LANG_EN = 'ENGLISH';
    const LANG_DE = 'GERMAN';
    
    /** 
     * Index templates names
     */
    const TEMPLATE_WEB_CRAWLER = 'WEB_CRAWLER';
    const TEMPLATE_FILE_CRAWLER = 'FILE_CRAWLER';

    /**
     * Search constants
     */
    const QUERY_FILTER = 'QueryFilter';
    const GEO_FILTER = 'GeoFilter';
    const GEO_FILTER_SQUARED = 'SQUARED';
    const GEO_FILTER_KILOMETERS = 'KILOMETERS';
    const GEO_FILTER_MILES = 'MILES';
    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';
    const OPERATOR_OR = 'OR';
    const OPERATOR_AND = 'AND';
    const COLLAPSING_MODE_OFF = 'OFF';
    const COLLAPSING_MODE_ADJACENT = 'ADJACENT';
    const COLLAPSING_MODE_CLUSTER = 'CLUSTER';
    const COLLAPSING_TYPE_OPTIMIZED = 'OPTIMIZED';
    const COLLAPSING_TYPE_FULL = 'FULL';
    const SCORING_FIELD_ORDER = 'FIELD_ORDER';
    const SCORING_DISTANCE = 'DISTANCE';
    const SNIPPET_NO_FRAGMENTER = 'NO';
    const SNIPPET_SENTENCE_FRAGMENTER = 'SENTENCE';
    
    /**
     * Various options for this request
     * Can have different roles, for example can be used when building full path
     * @var array options Various options
     */
    protected $options = array();
    
    /**
     * Data of the request. Array of values that will be transformed to JSON payload.
     * @var array data JSON values to send with request
     */
	protected $data = array();
	
	/**
	 * Query parameters to use when building URL
	 * @var array parameters URL parameters
	 */
	protected $parameters = array();
	
	/**
	 * JSON values to use directly with this request. If set those values will be used
	 * even if some values are defined in $data.
	 * @var array $jsonValues
	 */
    protected $jsonValues;
    
    /**
     * Construct an instance of Request 
     * @param array $jsonValues JSON values to send with the request. If set will be used 
     * even if some values are defined later by calling other methods.
     */
    public function __construct(array $jsonValues = null)
    {
		$this->setJsonValues($jsonValues);
    }
    
    public function setJsonValues($jsonValues) {
    	$this->jsonValues = $jsonValues;
    }
    
    /**
     * Set name of index to work on
     * @param string $value
     */
    public function index($value) {
    	$this->options['index'] = $value;
    	return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
    	if(!empty($this->jsonValues)) {
    		return json_encode($this->jsonValues);
    	} elseif(!empty($this->data)) {
        	return json_encode($this->data);
        }
        return '';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return array();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return '';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return '';
    }
    
    public function getParameters() {
    	return $this->parameters;
    }
    
    protected function checkPathIndexNeeded() {
    	if(empty($this->options['index'])) {
    		throw new \Exception('Method "index($indexName)" must be called before submitting request.');
    	}
    }
}