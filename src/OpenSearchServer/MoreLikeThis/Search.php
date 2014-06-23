<?php
namespace OpenSearchServer\MoreLikeThis;

use OpenSearchServer\MoreLikeThis\MoreLikeThis;

class Search extends MoreLikeThis
{
	/******************************
	 * INHERITED METHODS OVERRIDDEN
	 ******************************/
	/**
	 * {@inheritdoc}
	 */
	public function getMethod()
	{
		return self::METHOD_POST;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPath()
	{
    	$this->checkPathIndexNeeded();
        $path = $this->options['index'].'/morelikethis';
        return (!empty($this->options['template'])) ? $path.'/template/'.$this->options['template'] : $path;
	}
}