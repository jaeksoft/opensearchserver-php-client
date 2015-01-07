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
    const LANG_UNDEFINED = 'UNDEFINED';
    const LANG_AR = 'ARABIC';
    const LANG_ZH = 'CHINESE';
    const LANG_DA = 'DANISH';
    const LANG_NL = 'DUTCH';
    const LANG_EN = 'ENGLISH';
    const LANG_FI = 'FINNISH';
    const LANG_FR = 'FRENCH';
    const LANG_DE = 'GERMAN';
    const LANG_HU = 'HUNGARIAN';
    const LANG_IT = 'ITALIAN';
    const LANG_JA = 'JAPANESE';
    const LANG_KO = 'KOREAN';
    const LANG_NO = 'NORWEGIAN';
    const LANG_PL = 'POLISH';
    const LANG_PT = 'PORTUGUESE';
    const LANG_RO = 'ROMANIAN';
    const LANG_RU = 'RUSSIAN';
    const LANG_ES = 'SPANISH';
    const LANG_SV = 'SWEDISH';
    const LANG_TR = 'TURKISH';
    
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
    const RELATIVE_DATE_FILTER = 'RelativeDateFilter';
    const RELATIVE_DATE_FILTER_DATEFORMAT = 'yyyyMMddHHmmss';
    const RELATIVE_DATE_FILTER_UNIT_DAYS = 'days';
    const RELATIVE_DATE_FILTER_UNIT_HOURS = 'hours';
    const RELATIVE_DATE_FILTER_UNIT_MINUTES = 'minutes';
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
     * Types of replications
     */
    const REPL_BACKUP_INDEX = 'BACKUP_INDEX';
    const REPL_MAIN_INDEX = 'MAIN_INDEX';
    const REPL_WEB_CRAWLER_URL_DATABASE = 'WEB_CRAWLER_URL_DATABASE';
    const REPL_FILE_CRAWLER_URI_DATABASE = 'FILE_CRAWLER_URI_DATABASE';
    const REPL_SCHEMA_ONLY = 'SCHEMA_ONLY';
    const REPL_MAIN_DATA_COPY = 'MAIN_DATA_COPY';
    
    
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
	 * JSON text to use directly with this request. If set, this text will be used
	 * even if some values are defined in $data or in $jsonValues.
	 * @var string $jsonText
	 */
    protected $jsonText;
    
    /**
     * Prefix to use in URL. Defaults to /services/rest/index/ but some Request may override it (like Monitor for example)
     * @var string $urlPrefix
     */
    protected $urlPrefix = '/services/rest/index/';
    
    /**
     * Construct an instance of Request 
     * @param array $jsonValues JSON values to send with the request. If set will be used 
     * even if some values are defined later by calling other methods.
     */
    public function __construct(array $jsonValues = null, $jsonText = null)
    {
		$this->setJsonValues($jsonValues);
		$this->setJsonText($jsonText);
    }

    public function setJsonValues($jsonValues) {
    	$this->jsonValues = $jsonValues;
    }
    
    public function setJsonText($jsonText) {
    	$this->jsonText = $jsonText;
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
        if(!empty($this->jsonText)) {
    		return $this->jsonText;
    	}elseif(!empty($this->jsonValues)) {
    		return json_encode($this->jsonValues);
    	} elseif(!empty($this->data)) {
        	return json_encode($this->data);
        }
        return null;
    }
    
    /**
     */
    public function getHeaders()
    {
        return array();
    }
    
    /**
     */
    public function getMethod()
    {
        return '';
    }
    
    /**
     */
    public function getPath()
    {
        return '';
    }
    
    public function getParameters() {
    	return $this->parameters;
    }
    
    
    public function setUrlPrefix($prefix) {
        $this->urlPrefix = $prefix;
    }
    
    public function getUrlPrefix() {
        return $this->urlPrefix;
    }
    
    protected function checkPathIndexNeeded() {
    	if(empty($this->options['index'])) {
    		throw new \Exception('Method "index($indexName)" must be called before submitting request.');
    	}
    }
}