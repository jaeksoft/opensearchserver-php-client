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

namespace Opensearchserver\OssIndexDocument;

/**
 * @author pmercier <pmercier@open-search-server.com>
 * @package OpenSearchServer
 */
class Field extends \ArrayObject
{
    /** @var \Opensearchserver\OssIndexDocument\Document */
    protected $document;

    /** @var string */
    protected $name;

    /**
     * @param \Opensearchserver\OssIndexDocument\Document $document
     * @param string $name The name of the field
     * @return \Opensearchserver\OssIndexDocument\Field
     */
    public function __construct(Document $document, $name)
    {
        $this->document = $document;
        $this->name = $name;
    }

    /**
     * Return the name of the field
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Create a new value inside the field
     * @param string $value The string to append
     * @param boolean $removeTag Ask the indexator to remove the tags
     * @return \Opensearchserver\OssIndexDocument\Value
     */
    public function newValue($value, $removeTag = false)
    {
        $value = new Value($this, $value);
        $value->setRemoveTag($removeTag);
        $this->append($value);

        return $value;
    }

    /**
     * Add one or many values to the field
     * @param mixed $values The string to append. Can be an Array<String>
     */
    public function addValues($values)
    {
        foreach ((array) $values as $value) {
            $this->append(new Value($this, $value));
        }
    }

    /**
     * @param mixed $offset
     * @param \Opensearchserver\OssIndexDocument\Value $value
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Value) {
            throw new \UnexpectedValueException("\Opensearchserver\OssIndexDocument\Value was expected.");
        }
        parent::offsetSet($offset, $value);
    }

    /**
     * @param mixed $offset
     * @param \Opensearchserver\OssIndexDocument\Value $value
     */
    public function append($value)
    {
        if (!$value instanceof Value) {
            throw new \UnexpectedValueException("\Opensearchserver\OssIndexDocument\Value was expected.");
        }
        parent::append($value);
    }

    public function __toString()
    {
        $return = '';
        foreach ($this as $value) {
            $value = $value->__toString();
            if ($value !== false) {
                $return .= $value;
            }
        }
        if (empty($return)) {
            return null;
        }

        return '<field name="' . $this->name . '">' . $return . '</field>';
    }
}
