<?php

namespace OpenSearchServer\Tests;

require_once __DIR__.'/TestWrapper.php';
use OpenSearchServer\Tests\TestWrapper;

class TestWrapperCreateIndex extends TestWrapper
{
    //Create an index to work on during tests
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        
        $request = new \OpenSearchServer\Index\Create();
        $request->index(self::$indexName)->template(\OpenSearchServer\Request::TEMPLATE_WEB_CRAWLER);
        $response = self::$oss_api->submit($request);
    }
    
    //Delete index when tests end
    public static function tearDownAfterClass() {
        parent::tearDownAfterClass();
        
        $request = new \OpenSearchServer\Index\Delete();
        $request->index(self::$indexName);
        $response = self::$oss_api->submit($request);
    }
}