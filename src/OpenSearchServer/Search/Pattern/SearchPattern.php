<?php
namespace OpenSearchServer\Search\Pattern;

use OpenSearchServer\Search\Search as Search;

abstract class SearchPattern extends Search
{
	/**
	 * Set search pattern
	 * @return OpenSearchServer\Search\Search
	 */
	public function patternSearchQuery($pattern) {
		$this->data['patternSearchQuery'] = $pattern;
		return $this;
	}
	
	/**
	 * Set snippet pattern
	 * @return OpenSearchServer\Search\Search
	 */
	public function patternSnippetQuery($pattern) {
		$this->data['patternSnippetQuery'] = $pattern;
		return $this;
	}
}