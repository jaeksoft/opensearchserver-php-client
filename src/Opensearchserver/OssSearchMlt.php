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
class OssSearchMlt extends OssSearchAbstract
{
    protected $start;
    protected $rows;
    protected $lang;
    protected $analyzer;
    protected $filter;
    protected $field;
    protected $docQuery;
    protected $likeText;
    protected $minWordLen;
    protected $maxWordLen;
    protected $minDocFreq;
    protected $minTermFreq;
    protected $maxNumTokensParsed;
    protected $stopWords;

    /**
     * @param $enginePath The URL to access the OSS Engine
     * @param $index The index name
     * @return OssSearchMlt
     */
    public function __construct($enginePath, $index = null, $rows = null, $start = null, $login = null, $apiKey = null)
    {
        parent::__construct($enginePath, $index, $login, $apiKey);

        $this->rows($rows);
        $this->start($start);

        $this->lang = null;
        $this->analyzer = null;
        $this->field    = array();
        $this->filter    = array();
        $this->docQuery = null;
        $this->likeText = null;
        $this->minWordLen = null;
        $this->maxWordLen = null;
        $this->minDocFreq = null;
        $this->minTermFreq = null;
        $this->maxNumTokensParsed = null;
        $this->stopWords = null;
    }

    /**
     * @return OssSearchMlt
     */
    public function start($start = null)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return OssSearchMlt
     */
    public function rows($rows = null)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * Specify the query to identify one document
     * @param string $query string
     * @return OssSearchMlt
     */
    public function docQuery($docQuery = null)
    {
        $this->docQuery = $docQuery;

        return $this;
    }

    /**
     *
     * @param string $likeText
     * @return OssSearchMlt
     */
    public function likeText($likeText = null)
    {
        $this->likeText = $likeText;

        return $this;
    }

    /**
     * @return OssSearchMlt
     */
    public function filter($filter = null)
    {
        $this->filter[] = $filter;

        return $this;
    }

    /**
     * @return OssSearchMlt
     */
    public function lang($lang = null)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return OssSearchMlt
     */
    public function analyzer($analyzer = null)
    {
        $this->analyzer = $analyzer;

        return $this;
    }

    /**
     * @return OssSearchMlt
     */
    public function field($fields = null)
    {
        if ($fields != null) {
            $this->field = array_unique(array_merge($this->field, (array) $fields));
        }

        return $this;
    }

    /**
     * @param int $minWordLen
     * @return OssSearchMlt
     */
    public function minWordLen($minWordLen = null)
    {
        $this->minWordLen = $minWordLen;

        return $this;
    }

    /**
     * @param int $maxWordLen
     * @return OssSearchMlt
     */
    public function maxWordLen($maxWordLen = null)
    {
        $this->maxWordLen = $maxWordLen;

        return $this;
    }

    /**
     * @param int $minDocFreq
     * @return OssSearchMlt
     */
    public function minDocFreq($minDocFreq = null)
    {
        $this->minDocFreq = $minDocFreq;

        return $this;
    }

    /**
     * @param int $minTermFreq
     * @return OssSearchMlt
     */
    public function minTermFreq($minTermFreq = null)
    {
        $this->minTermFreq = $minTermFreq;

        return $this;
    }

    /**
     * @param int $maxNumTokensParsed
     * @return OssSearchMlt
     */
    public function maxNumTokensParsed($maxNumTokensParsed = null)
    {
        $this->maxNumTokensParsed = $maxNumTokensParsed;

        return $this;
    }

    /**
     *
     * @param string $stopWords
     * @return OssSearchMlt
     */
    public function stopWords($stopWords = null)
    {
        $this->stopWords = $stopWords;

        return $this;
    }

    /**
     *
     * @param array $queryChunks
     */
    protected function addParams($queryChunks = null)
    {
        $queryChunks = parent::addParams($queryChunks);

        if (!empty($this->lang)) {
            $queryChunks[] = 'lang=' . $this->lang;
        }

        if ($this->rows     !== null) {
            $queryChunks[] = 'rows='    . (int) $this->rows;
        }

        if ($this->start !== null) {
            $queryChunks[] = 'start=' . (int) $this->start;
        }

        // Filters
        foreach ((array) $this->filter as $filter) {
            if (empty($filter)) {
                continue;
            }
            $queryChunks[] = 'fq=' . urlencode($filter);
        }

        // Fields
        foreach ((array) $this->field as $field) {
            if (empty($field)) continue;
            $queryChunks[] = 'rf=' . $field;
        }

        if ($this->likeText != null) {
            $queryChunks[] = 'mlt.liketext='.urlencode($this->likeText);
        }
        if ($this->docQuery != null) {
            $queryChunks[] = 'mlt.docquery=' . urlencode($this->docQuery);
        }
        if ($this->minWordLen != null) {
            $queryChunks[] = 'mlt.minwordlen=' . (int) $this->minWordLen;
        }
        if ($this->maxWordLen != null) {
            $queryChunks[] = 'mlt.maxwordlen=' . (int) $this->maxWordLen;
        }
        if ($this->minDocFreq != null) {
            $queryChunks[] = 'mlt.mindocfreq=' . (int) $this->minDocFreq;
        }
        if ($this->minTermFreq != null) {
            $queryChunks[] = 'mlt.mintermfreq=' . (int) $this->minTermFreq;
        }
        if ($this->maxNumTokensParsed != null) {
            $queryChunks[] = 'mlt.maxnumtokensparsed=' . (int) $this->maxNumTokensParsed;
        }
        if ($this->moreLikeThis['stopwords']) {
            $queryChunks[] = 'mlt.stopwords=' . urlencode($this->moreLikeThis['stopwords']);
        }

        return $queryChunks;
    }
}
