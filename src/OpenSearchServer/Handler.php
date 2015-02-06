<?php
namespace OpenSearchServer;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Buzz\Browser;
use Buzz\Client\Curl;
use Buzz\Message\Response as BuzzResponse;
use OpenSearchServer\Request;
use OpenSearchServer\Response\ResponseFactory;

class Handler
{
    protected $options = array();
    protected $browser;

    /**
     * Constructor
     * 
     * @param array $options
     */
    public function __construct(array $options = array(), array $curlOptions = array())
    {
        //Handle options
        if(empty($options['key']) || empty($options['login'])) {
        	throw new \Exception('Parameters \'key\' and \'login\' are required');
    	}
        $defaultOptions = array(
            'url' => 'http://localhost:9090'
        );
        
        $this->options = array_merge($defaultOptions, $options);

        $this->init($curlOptions);
    }

    /**
     * Initialise
     */
    protected function init($curlOptions)
    {
        $client = new Curl;
        $client->setVerifyPeer(false);
        $client->setTimeout(60000);
        foreach($curlOptions as $option => $value) {
          $client->setOption($option, $value);
        }
        
        $this->client = $client;
        $this->browser = new Browser($client);
    }

    /**
     * Return common URL parameters
     */
    private function getParameters() {
    	return array(
    		'login' => $this->options['login'],
    		'key' => $this->options['key']
    	);
    }

    /**
     * Build final URL to call 
     * @param OpenSearchServer\Request $request
     */
    private function buildUrl($request) {
    	return $this->options['url'] . $request->getUrlPrefix() . $request->getPath() . '?' . $this->getUrlPartParameters($request);
    }
    
    private function getUrlPartParameters($request) {
        $url = http_build_query(array_merge($this->getParameters(), $request->getParameters()));
        //replace [0], [1], ... by nothing since OSS V2 API expects array values to have same parameter name
        $url = preg_replace('/%5B[0-9]+%5D=/simU', '=', $url);
        $url .= $this->getURLStringCredentials();
        
        return $url;
    }
    
    /**
     * Submit a request to the API.
     * 
     * @param OpenSearchServer\Request $request
     * @return object
     */
    public function submit(Request $request)
    {
        try {
            $response = $this->browser->call(
                $this->buildUrl($request),
                $request->getMethod(),
                $request->getHeaders(),
                $request->getData()
                );
              
            $response = ResponseFactory::createResponse($response, $request);
            return $response;
        } catch(\Exception $e) {
            throw new Exception\OpenSearchServerException('Error while connecting to OpenSearchServer: ' . $e->getMessage(), 1);
        } 
    }
    
    /**
     * Submit a PUT or POST request to the API, sending a file using CURL directly.
     * @param OpenSearchServer\RequestFile $requestOSS
     * @param array $options
     */
    public function submitFile(RequestFile $requestOSS, array $options = array()) {
        $request = curl_init($this->buildUrl($requestOSS));
        ($requestOSS->getMethod() == 'PUT') ?
                curl_setopt($request, CURLOPT_CUSTOMREQUEST, 'PUT') :
                curl_setopt($request, CURLOPT_POST, true);
        $args['file'] = new \CurlFile($requestOSS->getFilePath());
        curl_setopt($request, CURLOPT_POSTFIELDS, $args);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        //send
        $content = curl_exec ( $request );
        //build BuzzResponse
        $errmsg = curl_error ( $request );
        $headers = curl_getinfo ( $request );
        $httpCode = curl_getinfo ( $request, CURLINFO_HTTP_CODE );
        //build header for BuzzResponse to parse HTTP code properly
        $newHeader = 'HTTP ' . $httpCode . ' ';
        $newHeader .= ($errmsg) ? $errmsg : '-'; 
        array_unshift($headers, $newHeader);
	    $response = new BuzzResponse();
	    $response->setContent($content);
	    $response->setHeaders($headers);
	    //force parsing of first header
	    $response->getStatusCode();
        curl_close($request);
        //build and return an OpenSearchServer\Response
        return new \OpenSearchServer\Response\Response($response, $requestOSS);
    }
    
    public function setUser($value)
    {
        $this->options['user'] = $value;
    }
    public function setGroups($groups)
    {
        $this->options['groups'] = array($groups);
    }

    protected function getURLStringCredentials() {
   		$url = '&';
        if(!empty($this->options['user'])) {
            $url .= 'user='.$this->options['user'];
        }
        if(!empty($this->options['groups'])) {
            foreach($this->groups as $group) {
                $url .= '&group='.urlencode($group);
            }
        }
        return $url;
    }
    
    /**
     * Get last request's object
     */
    public function getLastRequest() {
    	return $this->browser->getLastRequest();
    }

}