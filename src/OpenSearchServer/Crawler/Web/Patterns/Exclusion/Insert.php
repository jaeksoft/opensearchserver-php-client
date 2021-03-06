<?php
namespace OpenSearchServer\Crawler\Web\Patterns\Exclusion;

use OpenSearchServer\Crawler\Web\Patterns\Insert as PatternsInsert;
class Insert extends PatternsInsert
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