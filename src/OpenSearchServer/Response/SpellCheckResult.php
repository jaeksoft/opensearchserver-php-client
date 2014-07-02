<?php
namespace OpenSearchServer\Response;

use Buzz\Message\Response as BuzzResponse;

class SpellCheckResult extends Response
{
    private $suggestions;

    public function __construct(BuzzResponse $response, \OpenSearchServer\Request $request)
    {
        parent::__construct($response, $request);

        /* build array of suggestions:
         <fieldname> =>
             string 'suggestion' => <best suggestion>
             array 'allSuggestions' =>
                 array
                     <search word> =>
                         array
                             <suggestion> => freq
                             <suggestion> => freq
                     ...
                     	...
                 			...
                 			...
         */
        if(!empty($this->getJsonValues()->fields)) {
            $values = $this->getJsonValues()->fields;
            foreach($values as $fieldInfo) {
                $words = array();
                foreach($fieldInfo->words as $wordObj) {
                    $suggestions = array();
                    foreach($wordObj->suggest as $suggestsObj) {
                        $suggestions[$suggestsObj->term] = $suggestsObj->freq;
                    }
                    uasort($suggestions, function($a, $b) {
                        if ($a == $b) {
                            return 0;
                        }
                        return ($a < $b) ? 1 : -1;
                    });
                    $words[$wordObj->word] = $suggestions;
                }
                $this->suggestions[$fieldInfo->fieldName] = array(
                    'suggestion' => $fieldInfo->suggestion,
                    'allSuggestions' => $words
                );
            }
        }
    }

    /**
     * Return the spell suggestions for one field as array, key is searched word and value is array of suggestions: key is suggestion and value frequency.
     * Array will be sorted with more frequent suggestions at the beginning.
     */
    public function getSpellSuggestionsArray($fieldname) {
        return ($this->suggestions[$fieldname]) ? $this->suggestions[$fieldname]['allSuggestions'] : null;
    }

    public function getBestSpellSuggestion($fieldname) {
       return ($this->suggestions[$fieldname]) ? $this->suggestions[$fieldname]['suggestion'] : null;
    }

    /**
     * Alias to getBestSpellSuggestion()
     */
    public function getSpellSuggest($fieldname) {
        return $this->getBestSpellSuggestion($fieldname);
    }
}