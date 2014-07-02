<?php
namespace OpenSearchServer\Response;

class ResponseIterable extends Response implements \Iterator
{
    protected $values = array();
    protected $position;

    public function setValues($values) {
        return $this->values = $values;
    }

    public function getValues() {
        return $this->values;
    }

    /************************
     * Implements methods from Iterator
     ************************/
    /*
     function rewind() {
     $this->position = 0;
     }

     function current() {
     return $this->values[$this->position];
     }

     function key() {
     return $this->position;
     }

     function next() {
     ++$this->position;
     }

     function valid() {
     return isset($this->values[$this->position]);
     }
     */

    function rewind() {
        return reset($this->values);
    }
    function current() {
        return current($this->values);
    }
    function key() {
        return key($this->values);
    }
    function next() {
        return next($this->values);
    }
    function valid() {
        return key($this->values) !== null;
    }
}