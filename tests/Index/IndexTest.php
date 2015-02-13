<?php

namespace OpenSearchServer\Tests\Index;

require_once __DIR__.'/../TestWrapper.php';
use OpenSearchServer\Tests\TestWrapper;

class IndexTest extends TestWrapper
{
    public function testCreate() {
        $request = new \OpenSearchServer\Index\Create();
        $request->index(self::$indexName);
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
        
        $request = new \OpenSearchServer\Index\Create();
        $request->index(self::$indexName.'_web')->template(\OpenSearchServer\Request::TEMPLATE_WEB_CRAWLER);
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
        
        $request = new \OpenSearchServer\Index\Create();
        $request->index(self::$indexName.'_file')->template(\OpenSearchServer\Request::TEMPLATE_FILE_CRAWLER);
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
    }

    public function testExists() {
        $request = new \OpenSearchServer\Index\Exists();
        $request->index(self::$indexName);
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
    }
    
    public function testGetList() {
        $request = new \OpenSearchServer\Index\GetList();
        $request->index(self::$indexName);
        $response = self::$oss_api->submit($request);
        $this->assertContains(self::$indexName, $response->getValues());
    }
    
    public function testDelete() {
        $request = new \OpenSearchServer\Index\Delete();
        $request->index(self::$indexName);
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
        
        $request = new \OpenSearchServer\Index\Delete();
        $request->index(self::$indexName.'_web');
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
        
        $request = new \OpenSearchServer\Index\Delete();
        $request->index(self::$indexName.'_file');
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
    }
}