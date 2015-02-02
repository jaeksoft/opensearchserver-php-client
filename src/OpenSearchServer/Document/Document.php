<?php
namespace OpenSearchServer\Document;

class Document
{
    protected $lang;
    protected $fields = array();

    public function lang($lang) {
        $this->lang = $lang;
        return $this;
    }

    /**
     * Add a 'field' entry for this document
     * @param String $fieldName
     * @param String $fieldValue
     * @param int $fieldBoost
     * @throws \InvalidArgumentException
     */
    public function field($fieldName, $fieldValue = '', $fieldBoost = null) {
        if(empty($fieldName)) {
            throw new \InvalidArgumentException('Please provide a fieldname');
        }
        $field = array(
			'name'=> $fieldName,
			'value' => $fieldValue
        );
        if(!empty($fieldBoost)) {
            $field['boost'] = $fieldBoost;
        }
        $this->fields[] = $field;
        return $this;
    }


    public function toArray() {
        return array(
			'lang' => $this->lang,
			'fields' => $this->fields
        );
    }

    /**
     * Return value(s) for one field 
     * @param String $fieldName
     */
    public function getField($fieldName) {
        if(empty($fieldName)) {
            throw new \InvalidArgumentException('Please provide a fieldname');
        }
        
        $values = array();
        foreach($this->fields as $field) {
            if($field['name'] == $fieldName) {
                $values[] = $field['value'];
            }
        }
        
        return $values;
    }
}