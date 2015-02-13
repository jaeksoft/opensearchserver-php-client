<?php

namespace OpenSearchServer\Tests\Index;

require_once __DIR__.'/../TestWrapperInitData.php';
use OpenSearchServer\Tests\TestWrapperInitData;

class SearchBatchTest extends TestWrapperInitData
{
    public function testSearchBatch() {
        $request = new \OpenSearchServer\Search\Field\Search();
        $request->query('musketeer')
                ->emptyReturnsAll()
                ->searchField('title', \OpenSearchServer\Search\Field\Search::SEARCH_MODE_TERM_AND_PHRASE, 5, 10)
                ->returnedFields(array('title', 'url'));
        $request2 = new \OpenSearchServer\Search\Pattern\Search();
        $request2->query('bragelonne')
                 ->patternSearchQuery('title:($$)^10 OR content:($$)^10')
                 ->returnedFields(array('title', 'url'))
                 ->rows(4);
        
        $requestBatch = new \OpenSearchServer\SearchBatch\SearchBatch();
        $requestBatch->index(self::$indexName)->mode(\OpenSearchServer\SearchBatch\SearchBatch::MODE_MANUAL);
        $requestBatch->addQueries(array(
                array($request, \OpenSearchServer\SearchBatch\SearchBatch::ACTION_CONTINUE), 
                array($request2, \OpenSearchServer\SearchBatch\SearchBatch::ACTION_STOP_IF_FOUND),
              ));
        $response = self::$oss_api->submit($requestBatch);

        $this->assertEquals(2, $response->getNumberOfQueriesWithResult());
    }
}