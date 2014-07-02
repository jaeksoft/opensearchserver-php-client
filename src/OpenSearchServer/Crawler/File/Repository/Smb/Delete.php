<?php
namespace OpenSearchServer\Crawler\File\Repository\Smb;

use OpenSearchServer\Crawler\File\Repository\Repository;

class Delete extends Repository
{
    public function username($username) {
        $this->parameters['username'] = $username;
        return $this;
    }
    
    public function domain($domain) {
        $this->parameters['domain'] = $domain;
        return $this;
    }
    
    public function host($host) {
        $this->parameters['host'] = $host;
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
        return 'crawler/file/repository/remove/smb/'.rawurlencode($this->options['index']).'/json';
    }
}