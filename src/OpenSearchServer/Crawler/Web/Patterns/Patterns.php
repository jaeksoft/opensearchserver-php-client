<?php
namespace OpenSearchServer\Crawler\Web\Patterns;

use OpenSearchServer\RequestJson;

abstract class Patterns extends RequestJson
{
	/**
	 * Add a pattern
	 * @param string $pattern URL to add
	 */
	public function pattern($pattern) {
		$this->data[] = $pattern;
		return $this;
	}

	/******************************
	 * HELPERS
	 ******************************/
	/**
	 * Add several patterns
	 * @param array $patterns
	 */
	public function patterns($patterns) {
		foreach($patterns as $pattern) {
			$this->pattern($pattern);
		}
		return $this;
	}
}