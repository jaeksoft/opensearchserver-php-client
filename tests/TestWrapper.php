<?php

namespace OpenSearchServer\Tests;

class TestWrapper extends \PHPUnit_Framework_TestCase
{
    static protected $oss_api;
    static protected $indexName = '__phpClient_test';
    
    public static function setUpBeforeClass() {
        if(empty(self::$oss_api)) {
            $app_key 	= $_ENV['oss_test_key'];
            $login      = $_ENV['oss_test_login'];
            $url = $_ENV['oss_test_url'];
            self::$oss_api    = new \OpenSearchServer\Handler(array('key' => $app_key, 'login' => $login, 'url' => $url ));
        }
    }
}