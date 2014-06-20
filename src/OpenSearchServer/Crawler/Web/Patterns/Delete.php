<?php
namespace OpenSearchServer\Crawler\Web\Patterns;

abstract class Delete extends Patterns
{
	/******************************
	 * INHERITED METHODS OVERRIDDEN
	 ******************************/	
    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return self::METHOD_DELETE;
    }
}