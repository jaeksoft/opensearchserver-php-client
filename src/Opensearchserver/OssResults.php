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

namespace Opensearchserver;

/**
 * @file
 * Class to access OpenSearchServer API
 * @author philcube <egosse@open-search-server.com>
 * @package OpenSearchServer
 */
class OssResults
{
    /* @var SimpleXMLElement */
    protected $result;
    protected $resultFound;
    protected $resultTime;
    protected $resultRows;
    protected $resultStart;
    protected $resultCollapsedCount;

    /**
     * @param $result The data
     * @param $model The list of fields
     * @return OssApi
     */
    public function __construct(\SimpleXMLElement $result, $model = null)
    {
        $this->result    = $result;
        $this->resultFound = (int) $this->result->result['numFound'];
        $this->resultTime = (float) $this->result->result['time'] / 1000;
        $this->resultRows = (int) $this->result->result['rows'];
        $this->resultStart = (int) $this->result->result['start'];
        $this->resultCollapsedCount = (int) $this->result->result['collapsedDocCount'];
    }
    public function getResultCollapsedCount()
    {
        return $this->resultCollapsedCount;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getResultFound()
    {
        return $this->resultFound;
    }

    public function getResultTime()
    {
        return $this->resultTime;
    }

    public function getResultRows()
    {
        return $this->resultRows;
    }

    public function getResultStart()
    {
        return $this->resultStart;
    }

    /**
     *    GETTER
     */
    public function getField($position, $fieldName, $modeSnippet = false, $highlightedOnly = false, $joinPosition = null, $getMultipleValues = false)
    {
        $field = null;
        $joinPrefix = '';

        if ($joinPosition != null) {
            $joinPrefix = '/join[@paramPosition="jq' . (int) $joinPosition . '"]';
        }

        $doc = $this->result->xpath('result/doc[@pos="' . $position . '"]' . $joinPrefix);

        if (isset($doc[0]) && is_array($doc)) {
            $value = null;
            if ($modeSnippet) {
                if ($highlightedOnly) {
                    $value = $doc[0]->xpath('snippet[@name="' . $fieldName . '" and @highlighted="yes"]');
                } else {
                    $value = $doc[0]->xpath('snippet[@name="' . $fieldName . '"]');
                }
            }
            if (!isset($value) || count($value) == 0) {
                $value = $doc[0]->xpath('field[@name="' . $fieldName . '"]');
            }
            if ($getMultipleValues && count($value)>1) {
                $tempArray = array();
                foreach ($value as $key=>$elt) {
                        $tempArray[] = $elt;
                }
                $field = $tempArray;
            } elseif (isset($value[0])) {
                $field = $value[0];
            }
        }

        return $field;
    }

    public function getScore($position)
    {
        $doc = $this->result->xpath('result/doc[@pos="' . $position . '"]');
        if (isset($doc[0]) && is_array($doc)) {
            return $doc[0]['score'];
        }

        return null;
    }

    /**
     *
     */
    public function getFields($position, $modeSnippet = false)
    {
        $doc = $this->result->xpath('result/doc[@pos="' . $position . '"]');

        if(! isset($doc[0]))
        {
            throw new \Exception('Invalid doc format');
        }

        $doc = $doc[0];
        $fields = $doc->xpath('field');
        $current = array();
        foreach ($fields as $field) {
            $name = (string) $field[0]['name'];
            $current = $this->getFieldOrMultivaluedField($current, $name, $field);
        }

        if ($modeSnippet) {
            $snippets = $doc->xpath('snippet');
            foreach ($snippets as $field) {
                $name = (string) $field[0]['name'];
                $current = $this->getFieldOrMultivaluedField($current, $name, $field);
            }
        }

        return $current;
    }

    private function getFieldOrMultivaluedField($current, $name, $field)
    {
        if(!empty($current[(string) $name]))
        {
            if(is_array($current[(string) $name]) === false)
            {
                $firstValue = $current[(string) $name];
                $current[(string) $name] = array($firstValue);
            }
            else
            {
                $current[(string) $name][] = trim($field);
            }
        }
        else
        {
            $current[(string) $name] = trim($field);
        }
        
        return $current;
    }

    /**
     *
     * @param unknown_type $fieldName
     * @return Ambigous <multitype:, null>
     */
    public function getFacet($fieldName)
    {
        $currentFacet = isset($fieldName)? $this->result->xpath('faceting/field[@name="' . $fieldName . '"]/facet'):null;
        if (!isset($currentFacet) || ( isset($currentFacet) && $currentFacet === false)) {
            $currentFacet = array();
        }

        return $currentFacet;
    }

    /**
     *
     * @return unknown_type
     */
    public function getFacets()
    {
        $facets = array();
        $allFacets = $this->result->xpath('faceting/field');
        foreach ($allFacets as $each) {
            $facets[] = $each[0]['name'];
        }

        return $facets;
    }

    /**
     *
     * @return Return the spellsuggest array.
     */
    public function getSpellSuggestions($fieldName)
    {
        $currentSpellCheck = isset($fieldName)? $this->result->xpath('spellcheck/field[@name="' . $fieldName . '"]/word/suggest'):null;
        if (!isset($currentSpellCheck) || ( isset($currentSpellCheck) && $currentSpellCheck === false)) {
            $currentSpellCheck = array();
        }

        return $currentSpellCheck;
    }

    /**
     *
     * @return Return the spellsuggest terms.
     * @deprecated Use getBestSpellSuggestion instead
     */
    public function getSpellSuggest($fieldName)
    {
        $spellCheckWord = isset($fieldName)? $this->result->xpath('spellcheck/field[@name="' . $fieldName . '"]/word'):null;
        $queryExact = '';
        if (isset($spellCheckWord) && $spellCheckWord != null) {
            foreach ($spellCheckWord as $each) {
                $queryExact .= $each[0]->suggest.' ';
            }
        }

        return $queryExact;
    }
    
    /**
     * Return the spell suggestions for one field as array, key is suggestion and value is frequency.
     * Array will be sorted with more frequent suggestions at the beginning.
     * @param fieldName string Name of the field that must be used
     */
    public function getSpellSuggestionsArray($fieldName)
    {
        $suggestions = array();
    	foreach($this->getSpellSuggestions($fieldName) as $suggestionXml)
		{
			$suggestionArray = (array)$suggestionXml[0];
			$suggestions[(string)$suggestionXml] = $suggestionArray['@attributes']['freq'];
		}
		uasort($suggestions, function($a, $b) {
		    if ($a == $b) {
		        return 0;
		    }
		
		    return ($a < $b) ? 1 : -1;
		});
		return $suggestions;
    }
    
    /**
     * Return configured fieldnames for this spellcheck template
     */
    public function getSpellSuggestionsFieldnames()
    {
		$fieldnames = array();    	
		foreach($this->result->xpath('spellcheck/field') as $xml)
		{
			$suggestionsArray = (array)$xml;
			if(!empty($suggestionsArray['@attributes']['name'])) {
				$fieldnames[] = $suggestionsArray['@attributes']['name'];
			}
		}
		return $fieldnames;
    }
    
    /**
     * Return the best suggestion for one field.
     * @param fieldName string Name of the field that must be used
     */
    public function getBestSpellSuggestion($fieldName)
    {
		$suggestions = $this->getSpellSuggestionsArray($fieldName);
		return (!empty($suggestions)) ? array_shift(array_keys($suggestions)) : '';
    }
}
