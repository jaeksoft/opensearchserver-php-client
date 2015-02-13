<?php

namespace OpenSearchServer\Tests\Index;

require_once __DIR__.'/../TestWrapperInitData.php';
use OpenSearchServer\Tests\TestWrapperInitData;

class SearchTemplateTest extends TestWrapperInitData
{
    public function testCreateSearchTemplateField() {
        $request = new \OpenSearchServer\Search\Field\Put();
        $request->index(self::$indexName)
                ->emptyReturnsAll()
                ->query('three musketeers')
                ->operator(\OpenSearchServer\Search\Search::OPERATOR_OR)
                ->lang('ENGLISH')
                ->returnedFields(array('title', 'url'))
                ->rows(5)
                ->snippet('title')
                ->snippet('content', 'b', '...', 200, 1, \OpenSearchServer\Search\Search::SNIPPET_SENTENCE_FRAGMENTER)
                ->searchFields(array('content', 'title', 'url'))
                ->template('__phpClient_testSearchField');
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
    }

    public function testCreateSearchTemplatePattern() {
        $request = new \OpenSearchServer\Search\Pattern\Put();
        $request->index(self::$indexName)
                ->emptyReturnsAll()
                ->query('three musketeers')
                ->operator(\OpenSearchServer\Search\Search::OPERATOR_OR)
                ->lang('ENGLISH')
                ->returnedFields(array('title', 'url'))
                ->rows(5)
                ->snippet('title')
                ->snippet('content', 'b', '...', 200, 1, \OpenSearchServer\Search\Search::SNIPPET_SENTENCE_FRAGMENTER)
                ->patternSearchQuery('title:($$) OR content:($$)')
                ->template('__phpClient_testSearchPattern');
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
    }
    
    public function testSearchTemplateGetList() {
        $request = new \OpenSearchServer\SearchTemplate\GetList();
        $request->index(self::$indexName);
        $response = self::$oss_api->submit($request);
        $names = array();
        foreach($response as $key => $item) {
            $names[] = $item->name;
        }
        $this->assertContains('__phpClient_testSearchField', $names);
        $this->assertContains('__phpClient_testSearchPattern', $names);
    }

    public function testSearchTemplateGet() {
        $request = new \OpenSearchServer\SearchTemplate\Get();
        $request->index(self::$indexName)->name('__phpClient_testSearchField');
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
    }
    
    public function testSearchTemplateDelete() {
        $request = new \OpenSearchServer\SearchTemplate\Delete();
        $request->index(self::$indexName)->name('__phpClient_testSearchField');
        $response = self::$oss_api->submit($request);
        $this->assertEquals('Template deleted: __phpClient_testSearchField', $response->getInfo());
    }
}