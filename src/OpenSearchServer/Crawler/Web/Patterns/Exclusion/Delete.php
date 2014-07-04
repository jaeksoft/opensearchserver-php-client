<?php
namespace OpenSearchServer\Crawler\Web\Patterns\Exclusion;

use OpenSearchServer\Crawler\Web\Patterns\Delete as PatternsDelete;
class Delete extends PatternsDelete
{
	/******************************
	 * INHERITED METHODS OVERRIDDEN
	 ******************************/	
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
    	$this->checkPathIndexNeeded();
        return rawurlencode($this->options['index']).'/crawler/web/patterns/exclusion';
    }
}