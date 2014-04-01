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

use Opensearchserver\OssIndexDocument;
use Opensearchserver\OssApi;

/**
 * @author pmercier <pmercier@open-search-server.com>
 * @package OpenSearchServer
 */
class Document extends \ArrayObject
{
    /** @var OssIndexDocument */
    private $indexDocument;

    /** @var string */
    private $language = null;

    /** @var Array<OSS_DocumentField> */
    private $fieldByName = array();

    /**    @var Array<URL> */
    private $binaries = array();

    /**
     * @param OssIndexDocument $indexDocument
     * @param string $language ISO 639-1 format (en, de, fr, ...)
     * @return OSS_DocumentNode
    */
    public function __construct(OssIndexDocument $indexDocument, $language = '')
    {
        $this->indexDocument = $indexDocument;
        $this->setLanguage($language);
    }

    /**
     * Define the document language
     * @param string $language ISO 639-1 format (en, de, fr, ...). Null to unset the language attribute.
     * @return boolean True if language is supported. Null if language was unset.
     * @throw UnexpectedValueException When language is not supported
     */
    public function setLanguage($language)
    {
        static $supportedLanguages = null;

        if ($language === null) {
            $this->language = $language;

            return null;
        }

        if ($supportedLanguages === null) {
            $supportedLanguages = OssApi::supportedLanguages();
        }

        if (isset($supportedLanguages[$language])) {
            $this->language = (string) $language;
        } else {
            if (class_exists('OssException')) {
                throw new \UnexpectedValueException('Language "' . $language . '" is not supported.');
            }
            trigger_error(__CLASS__ . '::' . __METHOD__ . '($lang): Language "' . $language . '" is not supported.', E_USER_ERROR);

            return false;
        }

        return true;
    }

    /**
     * Return the defined language of the document
     * @return string ISO 639-1 format (en, de, fr, ...)
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Create a new field inside the document
     * @param string $name The name of the field
     * @param mixed $values The string to append. Can be an Array<String>
     * @return \Opensearchserver\OssIndexDocument\Field
     * Note: If the field by that name already exist, it'll be returned
     */
    public function newField($name, $values = null)
    {
        if (isset($this->fieldByName[$name])) {
            return $this->fieldByName[$name];
        }
        $field = new Field($this, $name);
        $this->append($field);
        if ($values !== null) {
            $field->addValues($values);
        }

        return $field;
    }

    /**
     * Add a binary entry for an external file to index
     * @param URI $uri The URL to the file to index
     * @param string $faultTolerant
     */
    public function newBinaryUrl($uri, $faultTolerant = true)
    {
        $this->binaries[] = new BinaryUrl($uri, $faultTolerant);
    }

    /**
     * Retrieve a field using his name
     * @param string $name The name of the field to retrieve
     * @return \Opensearchserver\OssIndexDocument\Field If field don't exist, null is returned
     */
    public function getField($name)
    {
        if (isset($this->fieldByName[$name])) {
            return $this->fieldByName[$name];
        }

        return null;
    }

    /**
     * @param mixed $offset
     * @param \Opensearchserver\OssIndexDocument\Field $field
     */
    public function offsetSet($offset, $field)
    {
        if (!$field instanceof Field) {
            throw new \UnexpectedValueException("\Opensearchserver\OssIndexDocument\Field was expected.");
        }
        parent::offsetSet($offset, $field);
        $this->fieldByName[$field->getName()] = $field;
    }

    /**
     * @param \Opensearchserver\OssIndexDocument\Field $field
     */
    public function append($field)
    {
        if (!$field instanceof Field) {
            throw new \UnexpectedValueException("\Opensearchserver\OssIndexDocument\Field was expected.");
        }
        $fieldName = $field->getName();
        if (isset($this->fieldByName[$fieldName])) {
            $storedField = $this->fieldByName[$fieldName];
            foreach ($field as $value) {
                $storedField->append($value);
            }
        } else {
            parent::append($field);
            $this->fieldByName[$field->getName()] = $field;
        }
    }

    public function __toString()
    {
        $data = '';
        foreach ($this as $field) {
            $field = $field->__toString();
            $data .= $field;
        }
        foreach ($this->binaries as $binary) {
            $data .= $binary->__toString();
        }

        if (empty($data)) {
            return null;
        }
        $return = '<document';
        if ($this->language !== null) {
            $return .= ' lang="' . $this->language . '"';
        }
        $return .= '>';

        return $return . $data . '</document>';
    }
}
