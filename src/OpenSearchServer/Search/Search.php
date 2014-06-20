<?php
namespace OpenSearchServer\Search;

use OpenSearchServer\RequestJson;

abstract class RequestJson extends RequestJson
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
	 * @return OpenSearchServer\Search\Search
	 */
	public function start($start) {
		$this->data['start'] = $start;
		return $this;
	}

	/**
	 * @return OpenSearchServer\Search\Search
	 */
	public function rows($rows) {
		$this->data['rows'] = $rows;
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
	public function lang($lang) {
		$this->data['lang'] = $lang;
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
	 * Add a QueryFilter on request
	 * @return OpenSearchServer\Search\Search
	 */
	public function queryFilter($filter) {
		$this->addFilter($filter, self::QUERY_FILTER, false);
		return $this;
	}

	/**
	 * Add a negative QueryFilter on request
	 * @return OpenSearchServer\Search\Search
	 */
	public function negativeFilter($filter) {
		$this->addFilter($filter, self::QUERY_FILTER, true);
		return $this;
	}

	/**
	 * Add a GeoFilter on request
	 * @return OpenSearchServer\Search\Search
	 */
	public function geoFilter($shape = self::GEO_FILTER_SQUARED, $unit = self::GEO_FILTER_KILOMETERS, $distance) {
		$this->addFilter($filter, self::GEO_FILTER, false, $shape, $unit, $distance);
		return $this;
	}

	/**
	 * Add a negative GeoFilter on request
	 * @return OpenSearchServer\Search\Search
	 */
	public function negativeGeoFilter($shape = self::GEO_FILTER_SQUARED, $unit = self::GEO_FILTER_KILOMETERS, $distance) {
		$this->addFilter($filter, self::GEO_FILTER, true, $shape, $unit, $distance);
		return $this;
	}

	/**
	 * Configure fields to return
	 * @param array or string $fields
	 * @return OpenSearchServer\Search\Search
	 */
	public function returnedFields($fields) {
		if(empty($this->data['returnedFields'])) {
			$this->data['returnedFields'] = array();
		}
		$this->data['returnedFields'] = array_unique(array_merge($this->data['returnedFields'], (array)$fields));
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
			'$queryString' => $queryString,
			'localField' => $localField,
			'$foreignField' => $foreignField,
			'type' => $type,
			'returnFields' => $returnFields,
			'returnScores' => $returnScores,
			'returnFacets' => $returnFacets			
		);
		return $this;
	}

	/******************************
	 *     HELPER AND ALIASES
	 ******************************/
	/**
	 * Helper method (alias)
	 * @see queryFilter()
	 * @return OpenSearchServer\Search\Search
	 */
	public function filter($filter) {
		return $this->queryFilter($filter);
	}

	/**
	 * Helper method: set a QueryFilter by specifying field and value.
	 * Value can be an array of values, that will be joined with $join
	 * @param string $field Field on which filter.
	 * @param string $filter Value of the filter.
	 * @param string $join Type of join. Default is OR.
	 * @param string $addQuotes Whether or not add quotes around values.
	 */
	function filterField($field, $filter, $join = self::OPERATOR_OR, $addQuotes = false) {
		$quotes = ($addQuotes) ? '"' : '';
		if(is_array($filter)) {
			$filterString = $field . ':'.$quotes.'' . implode(''.$quotes.' '.$join.' '.$field.':'.$quotes.'', $filter ) .''.$quotes.'';
		}
		else {
			$filterString = $field . ':'.$quotes.'' . $filter .''.$quotes.'';
		}
		return $this->queryFilter($filterString);
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

	/******************************
	 * INHERITED METHODS OVERRIDDEN
	 ******************************/
	/**
	 * {@inheritdoc}
	 */
	public function getHeaders()
	{
		return array(
        	'Content-Type' => 'application/json'
        	);
	}

	/******************************
	 *       PRIVATE METHODS
	 ******************************/
	private function addFilter($value, $type, $isNegative, $shape = null, $unit = null, $distance = null) {
		if(empty($this->data['filters'])) {
			$this->data['filters'] = array();
		}

		$newFilter = array(
			'type' => $type,
			'negative' => (boolean)$isNegative,
		);
		//add options depending on type of filter
		switch($type) {
			case self::GEO_FILTER:
				$newFilter['shape'] = $shape;
				$newFilter['unit'] = $unit;
				$newFilter['distance'] = $distance;
				break;
			case self::QUERY_FILTER:
				$newFilter['query'] = $value;
				break;
		}
		$this->data['filters'][] = $newFilter;
	}
}