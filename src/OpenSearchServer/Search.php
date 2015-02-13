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
	 * Add a RelativeDateFilter on request
	 * @return OpenSearchServer\Search
	 */
	public function relativeDateFilter($field, $fromUnit = self::RELATIVE_DATE_FILTER_UNIT_MINUTES, $fromInterval, $toUnit = self::RELATIVE_DATE_FILTER_UNIT_MINUTES, $toInterval = 0, $dateFormat = self::RELATIVE_DATE_FILTER_DATEFORMAT, $isNegative = false) {
		$parameters = array(
	        'fromUnit' => $fromUnit,
	        'fromInterval' => $fromInterval,
	        'toUnit' => $toUnit,
		    'toInterval' => $toInterval,
		    'dateFormat' => $dateFormat,
		    'field' => $field
	    );
	    $this->addFilter(null, self::RELATIVE_DATE_FILTER, $isNegative, $parameters);
		return $this;
	}
	
	/**
	 * Add a negative RelativeDateFilter on request
	 * @return OpenSearchServer\Search
	 */
	public function negativeRelativeDateFilter($field, $fromUnit = self::RELATIVE_DATE_FILTER_UNIT_MINUTES, $fromInterval, $toUnit = self::RELATIVE_DATE_FILTER_UNIT_MINUTES, $toInterval = 0, $dateFormat = self::RELATIVE_DATE_FILTER_DATEFORMAT, $isNegative = false) {
        return $this->relativeDateFilter($field, $fromUnit, $fromInterval, $toUnit, $toInterval, $dateFormat, true);	    
	}
	
	

	/**
	 * Add a GeoFilter on request
	 * @return OpenSearchServer\Search
	 */
	public function geoFilter($shape = self::GEO_FILTER_SQUARED, $unit = self::GEO_FILTER_KILOMETERS, $distance, $isNegative = false) {
		$parameters = array(
	        'shape' => $shape,
	        'unit' => $unit,
	        'distance' => $distance
	    );
	    $this->addFilter(null, self::GEO_FILTER, $isNegative, $parameters);
		return $this;
	}

	/**
	 * Add a negative GeoFilter on request
	 * @return OpenSearchServer\Search
	 */
	public function negativeGeoFilter($shape = self::GEO_FILTER_SQUARED, $unit = self::GEO_FILTER_KILOMETERS, $distance) {
		return $this->geoFilter(null, $shape, $unit, $distance, true);
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

	/** 
	 * Return template used by the query, if any
	 */
	public function getTemplate() {
	    return (!empty($this->options['template'])) ? $this->options['template'] : null;
	} 
	
	/******************************
	 *       PRIVATE METHODS
	 ******************************/
	private function addFilter($value = null, $type, $isNegative, $parameters = array()) {
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
				$newFilter['shape'] = $parammeters['shape'];
				$newFilter['unit'] = $parammeters['unit'];
				$newFilter['distance'] = $parammeters['distance'];
				break;
			case self::QUERY_FILTER:
				$newFilter['query'] = $value;
				break;
			case self::RELATIVE_DATE_FILTER:
			    $newFilter['from'] = array(
			    		'unit' => $parameters['fromUnit'],
			    		'interval' => $parameters['fromInterval'],
			        );
			    $newFilter['to'] = array(
			    		'unit' => $parameters['toUnit'],
			    		'interval' => $parameters['toInterval'],
			        );
			    $newFilter['field'] = $parameters['field'];
			    $newFilter['dateFormat'] = $parameters['dateFormat'];
			    break;
		}
		$this->data['filters'][] = $newFilter;
	}
}