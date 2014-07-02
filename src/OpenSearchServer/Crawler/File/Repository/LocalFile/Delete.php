<?php
namespace OpenSearchServer\Crawler\File\Repository\LocalFile;

use OpenSearchServer\Crawler\File\Repository\Repository;

class Delete extends Repository
{
    /******************************
     * INHERITED METHODS OVERRIDDEN
     ******************************/
    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return self::METHOD_DELETE;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        $this->checkPathIndexNeeded();
        return 'crawler/file/repository/remove/localfile/'.rawurlencode($this->options['index']).'/json';
    }
}