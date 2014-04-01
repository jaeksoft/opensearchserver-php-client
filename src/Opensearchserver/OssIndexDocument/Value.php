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
class Value
{
    /** @var string */
    private $field;

    /** @var boolean */
    private $removeTag = false;

    /** @var string */
    private $value = '';

    /**
     * @param \Opensearchserver\OssIndexDocument\Field $field
     * @param string $value The value
     * @return \Opensearchserver\OssIndexDocument\Value
     */
    public function __construct(Field $field, $value)
    {
        $this->field = $field;
        $this->value = (string) $value;
    }

    /**
     * Set the value
     * @param string $value The value
     */
    public function setValue($value)
    {
        $this->value = (string) $value;
    }

    /**
     * Retrieve the value
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     *    Ask the indexator to remove the tags
     * @param boolean $bool
     */
    public function setRemoveTag($bool)
    {
        $this->removeTag = (bool) $bool;
    }

    /**
     * @return boolean
     */
    public function getRemoveTag()
    {
        return $this->removeTag;
    }

    public function __toString()
    {
        $data = str_replace(']]>', ']]]]><![CDATA[>', $this->value);
        if (empty($data)) {
            return null;
        }
        $return = '<value';
        if ($this->removeTag) {
            $return .= ' removeTag="yes"';
        }

        return $return . '><![CDATA[' . $data . ']]></value>';
    }
}
