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
class OssSearchAbstract extends OssAbstract
{
    const API_SELECT     = 'select';

    protected $template;
    protected $log;
    protected $customLogs;

    protected $user = '';
    protected $groups = array();

    /**
     * @param $enginePath The URL to access the OSS Engine
     * @param $index The index name
     * @return OssSearch
     */
    public function __construct($enginePath, $index = null, $login = null, $apiKey = null)
    {
        $this->init($enginePath, $index, $login, $apiKey);
        $this->log = false;
        $this->customLogs = array();
    }

    /**
     * @return OssSearch
     */
    public function template($template = null)
    {
        $this->template = $template;

        return $this;
    }

    public function setLog($log = false)
    {
        $this->log = $log;
    }

    public function setCustomLog($pos, $log)
    {
        $this->customLogs[(int) $pos] = $log;
    }

    /**
     * @return SimpleXMLElement False if the query produced an error
     * FIXME Must think about OssApi inteegration inside OssSearch
     */
    public function execute($connectTimeOut = null, $timeOut = null)
    {
        $queryChunks = array();
        $queryChunks = $this->addParams($queryChunks);
        $params = implode('&', $queryChunks);

        $result = $this->queryServerXML(OssSearch::API_SELECT, $params, $connectTimeOut, $timeOut);
        if ($result === false) {
            return false;
        }

        return $result;
    }

    protected function addParams($queryChunks = null)
    {
        if (!empty($this->template)) {
            $queryChunks[] = 'qt='     . $this->template;
        }

        // Logs and customLogs
        if ($this->log) {
            $queryChunks[] = 'log=' . $this->log;
        }
        foreach ($this->customLogs as $pos => $customLog) {
            $queryChunks[] = 'log' . $pos . '=' . urlencode($customLog);
        }

        /*
        //User
        $queryChunks[] = 'user='.urlencode($this->user);

        //Groups
        foreach($this->groups as $group) {
                $queryChunks[] = 'group='.urlencode($group);
        }
        */

        return $queryChunks;
    }
}
