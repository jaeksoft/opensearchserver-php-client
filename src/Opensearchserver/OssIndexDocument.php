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
 * @author pmercier <pmercier@open-search-server.com>
 * @package OpenSearchServer
 */
class OssIndexDocument extends \ArrayObject
{
    /**
     * @param string $language ISO 639-1 format (en, de, fr, ...)
     * @return \Opensearchserver\OssIndexDocument\Document
     */
    public function newDocument($language = '')
    {
        $document = new OssIndexDocument\Document($this, $language);
        $this->append($document);

        return $document;
    }

    /**
     * @param mixed $offset
     * @param \Opensearchserver\OssIndexDocument\Document $document
     */
    public function offsetSet($offset, $document)
    {
        if (!$document instanceof OssIndexDocument\Document) {
            throw new \UnexpectedValueException("\Opensearchserver\OssIndexDocument\Document was expected.");
        }
        parent::offsetSet($offset, $document);
    }

    /**
     * @param \Opensearchserver\OssIndexDocument\Document $document
     */
    public function append($document)
    {
        if (!$document instanceof OssIndexDocument\Document) {
            throw new \UnexpectedValueException("\Opensearchserver\OssIndexDocument\Document was expected.");
        }
        parent::append($document);
    }

    /**
     * @return string XML
     */
    public function toXML()
    {
        return $this->__toString();
    }

    public function __toString()
    {
        $return    = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<index>";
        foreach ($this as $document) {
            $return .= $document->__toString();
        }

        return $return . '</index>';
    }
}
