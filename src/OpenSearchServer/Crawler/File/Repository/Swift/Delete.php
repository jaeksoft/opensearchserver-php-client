<?php
namespace OpenSearchServer\Crawler\File\Repository\Swift;

use OpenSearchServer\Crawler\File\Repository\Repository;

class Delete extends Repository
{
    public function username($username) {
        $this->parameters['username'] = $username;
        return $this;
    }
    
    public function container($container) {
        $this->parameters['container'] = $container;
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
        return self::METHOD_DELETE;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        $this->checkPathIndexNeeded();
        return 'crawler/file/repository/remove/swift/'.rawurlencode($this->options['index']).'/json';
    }
}