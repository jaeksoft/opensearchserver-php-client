<?php
namespace OpenSearchServer;

use OpenSearchServer\RequestJson;
/**
 * Main abstract Search class containing common methods.
 * Used by OpenSearchServer\Search\Search, OpenSearchServer\MoreLikeThis\MoreLikeThis, ...
 * @author Alexandre Toyer
 */
abstract class Search extends RequestJson
{
	/**
	 * @return OpenSearchServer\Search
	 */
	public function start($start) {
		$this->data['start'] = $start;
		return $this;
	}

	/**
	 * @return OpenSearchServer\Search
	 */
	public function rows($rows) {
		$this->data['rows'] = $rows;
		return $this;
	}

	/**
	 * @return OpenSearchServer\Search
	 */
	public function lang($lang) {
		$this->data['lang'] = $lang;
		return $this;
	}

	/**
	 * Add a QueryFilter on request
	 * @return OpenSearchServer\Search
	 */
	public function queryFilter($filter) {
		$this->addFilter($filter, self::QUERY_FILTER, false);
		return $this;
	}

	/**
	 * Add a negative QueryFilter on request
	 * @return OpenSearchServer\Search
	 */
	public function negativeFilter($filter) {
		$this->addFilter($filter, self::QUERY_FILTER, true);
		return $this;
	}

	/**
	 * Add a GeoFilter on request
	 * @return OpenSearchServer\Search
	 */
	public function geoFilter($shape = self::GEO_FILTER_SQUARED, $unit = self::GEO_FILTER_KILOMETERS, $distance) {
		$this->addFilter($filter, self::GEO_FILTER, false, $shape, $unit, $distance);
		return $this;
	}

	/**
	 * Add a negative GeoFilter on request
	 * @return OpenSearchServer\Search
	 */
	public function negativeGeoFilter($shape = self::GEO_FILTER_SQUARED, $unit = self::GEO_FILTER_KILOMETERS, $distance) {
		$this->addFilter($filter, self::GEO_FILTER, true, $shape, $unit, $distance);
		return $this;
	}

	/**
	 * Configure fields to return
	 * @param array or string $fields
	 * @return OpenSearchServer\Search
	 */
	public function returnedFields($fields) {
		if(empty($this->data['returnedFields'])) {
			$this->data['returnedFields'] = array();
		}
		$this->data['returnedFields'] = array_unique(array_merge($this->data['returnedFields'], (array)$fields));
		return $this;
	}

	/******************************
	 *     HELPER AND ALIASES
	 ******************************/
	/**
	 * Helper method (alias)
	 * @see queryFilter()
	 * @return OpenSearchServer\Search
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