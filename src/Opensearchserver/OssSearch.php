<?php
/*
*  This file is part of OpenSearchServer PHP Client.
*
*  Copyright (C) 2013 Emmanuel Keller / Jaeksoft
*
*  http://www.open-search-server.com
*
*  OpenSearchServer PHP Client is free software: you can redistribute it and/or modify
*  it under the terms of the GNU Lesser General Public License as published by
*  the Free Software Foundation, either version 3 of the License, or
*  (at your option) any later version.
*
*  OpenSearchServer PHP Client is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU Lesser General Public License for more details.
*
*  You should have received a copy of the GNU Lesser General Public License
*  along with OpenSearchServer PHP Client.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * @file
 * Class to access OpenSearchServer API
 */

namespace Opensearchserver;

/**
 * @package OpenSearchServer
*/
class OssSearch extends OssSearchAbstract
{
    protected $query;
    protected $start;
    protected $rows;
    protected $lang;
    protected $filter;
    protected $negativeFilter;
    protected $field;
    protected $sort;
    protected $sortClear;
    protected $operator;
    protected $collapse;
    protected $facet;
    protected $join;
    protected $joinFilter;
    protected $joinNegativeFilter;

    /**
     * @param $enginePath The URL to access the OSS Engine
     * @param $index The index name
     * @return OssSearch
     */
    public function __construct($enginePath, $index = null, $rows = null, $start = null, $login = null, $apiKey = null)
    {
        parent::__construct($enginePath, $index, $login, $apiKey);

        $this->rows($rows);
        $this->start($start);

        $this->field = array();
        $this->filter = array();
        $this->negativeFilter = array();
        $this->sortClear = false;
        $this->sort = array();
        $this->facet = array();
        $this->join = array();
        $this->joinFilter = array();
        $this->joinNegativeFilter = array();
        $this->query = null;
        $this->lang = null;
        $this->operator = null;
        $this->collapse = array('field' => null, 'max' => null, 'mode' => null, 'type' => null);
    }

    /**
     * Specify the query
     * @param $query string
     * @return OssSearch
     */
    public function query($query = null)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return OssSearch
     */
    public function start($start = null)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return OssSearch
     */
    public function rows($rows = null)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * Set the default operation OR or AND
     * @param unknown_type $start
     * @return OssSearch
     */
    public function operator($operator = null)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return OssSearch
     */
    public function filter($filter = null)
    {
        $this->filter[] = $filter;

        return $this;
    }

    /**
     * @return OssSearch
     */
    public function negativeFilter($negativeFilter = null)
    {
        $this->negativeFilter[] = $negativeFilter;

        return $this;
    }

    /**
     * @return OssSearch
     */
    public function lang($lang = null)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return OssSearch
     */
    public function field($fields)
    {
        $this->field = array_unique(array_merge($this->field, (array) $fields));

        return $this;
    }

    /**
     * @param string $clear
     * @return OssSearch
     */
    public function sortClear($clear = true) {
      $this->sortClear = $clear;
      return $this;
    }

    /**
     * @return OssSearch
     */
    public function sort($fields)
    {
        if (isArray($fields)) {
            foreach ((array) $fields as $field)
                $this->sort[] = $field;
        } else {
            $this->sort[] = $fields;
        }
        return $this;
    }

    /**
     * @return OssSearch
     */
    public function collapseField($field)
    {
        $this->collapse['field'] = $field;

        return $this;
    }

    /**
     * @return OssSearch
     */
    public function collapseMode($mode)
    {
        $this->collapse['mode'] = $mode;

        return $this;
    }

    /**
     * @return OssSearch
     */
    public function collapseType($type)
    {
        $this->collapse['type'] = $type;

        return $this;
    }

    /**
     * @return OssSearch
     */
    public function collapseMax($max)
    {
        $this->collapse['max'] = $max;

        return $this;
    }

    /**
     * @return OssSearch
     */
    public function facet($field, $min = null, $multi = false, $multi_collapse = false)
    {
        $this->facet[$field] = array('min' => $min, 'multi' => $multi, 'multi_collapse' => $multi_collapse);

        return $this;
    }

