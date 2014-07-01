<?php
namespace OpenSearchServer\Crawler\File;

use OpenSearchServer\Crawler\File\RequestFileCrawler;

class GetStatus extends RequestFileCrawler
{
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
        return 'crawler/file/status/'.$this->options['index'].'/json';
    }
}