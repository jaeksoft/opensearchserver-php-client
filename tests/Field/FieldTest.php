<?php

namespace OpenSearchServer\Tests\Index;

require_once __DIR__.'/../TestWrapperCreateIndex.php';
use OpenSearchServer\Tests\TestWrapperCreateIndex;

class FieldTest extends TestWrapperCreateIndex
{
    public function testCreate() {
        $request = new \OpenSearchServer\Field\Create();
        $request->index(self::$indexName)
        ->name('titleStandard')
        ->indexed(true)
        ->analyzer('StandardAnalyzer')
        ->stored(true)
        ->copyOf('title');
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
    }

    public function testCreateBulk() {
        $json = <<<JSON
[
        {
            "name": "uniqueId",
            "indexed": "YES",
            "stored": "NO"
        },
        {
            "name": "title",
            "indexed": "YES",
            "stored": "YES",
            "analyzer": "TextAnalyzer"
        },
        {
            "name": "description",
            "indexed": "YES",
            "stored": "YES",
            "analyzer": "TextAnalyzer"
        },
        {
            "name": "descriptionStandard",
            "indexed": "YES",
            "stored": "NO",
            "analyzer": "StandardAnalyzer",
            "copyOf": [
                "description"
            ]
        }
]
JSON;
        $request = new \OpenSearchServer\Field\CreateBulk(null, $json);
        $request->index(self::$indexName);
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
    }

    public function testGetList() {
        $request = new \OpenSearchServer\Field\GetList();
        $request->index(self::$indexName);
        $response = self::$oss_api->submit($request);
        $contains = false;
        foreach($response as $key => $item) {
            if($item->name == 'uniqueId') {
                $contains = true;
            }
        }
        $this->assertTrue($contains);
    }

    public function testGet() {
        $request = new \OpenSearchServer\Field\Get();
        $request->index(self::$indexName)->name('uniqueId');
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
    }
    
    public function testSetDefaultUnique() {
        $request = new \OpenSearchServer\Field\SetDefaultUnique();
        $request->index(self::$indexName)->defaultField('description')->uniqueField('uniqueId');
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
    }
    
    public function testDelete() {
        $request = new \OpenSearchServer\Field\Delete();
        $request->index(self::$indexName)->name('uniqueId');
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
    }
}