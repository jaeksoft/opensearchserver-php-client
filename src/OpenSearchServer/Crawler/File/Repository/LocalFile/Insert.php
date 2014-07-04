<?php
namespace OpenSearchServer\Crawler\File\Repository\LocalFile;

use OpenSearchServer\Crawler\File\Repository\Repository;

class Insert extends Repository
{
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
        return 'crawler/file/repository/inject/localfile/'.rawurlencode($this->options['index']).'/json';
    }
}