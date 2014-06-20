<?php
namespace OpenSearchServer;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Buzz\Browser;
use Buzz\Client\Curl;
use OpenSearchServer\Request;
use OpenSearchServer\Response;

class Handler
{
    protected $options = array();
    protected $browser;

    /**
     * Constructor
     * 
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        $this->options = $resolver->resolve($options);

        $this->init();
    }

    /**
     * Set our default options.
     * 
     * @param OptionsResolverInterface $resolver
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'url' => function (Options $options) {
                    return 'http://localhost:9090';
                },
                'prefix' => function (Options $options) {
                	return '/services/rest/index/';
                }
            ))
            ->setRequired(array(
                'key',
                'login'
            ))
            ->setOptional(array(
                'prefix'
            ))
            ->setAllowedTypes(array(
                'url' => 'string',
                'key' => 'string',
                'login' => 'string',
                'prefix' => 'string'
            ));
    }

    /**
     * Initialise
     */
    protected function init()
    {
        $client = new Curl;
        $client->setVerifyPeer(false);

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
    	return $this->options['url'] . $this->options['prefix'] . $request->getPath() . '?' . http_build_query(array_merge($this->getParameters(), $request->getParameters()));
    }
    
    /**
     * Submit a request to the API.
     * 
     * @param RequestInterface $request
     * @return object
     */
    public function submit(Request $request)
    {
        $response = $this->browser->call(
            $this->buildUrl($request),
            $request->getMethod(),
            $request->getHeaders(),
            $request->getData()
            );
            
        var_dump($this->browser->getLastRequest());

        return new Response($response);
    }

}