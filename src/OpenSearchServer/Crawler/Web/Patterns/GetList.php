<?php
namespace OpenSearchServer\Crawler\Web\Patterns;

use OpenSearchServer\Request;

abstract class GetList extends Request
{
	/**
	 * Retrieve only patterns starting with given text
	 * @param string $prefix
	 */
	public function startsWith($prefix) {
		$this->parameters['starts_with'] = $prefix;
	}
	
	/******************************
	 * INHERITED METHODS OVERRIDDEN
	 ******************************/	
    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return self::METHOD_GET;
    }
}