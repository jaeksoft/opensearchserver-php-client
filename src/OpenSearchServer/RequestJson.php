<?php
namespace OpenSearchServer;

class RequestJson extends Request
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
        	'Content-Type' => 'application/json'
        	);
	}
}