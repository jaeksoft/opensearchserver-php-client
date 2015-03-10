<?php
namespace OpenSearchServer\Crawler\File\Repository;

use OpenSearchServer\Crawler\File\RequestFileCrawler;

//List being a PHP reserved word this class is named GetList
class GetList extends RequestFileCrawler
{

    public function getHeaders()
    {
        return array(
        	'Content-Type' => 'application/json'
        	);
    }

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
        return 'crawler/file/index/'.rawurlencode($this->options['index']);
    }
}