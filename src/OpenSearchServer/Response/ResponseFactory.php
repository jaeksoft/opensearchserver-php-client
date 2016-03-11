<?php
namespace OpenSearchServer\Response;

use Buzz\Message\Response as BuzzResponse;

class ResponseFactory
{
    public static function createResponse(BuzzResponse $response, \OpenSearchServer\Request $request)
    {
        if(            is_a($request, 'OpenSearchServer\Search\Pattern\Search')
                    || is_a($request, 'OpenSearchServer\Search\Field\Search')) {
                return new \OpenSearchServer\Response\SearchResult($response, $request);
        } else if (    is_a($request, 'OpenSearchServer\Monitor\Monitor')) {
                return new \OpenSearchServer\Response\ResponseMonitor($response, $request);
        } else if (    is_a($request, 'OpenSearchServer\Autocompletion\Query')) {
                $response = new ResponseIterable($response, $request);
                if(!empty($response->getJsonValues()->terms)) {
                    $response->setValues($response->getJsonValues()->terms);
                }
                return $response;
		} else if (	   is_a($request, 'OpenSearchServer\Index\GetList')) {
                $response = new ResponseIterable($response, $request);
                if(!empty($response->getJsonValues()->indexList)) {
                    $response->setValues($response->getJsonValues()->indexList);
                }
                return $response;
		} else if (		is_a($request, 'OpenSearchServer\Field\GetList')) {
                $response = new ResponseIterable($response, $request);
                if(!empty($response->getJsonValues()->fields)) {
                    $response->setValues($response->getJsonValues()->fields);
                }
                return $response;
		} else if (    is_a($request, 'OpenSearchServer\Analyzer\GetList')) {
                $response = new ResponseIterable($response, $request);
                if(!empty($response->getJsonValues()->analyzers)) {
                    $response->setValues($response->getJsonValues()->analyzers);
                }
                return $response;
		} else if (	   is_a($request, 'OpenSearchServer\Crawler\Web\Patterns\Exclusion\GetList')
                    || is_a($request, 'OpenSearchServer\Crawler\Web\Patterns\Inclusion\GetList')
                    || is_a($request, 'OpenSearchServer\Autocompletion\GetList')
                    || is_a($request, 'OpenSearchServer\Synonyms\GetList')
                    || is_a($request, 'OpenSearchServer\Crawler\Rest\GetList')
                    || is_a($request, 'OpenSearchServer\Replication\GetList')
                    || is_a($request, 'OpenSearchServer\Parser\GetList')
                    || is_a($request, 'OpenSearchServer\StopWords\GetList')
                    || is_a($request, 'OpenSearchServer\Crawler\File\Repository\GetList')) {
                $response = new ResponseIterable($response, $request);
                if(!empty($response->getJsonValues()->items)) {
                    $response->setValues($response->getJsonValues()->items);
                }
                return $response;
         } else if (   is_a($request, 'OpenSearchServer\Spellcheck\GetList')) {
                $response = new ResponseIterable($response, $request);
                $values = $response->getJsonValues();
                if(!empty($values)) {
                    $templates = array();
                    foreach($values as $obj) {
                        $templates[] = $obj->name;
                    }
                    $response->setValues($templates);
                }
                return $response;
         } else if (   is_a($request, 'OpenSearchServer\SearchTemplate\GetList')
                    || is_a($request, 'OpenSearchServer\MoreLikeThis\GetList')) {
                $response = new ResponseIterable($response, $request);
                if(!empty($response->getJsonValues()->templates)) {
                    $response->setValues($response->getJsonValues()->templates);
                }
                return $response;
         } else if (   is_a($request, 'OpenSearchServer\Synonyms\Get')
                    || is_a($request, 'OpenSearchServer\StopWords\Get')) {
                $response = new ResponseIterable($response, $request);
                $content = $response->getRawContent();
                if(!empty($content)) {
                    $response->setValues(explode("\n", $content));
                }
                return $response;
         }
         //Synonyms\Exists has a particular behaviour: existence is based on HTTP response code (200 / 404)
         else if (	   is_a($request, 'OpenSearchServer\Synonyms\Exists')
                    || is_a($request, 'OpenSearchServer\StopWords\Exists')) {
                $headers = $response->getHeaders();
                $responseHttpCode = $headers[0];
                $response = new \OpenSearchServer\Response\Response($response, $request);
                $response->setSuccess(strpos($responseHttpCode, '200 OK') !== false);
                return $response;
         } else if (   is_a($request, 'OpenSearchServer\SpellCheck\Search')) {
                return new \OpenSearchServer\Response\SpellCheckResult($response, $request);
         } else if (   is_a($request, 'OpenSearchServer\MoreLikeThis\Search')) {
                return new \OpenSearchServer\Response\MoreLikeThisResult($response, $request);
         } else if (   is_a($request, 'OpenSearchServer\SearchBatch\SearchBatch')) {
                return new \OpenSearchServer\Response\SearchBatchResult($response, $request);
         } else {
                return new \OpenSearchServer\Response\Response($response, $request);
        }
    }    
}
