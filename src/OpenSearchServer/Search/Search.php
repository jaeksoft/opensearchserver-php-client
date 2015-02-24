<?php
namespace OpenSearchServer\Search;

use OpenSearchServer\Search as SearchAbstract;

abstract class Search extends SearchAbstract
{
	/**
	 * Set name of search template to use
	 * Optional
	 * @param string $value
	 */
	public function template($value) {
		$this->options['template'] = $value;
		return $this;
	}

	/**
	 * Specify the query
	 * @param string $query
	 * @return OpenSearchServer\Search\Search
	 */
	public function query($query = NULL) {
		$this->data['query'] = $query;
		return $this;
	}

	/**
	 * Set the default operator: OR or AND
	 * @return OpenSearchServer\Search\Search
	 */
	public function operator($operator) {
		$this->data['operator'] = $operator;
		return $this;
	}

	/**
	 * @return OpenSearchServer\Search\Search
	 */
	public function enableLog($enableLog = true) {
		$this->data['enableLog'] = (boolean) $enableLog;
		return $this;
	}

	/**
	 * @return OpenSearchServer\Search\Search
	 */
	public function emptyReturnsAll($emptyReturnsAll = true) {
		$this->data['emptyReturnsAll'] = (boolean) $emptyReturnsAll;
		return $this;
	}

	/**
	 * Add one level of sorting
	 * @return OpenSearchServer\Search\Search
	 */
	public function sort($field, $direction = self::SORT_DESC) {
		if(empty($this->data['sorts'])) {
			$this->data['sorts'] = array();
		}
		$this->data['sorts'][] = array(
			'field' => $field,
			'direction' => $direction
		);
		return $this;
	}

	/**
	 * Configure collapsing
	 * @return OpenSearchServer\Search\Search
	 */
	public function collapsing($field, $max, $mode = self::COLLAPSING_MODE_OFF, $type = self::COLLAPSING_TYPE_FULL) {
		if(empty($this->data['collapsing'])) {
			$this->data['collapsing'] = array();
		}
		$this->data['collapsing'] = array(
			'field' => $field,
			'mode' => $mode,
			'type' => $type,
			'max' => $max
		);
		return $this;
	}

	/**
	 * Add a facet to search results
	 * @return OpenSearchServer\Search\Search
	 */
	public function facet($field, $min = 0, $multi = false, $postCollapsing = false) {
		if(empty($this->data['facets'])) {
			$this->data['facets'] = array();
		}
		$this->data['facets'][] = array(
			'field' => $field,
			'minCount' => $min,
			'multivalued' => $multi,
			'postCollapsing' => $postCollapsing
		);
		return $this;
	}

	/**
	 * Add one snippet
	 * @return OpenSearchServer\Search\Search
	 */
	public function snippet($field, $tag = 'b', $separator = '...', $maxSize = 200, $maxNumber = 1, $fragmenter = self::SNIPPET_NO_FRAGMENTER) {
		if(empty($this->data['snippets'])) {
			$this->data['snippets'] = array();
		}
		$this->data['snippets'][] = array(
			'field' => $field,
			'tag' => $tag,
			'separator' => $separator,
			'maxSize' => $maxSize,
			'maxNumber' => $maxNumber,
			'fragmenter' => $fragmenter,
		);
		return $this;
	}

	/**
	 * Add one level of scoring
	 * @return OpenSearchServer\Search\Search
	 */
	public function scoring($field = null, $weight = 1, $ascending = false, $type = self::SCORING_FIELD_ORDER) {
		if(empty($this->data['scorings'])) {
			$this->data['scorings'] = array();
		}
		$newScoring = array(
			'ascending' => (boolean) $ascending,
			'type' => $type,
			'weight' => $weight
		);
		if(!empty($field)) {
			$newScoring['field'] = $field;
		}
		$this->data['scorings'][] = $newScoring;
		return $this;
	}
	
	/**
	 * Add one boosting query
	 * @return OpenSearchServer\Search\Search
	 */
	public function boostingQuery($query, $boost = 1) {
		if(empty($this->data['boostingQueries'])) {
			$this->data['boostingQueries'] = array();
		}
		$newBoosting = array(
			'patternQuery' => $query,
			'boost' => $boost
		);
		$this->data['boostingQueries'][] = $newBoosting;
		return $this;
	}
	
	/**
	 * Add one join
	 * @return OpenSearchServer\Search\Search
	 */
	public function join($indexName, $queryTemplate, $queryString, $localField, $foreignField, $type = self::JOIN_INNER, $returnFields = true, $returnScores = false, $returnFacets = false) {
		if(empty($this->data['joins'])) {
			$this->data['joins'] = array();
		}
		$this->data['joins'][] = array(
			'indexName' => $indexName,
			'queryTemplate' => $queryTemplate,
			'queryString' => $queryString,
			'localField' => $localField,
			'$foreignField' => $foreignField,
			'type' => $type,
			'returnFields' => $returnFields,
			'returnScores' => $returnScores,
			'returnFacets' => $returnFacets			
		);
		return $this;
	}

	/**
	 * Helper method (alias)
	 * @see returnedFields()
	 * @return OpenSearchServer\Search\Search
	 */
	public function field($fields) {
		return $this->returnedFields($fields);
	}

	/**
	 * Helper method: add several sorts
	 * @return OpenSearchServer\Search\Search
	 */
	public function sorts($sorts, $direction = self::SORT_DESC) {
		foreach($sorts as $sort) {
			$this->sort($sort, $direction);
		}
		return $this;
	}

	/**
	 * Helper method: configure request to return distance between the searched coordinates and each document
	 * in search results. To be used with some others "geo" features.
	 */
	public function getDistanceInResults() {
		return $this->scoring(null, 1, false, self::SCORING_DISTANCE);
	}
}