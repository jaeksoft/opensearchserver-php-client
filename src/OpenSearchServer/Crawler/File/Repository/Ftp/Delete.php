<?php
namespace OpenSearchServer\Crawler\File\Repository\Ftp;

use OpenSearchServer\Crawler\File\Repository;

class Smb extends Repository
{
    public function username($username) {
        $this->parameters['username'] = $username;
        return $this;
    }
    
    public function host($host) {
        $this->parameters['host'] = $host;
        return $this;
    }
    
    public function ssl($ssl) {
        $this->parameters['ssl'] = $ssl;
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
        return 'crawler/file/repository/remove/ftp/'.$this->options['index'].'/json';
    }
}