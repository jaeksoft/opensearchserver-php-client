<?php
namespace OpenSearchServer\Crawler\Web\Patterns;

abstract class Insert extends Patterns
{
	/**
	 * if set to true every previous patterns will be deleted 
	 * @param unknown_type $replace
	 */
	public function replace($replace = true) {
		if($replace === true) {
			$this->parameters['replace'] = 'true';
		}
		return $this;
	}
	
	/******************************
	 * INHERITED METHODS OVERRIDDEN
	 ******************************/	
    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return self::METHOD_PUT;
    }
}