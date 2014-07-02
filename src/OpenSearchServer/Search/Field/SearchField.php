<?php
namespace OpenSearchServer\Search\Field;

use OpenSearchServer\Search\Search as Search;

abstract class SearchField extends Search
{
	const SEARCH_MODE_PATTERN = 'PATTERN';
	const SEARCH_MODE_TERM = 'TERM';
	const SEARCH_MODE_PHRASE = 'PHRASE';
	const SEARCH_MODE_TERM_AND_PHRASE = 'TERM_AND_PHRASE';

	/**
	 * Add a field to search into
	 * @return OpenSearchServer\Search\Search
	 */
	public function searchField($field, $mode = self::SEARCH_MODE_PATTERN, $boost = 1, $phraseBoost = 1) {
		if(empty($this->data['searchFields'])) {
			$this->data['searchFields'] = array();
		}
		$this->data['searchFields'][] = array(
			'field' => $field,
			'mode' => $mode,
			'boost' => $boost,
			'phraseBoost' => $phraseBoost
		);
		return $this;
	}

	/******************************
	 *     HELPER AND ALIASES
	 ******************************/
	/**
	 * Helper method: add several searchFields with same parameters
	 */
	public function searchFields(array $fields, $mode = self::SEARCH_MODE_PATTERN, $boost = 1, $phraseBoost = 2) {
		foreach($fields as $field) {
			$this->searchField($field, $mode, $boost, $phraseBoost);
		}
		return $this;
	}
}