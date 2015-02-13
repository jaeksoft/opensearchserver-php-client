<?php

namespace OpenSearchServer\Tests\Index;

require_once __DIR__.'/../TestWrapper.php';
use OpenSearchServer\Tests\TestWrapper;

class MonitorTest extends TestWrapper
{
    public function testMonitor() {
        $request = new \OpenSearchServer\Monitor\Monitor();
        $response = self::$oss_api->submit($request);
        $this->assertArrayHasKey('indexCount', $response->getValues());
        
        $request->full();
        $response = self::$oss_api->submit($request);
        $this->assertArrayHasKey('java.vm.vendor', $response->getValues());
    }

}