<?php
namespace OpenSearchServer\Response;

use Buzz\Message\Response as BuzzResponse;

class ResponseFactory
{
    public static function createResponse(BuzzResponse $response, \OpenSearchServer\Request $request)
    {
        switch(get_class($request)) {
            case 'OpenSearchServer\Search\Pattern\Search':
            case 'OpenSearchServer\Search\Field\Search':
                return new \OpenSearchServer\Response\SearchResult($response, $request);
                break;
            case 'OpenSearchServer\Monitor\Monitor':
                return new \OpenSearchServer\Response\ResponseMonitor($response, $request);
                break;
            case 'OpenSearchServer\Autocompletion\Query':
                $response = new ResponseIterable($response, $request);
                if(!empty($response->getJsonValues()->terms)) {
                    $response->setValues($response->getJsonValues()->terms);
                }
                return $response;
                break;
            case 'OpenSearchServer\Index\GetList':
                $response = new ResponseIterable($response, $request);
                if(!empty($response->getJsonValues()->indexList)) {
                    $response->setValues($response->getJsonValues()->indexList);
                }
                return $response;
                break;
            case 'OpenSearchServer\Field\GetList':
                $response = new ResponseIterable($response, $request);
                if(!empty($response->getJsonValues()->fields)) {
                    $response->setValues($response->getJsonValues()->fields);
                }
                return $response;
                break;
            case 'OpenSearchServer\Analyzer\GetList':
                $response = new ResponseIterable($response, $request);
                if(!empty($response->getJsonValues()->analyzers)) {
                    $response->setValues($response->getJsonValues()->analyzers);
                }
                return $response;
                break;
            case 'OpenSearchServer\Crawler\Web\Patterns\Exclusion\GetList':
            case 'OpenSearchServer\Crawler\Web\Patterns\Inclusion\GetList':
            case 'OpenSearchServer\Autocompletion\GetList':
            case 'OpenSearchServer\Synonyms\GetList':
            case 'OpenSearchServer\Crawler\Rest\GetList':
            case 'OpenSearchServer\Replication\GetList':
            case 'OpenSearchServer\Parser\GetList':
            case 'OpenSearchServer\StopWords\GetList':
                $response = new ResponseIterable($response, $request);
                if(!empty($response->getJsonValues()->items)) {
                    $response->setValues($response->getJsonValues()->items);
                }
                return $response;
                break;
            case 'OpenSearchServer\Spellcheck\GetList':
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
                break;
            case 'OpenSearchServer\SearchTemplate\GetList':
            case 'OpenSearchServer\MoreLikeThis\GetList':
                $response = new ResponseIterable($response, $request);
                if(!empty($response->getJsonValues()->templates)) {
                    $response->setValues($response->getJsonValues()->templates);
                }
                return $response;
                break;
            case 'OpenSearchServer\Synonyms\Get':
            case 'OpenSearchServer\StopWords\Get':
                $response = new ResponseIterable($response, $request);
                $content = $response->getRawContent();
                if(!empty($content)) {
                    $response->setValues(explode("\n", $content));
                }
                return $response;
                break;
            //Synonyms\Exists has a particular behaviour: existence is based on HTTP response code (200 / 404)
            case 'OpenSearchServer\Synonyms\Exists':
            case 'OpenSearchServer\StopWords\Exists':
                $headers = $response->getHeaders();
                $responseHttpCode = $headers[0];
                $response = new \OpenSearchServer\Response\Response($response, $request);
                $response->setSuccess(strpos($responseHttpCode, '200 OK') !== false);
                return $response;
                break;
            case 'OpenSearchServer\SpellCheck\Search':
                return new \OpenSearchServer\Response\SpellCheckResult($response, $request);
                break;
            case 'OpenSearchServer\MoreLikeThis\Search':
                return new \OpenSearchServer\Response\MoreLikeThisResult($response, $request);
                break;
            case 'OpenSearchServer\SearchBatch\SearchBatch':
                return new \OpenSearchServer\Response\SearchBatchResult($response, $request);
                break;
            default:
                return new \OpenSearchServer\Response\Response($response, $request);
        }
    }    
}