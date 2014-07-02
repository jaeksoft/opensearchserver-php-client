<?php
namespace OpenSearchServer\Crawler\File;

use OpenSearchServer\Crawler\File\RequestFileCrawler;

class Stop extends RequestFileCrawler
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
        return 'crawler/file/stop/'.rawurlencode($this->options['index']).'/json';
    }
}