    /**
     * @return OssSearch
     */
    public function join($position, $value)
    {
        $intpos = (int) $position;
        $this->join[$intpos] = $value;

        return $this;
    }

    /**
     * @return OssSearch
     */
    public function joinFilter($position, $filter = null)
    {
        $intpos = (int) $position;
        if (!array_key_exists($intpos, $this->joinFilter)) {
            $this->joinFilter[$intpos] = array();
        }
        $this->joinFilter[$intpos][] = $filter;

        return $this;
    }

    /**
     * @return OssSearch
     */
    public function joinNegativeFilter($position, $negativeFilter = null)
    {
        $intpos = (int) $position;
        if (!array_key_exists($intpos, $this->joinNegativeFilter)) {
            $this->joinFilter[$intpos] = array();
        }
        $this->joinNegativeFilter[$intpos][] = $negativeFilter;

        return $this;
    }

    protected function addParams($queryChunks = null)
    {
        $queryChunks = parent::addParams($queryChunks);

        $queryChunks[] = 'q=' . urlencode($this->query);

        if (!empty($this->lang)) {
            $queryChunks[] = 'lang=' . $this->lang;
        }

        if ($this->rows     !== null) {
            $queryChunks[] = 'rows='    . (int) $this->rows;
        }

        if ($this->start !== null) {
            $queryChunks[] = 'start=' . (int) $this->start;
        }

        if ($this->operator !== null) {
            $queryChunks[] = 'operator=' . $this->operator;
        }

        // Sorting
        if ($this->sortClear) {
            $queryChunks[] = 'sort.clear';
        }
        $i = 1;
        foreach ((array) $this->sort as $sort) {
            if (empty($sort)) {
                continue;
            }
            $queryChunks[] = 'sort'. $i .'=' . urlencode($sort);
            $i++;
        }

        // Filters
        foreach ((array) $this->filter as $filter) {
            if (empty($filter)) {
                continue;
            }
            $queryChunks[] = 'fq=' . urlencode($filter);
        }

        // Negative Filters
        foreach ((array) $this->negativeFilter as $negativeFilter) {
            if (empty($negativeFilter)) {
                continue;
            }
            $queryChunks[] = 'fqn=' . urlencode($negativeFilter);
        }

        // Fields
        foreach ((array) $this->field as $field) {
            if (empty($field)) continue;
            $queryChunks[] = 'rf=' . $field;
        }

        // Facets
        foreach ((array) $this->facet as $field => $options) {
            if ($options['multi']) {
                $facet = 'facet.multi=';
            } elseif ($options['multi_collapse']) {
                $facet = 'facet.multi.collapse=';
            } else {
                $facet = 'facet=';
            }
            $facet .= $field;
            if ($options['min'] !== null) {
                $facet .= '(' . $options['min'] . ')';
            }
            $queryChunks[] = $facet;
        }

        // Join query parameter
        foreach ((array) $this->join as $position => $value) {
            $queryChunks[] = 'jq'.$position.'='.urlencode($value);
        }

        // Join filters
        foreach ((array) $this->joinFilter as $position => $filters) {
            foreach ((array) $filters as $filter) {
                if (empty($filter)) {
                    continue;
                }
                $queryChunks[] = 'jq'.$position.'.fq=' . urlencode($filter);
            }
        }

        // Join negative Filters
        foreach ((array) $this->joinNegativeFilter as $position => $negativeFilters) {
            foreach ((array) $negativeFilters as $negativeFilter) {
                if (empty($negativeFilter)) {
                    continue;
                }
                $queryChunks[] = 'jq'.$position.'.fqn=' . urlencode($negativeFilter);
            }
        }

        // Collapsing
        if ($this->collapse['field']) {
            $queryChunks[] = 'collapse.field=' . $this->collapse['field'];
        }
        if ($this->collapse['type']) {
            $queryChunks[] = 'collapse.type=' . $this->collapse['type'];
        }
        if ($this->collapse['mode'] !== null) {
            $queryChunks[] = 'collapse.mode=' . $this->collapse['mode'];
        }
        if ($this->collapse['max'] !== null) {
            $queryChunks[] = 'collapse.max=' . (int) $this->collapse['max'];
        }

        return $queryChunks;
    }
}
