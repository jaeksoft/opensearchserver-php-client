<?php

namespace OpenSearchServer\Tests\Index;

require_once __DIR__.'/../TestWrapperInitData.php';
use OpenSearchServer\Tests\TestWrapperInitData;

class SearchTest extends TestWrapperInitData
{
    public function testSearchField() {
        $request = new \OpenSearchServer\Search\Field\Search();
        $request->index(self::$indexName)
                ->emptyReturnsAll()
                ->query('three musketeers')
                ->operator(\OpenSearchServer\Search\Search::OPERATOR_OR)
                ->lang('ENGLISH')
                ->returnedFields(array('title', 'url'))
                ->rows(5)
                ->snippet('title')
                ->snippet('content', 'b', '...', 200, 1, \OpenSearchServer\Search\Search::SNIPPET_SENTENCE_FRAGMENTER)
                ->searchFields(array('content', 'title', 'url'));
        $results = self::$oss_api->submit($request);
        
        $this->assertEquals(2, $results->getTotalNumberFound());
        
        foreach($results as $result) {
            $this->assertCount(2, $result->getAvailableSnippets());
        }
    }
    
    public function testSearchPattern() {
        $request = new \OpenSearchServer\Search\Pattern\Search();
        $request->index(self::$indexName)
                ->emptyReturnsAll()
                ->query('three musketeers')
                ->operator(\OpenSearchServer\Search\Search::OPERATOR_OR)
                ->lang('ENGLISH')
                ->returnedFields(array('title', 'url'))
                ->rows(5)
                ->snippet('title')
                ->snippet('content', 'b', '...', 200, 1, \OpenSearchServer\Search\Search::SNIPPET_SENTENCE_FRAGMENTER)
                ->patternSearchQuery('title:($$) OR content:($$)');
        $results = self::$oss_api->submit($request);
        
        $this->assertEquals(2, $results->getTotalNumberFound());
        
        foreach($results as $result) {
            $this->assertCount(2, $result->getAvailableSnippets());
        }
    }
}