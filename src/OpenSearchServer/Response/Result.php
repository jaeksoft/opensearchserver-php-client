<?php
namespace OpenSearchServer\Response;

class Result
{
	protected $fields = array();
	protected $snippets = array();
	protected $pos;
	protected $score;
	protected $collapsedCount;
	
    public function __construct(\stdClass $resultItem) {
        $this->pos = (!empty($resultItem->pos)) ? $resultItem->pos : null;
        $this->score = (!empty($resultItem->score)) ? $resultItem->score : null;
        $this->collapsedCount = (!empty($resultItem->collapsedCount)) ? $resultItem->collapsedCount : null;
        
        //handle fields
        if(!empty($resultItem->fields)) {
            foreach($resultItem->fields as $fieldObject) {
                $fieldValue = null;
                if(!empty($fieldObject->values)) {
                    $fieldValue = $fieldObject->values;
                }
                //add field to array of fields
                $this->fields[$fieldObject->fieldName] = $fieldValue; 
            }
        }
        
        //handle snippets
        if(!empty($resultItem->snippets)) {
            foreach($resultItem->snippets as $fieldObject) {
                $fieldValue = null;
                if(!empty($fieldObject->values)) {
                    $fieldValue = $fieldObject->values;
                }
                //add snippet to array of snippets
                $this->snippets[$fieldObject->fieldName] = $fieldValue; 
            }
        }
    }    

    public function getPos() {
    	return $this->pos;
    }
    public function getScore() {
    	return $this->score;
    }
    public function getCollapsedCount() {
    	return $this->collapsedCount;
    }
    
    /**
     * Return value of a field 
     * @param string $fieldName
     * @param boolean $returnFirstValueOnly Return every values (useful for a multivalued field), or only 
     * 										first value (useful for most of the cases when there is only one value).
     * 										Default value: true.
     */
    public function getField($fieldName, $returnFirstValueOnly = true) {
        if(!empty($this->fields[$fieldName]) && count($this->fields[$fieldName] > 0)) {
            return ($returnFirstValueOnly) ? $this->fields[$fieldName][0] : $this->fields[$fieldName];
        }
    }
    
    /**
     * Return value of a snippet
     * @param string $fieldName
     * @param boolean $returnFirstValueOnly Return every values (useful for a multivalued snippets), or only 
     * 										first value (useful for most of the cases when there is only one value).
     * 										Default value: true.
     */
    public function getSnippet($fieldName, $returnFirstValueOnly = true) {
        if(!empty($this->snippets[$fieldName]) && count($this->snippets[$fieldName] > 0)) {
            return ($returnFirstValueOnly) ? $this->snippets[$fieldName][0] : $this->snippets[$fieldName];
        }
    }

    public function getAvailableFields($returnAllWithoutValues = false) {
        return $this->getAvailablesValues($this->fields, $returnAllWithoutValues);      
    }
    
    public function getAvailableSnippets($returnAllWithoutValues = false) {
        return $this->getAvailablesValues($this->snippets, $returnAllWithoutValues);    
    }
    
    static function getAvailablesValues($array, $returnAllWithoutValues = false) {
        if($returnAllWithoutValues) {
            return array_keys($array);
        } else {
            $fieldsWithValues = array();
            foreach($array as $name => $value) {
                if(!empty($value)) {
                    $fieldsWithValues[] = $name;
                }
            }
            return $fieldsWithValues;
        }    
    }
}