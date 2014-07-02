<?php
namespace OpenSearchServer\Crawler\File\Repository\Smb;

use OpenSearchServer\Crawler\File\Repository\Repository;

class Insert extends Repository
{
    public function username($username) {
        $this->parameters['username'] = $username;
        return $this;
    }
    
    public function password($password) {
        $this->parameters['password'] = $password;
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
        return self::METHOD_PUT;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        $this->checkPathIndexNeeded();
        return 'crawler/file/repository/inject/smb/'.rawurlencode($this->options['index']).'/json';
    }
}