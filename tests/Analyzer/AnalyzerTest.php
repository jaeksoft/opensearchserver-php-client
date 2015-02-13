<?php

namespace OpenSearchServer\Tests\Index;

require_once __DIR__.'/../TestWrapperCreateIndex.php';
use OpenSearchServer\Tests\TestWrapperCreateIndex;

class AnalyzerTest extends TestWrapperCreateIndex
{
    public function testCreate() {
        $json = <<<JSON
{
  "queryTokenizer":{"name":"KeywordTokenizer"},
  "indexTokenizer":{"name":"KeywordTokenizer"},
  "filters":[
    {
      "name":"ShingleFilter",
      "properties":{
        "max_shingle_size":"5",
        "token_separator":" ",
        "min_shingle_size":"1"
      },
      "scope":"QUERY_INDEX"
    },
    {
      "name":"PrefixSuffixStopFilter",
      "properties":{
        "prefixList":"English stop words",
        "ignore_case":"true",
        "token_separator":" ",
        "suffixList":"English stop words"
      },
      "scope":"QUERY_INDEX"
    } 
  ]
}
JSON;
        $request = new \OpenSearchServer\Analyzer\Create(null, $json);
        $request->index(self::$indexName)->name('__phpClient_TestAnalyzer');
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
    }

    public function testGetList() {
        $request = new \OpenSearchServer\Analyzer\GetList();
        $request->index(self::$indexName);
        $response = self::$oss_api->submit($request);
        $contains = false;
        foreach($response as $key => $item) {
            if($item->name == '__phpClient_TestAnalyzer') {
                $contains = true;
            }
        }
        $this->assertTrue($contains);
    }
    
    public function testGet() {
        $request = new \OpenSearchServer\Analyzer\Get();
        $request->index(self::$indexName)->name('__phpClient_TestAnalyzer');
        $response = self::$oss_api->submit($request);
        $this->assertTrue($response->isSuccess());
    }

}