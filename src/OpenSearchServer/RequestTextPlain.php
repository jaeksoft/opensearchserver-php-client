<?php
namespace OpenSearchServer;

class RequestTextPlain extends Request
{
    /******************************
	 * INHERITED METHODS OVERRIDDEN
	 ******************************/
	/**
	 * {@inheritdoc}
	 */
	public function getHeaders()
	{
		return array(
        	'Content-Type' => 'text/plain'
        	);
	}
}