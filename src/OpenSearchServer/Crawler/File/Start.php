<?php
namespace OpenSearchServer\Crawler\File;

use OpenSearchServer\Crawler\File\RequestFileCrawler;

class Start extends RequestFileCrawler
{
    
    public function __construct(array $jsonValues = null)
    {
    	$this->options['run'] = 'forever';
		parent::__construct($jsonValues);
    }
    
    /**
     * Start crawler for running once or forever
     * @param string $run once|forever
     */
    public function run($run) {
        $this->options['run'] = $run;
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
    	$this->checkPathIndexNeeded();
        return 'crawler/file/run/'.$this->options['run'].'/'.$this->options['index'].'/json';
    }
}