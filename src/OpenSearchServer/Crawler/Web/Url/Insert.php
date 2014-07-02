<?php
namespace OpenSearchServer\Crawler\Web\Url;

use OpenSearchServer\RequestJson;

class Insert extends RequestJson
{
	/**
	 * Add an URL
	 * @param string $url URL to add
	 */
	public function url($url) {
		$this->data[] = $url;
		return $this;
	}

	/******************************
	 * HELPERS
	 ******************************/
	/**
	 * Add several urls
	 * @param array $urls
	 */
	public function urls($urls) {
		foreach($urls as $url) {
			$this->url($url);
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
    	$this->checkPathIndexNeeded();
        return rawurlencode($this->options['index']).'/crawler/web/urls';
    }
}