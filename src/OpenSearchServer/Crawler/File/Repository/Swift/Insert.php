<?php
namespace OpenSearchServer\Crawler\File\Repository\Swift;

use OpenSearchServer\Crawler\File\Repository\Repository;

class Insert extends Repository
{
    const AUTH_KEYSTONE = 'KEYSTONE';
    const AUTH_IAM = 'IAM';
    
    public function username($username) {
        $this->parameters['username'] = $username;
        return $this;
    }
    
    public function password($password) {
        $this->parameters['password'] = $password;
        return $this;
    }

    public function tenant($tenant) {
        $this->parameters['tenant'] = $tenant;
        return $this;
    }
    
    public function container($container) {
        $this->parameters['container'] = $container;
        return $this;
    }
    
    public function authUrl($authUrl) {
        $this->parameters['authUrl'] = $authUrl;
        return $this;
    }
    
    /**
     * @param string $authType KEYSTONE|IAM
     */
    public function authType($authType) {
        $this->parameters['authType'] = $authType;
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
        return 'crawler/file/repository/inject/swift/'.rawurlencode($this->options['index']).'/json';
    }
}