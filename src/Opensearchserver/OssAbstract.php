<?php
/*
 *  This file is part of OpenSearchServer PHP Client.
*
*  Copyright (C) 2008-2013 Emmanuel Keller / Jaeksoft
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

namespace Opensearchserver;

abstract class OssAbstract
{

  protected $enginePath;
  protected $index;
  protected $login;
  protected $apiKey;
  protected $lastQueryString;

  protected $user = '';
  protected $groups = array();

  public function init($enginePath, $index = null, $login = null, $apiKey = null)
  {
    $this->lastQueryString = null;
    $this->enginePath = $enginePath;
    $this->index = $index;
    $this->credential($login, $apiKey);
  }

  /**
   * Assign credential information for the next queries
   * @param $login string
   * @param $apiKey string
   * If $login is empty, credential is removed
   */
  public function credential($login, $apiKey)
  {
    // Remove credentials
    if (empty($login)) {
      $this->login  = null;
      $this->apiKey  = null;

      return;
    }

    // Else parse and affect new credentials
    if (empty($login) || empty($apiKey)) {
      if (class_exists('OssException')) {
        throw new \UnexpectedValueException('You must provide a login and an api key to use credential.');
      }
      trigger_error(__CLASS__ . '::' . __METHOD__ . ': You must provide a login and an api key to use credential.', E_USER_ERROR);

      return false;
    }

    $this->login  = $login;
    $this->apiKey  = $apiKey;
  }

  /**
   * Return the url to use with curl
   * param string $apiCall The Web API to call. Refer to the OSS Wiki documentation of [Web API]
   * param string[] $options Additional query parameters
   * return string
   * Optionals query parameters are provided as a named list:
   * array(
   *   "arg1" => "value1",
   *   "arg2" => "value2"
   * )
   */
  protected function getQueryURL($apiCall, $options = null)
  {
    $path = $this->enginePath . '/' . $apiCall;
    $chunks = array();

    if (!empty($this->index)) {
      $chunks[] = 'use=' . urlencode($this->index);
    }

    // If credential provided, include them in the query url
    if (!empty($this->login)) {
      $chunks[] = "login=" . urlencode($this->login);
      $chunks[] = "key="  . urlencode($this->apiKey);
    }

    // Prepare additionnal parameters
    if (is_array($options)) {
      foreach ($options as $argName => $argValue) {
        $chunks[] = $argName . "=" . urlencode($argValue);
      }
    } elseif ($options != null) {
      $chunks[] = $options;
    }

    //User
    if(!empty($this->user)) {
        $chunks[] = 'user='.urlencode($this->user);
    }

    //Groups
    if(!empty($this->groups)) {
        foreach($this->groups as $group) {
            $chunks[] = 'group='.urlencode($group);
        }
    }

    $path .= (strpos($path, '?') !== false ? '&' : '?') . implode('&', $chunks);

    return $path;
  }

  public function setUser($value)
  {
        $this->user = $value;
  }
  public function setGroups($groups)
  {
        if (!is_array($groups)) {
            $this->groups = array($groups);
        } else {
            $this->groups = $groups;
        }
  }

  /**
   * @return string The parsed engine path
   */
  public function getEnginePath()
  {
    return $this->enginePath;
  }

  /**
   * @return string The parsed index (null if not specified)
   */
  public function getIndex()
  {
    return $this->index;
  }

  /**
   * Post data to an URL
   * @param string $url
   * @param string $data Optional. If provided will use a POST method. Only accept
   *                     data as POST encoded string or raw XML string.
   * @param int $timeout Optional. Number of seconds before the query fail
   * @return false|string
   *
   * Will fail if more than 16 HTTP redirection
   */
  protected function queryServer($url, $data = null, $connexionTimeout = OssApi::DEFAULT_CONNEXION_TIMEOUT, $timeout = OssApi::DEFAULT_QUERY_TIMEOUT)
  {
    $this->lastQueryString = $url;
    // Use CURL to post the data

    $rcurl = curl_init($url);
    curl_setopt($rcurl, CURLOPT_HTTP_VERSION, '1.0');
    curl_setopt($rcurl, CURLOPT_BINARYTRANSFER, TRUE);
    curl_setopt($rcurl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($rcurl, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($rcurl, CURLOPT_MAXREDIRS, 16);
    curl_setopt($rcurl, CURLOPT_VERBOSE, false);

    if (is_integer($connexionTimeout) && $connexionTimeout >= 0) {
      curl_setopt($rcurl, CURLOPT_CONNECTTIMEOUT, $connexionTimeout);
    }

    if (is_integer($timeout) && $timeout >= 0) {
      curl_setopt($rcurl, CURLOPT_TIMEOUT, $timeout);
    }

    // Send provided string as POST data. Must be encoded to meet POST specification
    if ($data !== null) {
      curl_setopt($rcurl, CURLOPT_POST, TRUE);
      curl_setopt($rcurl, CURLOPT_POSTFIELDS, (string) $data);
      curl_setopt($rcurl, CURLOPT_HTTPHEADER, array("Content-type: text/xml; charset=utf-8"));
    }

    set_error_handler(function() {}, E_ALL);
    $content = curl_exec($rcurl);
    restore_error_handler();

    if ($content === false) {
        throw new \RuntimeException('CURL failed to execute on URL "' . $url . '"');
    }

    $aResponse   = curl_getinfo($rcurl);

    // Must check return code
    if ($aResponse['http_code'] >= 400) {
      if (class_exists('OssException')) {
        throw new TomcatException($aResponse['http_code'], $content);
      }
      trigger_error('HTTP ERROR ' . $aResponse['http_code'] . ': "' . trim(strip_tags($content)) . '"', E_USER_WARNING);

      return false;
    }

    // FIXME Possible problem to identify Locked Index message. Must set a lock on an index to check this
    if ($this->isOSSError($content)) {
      if (class_exists('OssException')) {
        throw new OssException($content);
      } else {
          throw new \Exception($content);
      }
      trigger_error('OSS Returned an error: "' . trim(strip_tags($content)) . '"', E_USER_WARNING);

      return false;
    }

    return $content;
  }

  public function getLastQueryString()
  {
    return $this->lastQueryString;
  }

  protected function queryServerTXT($path, $params = null, $data = null, $connexionTimeout = OssApi::DEFAULT_CONNEXION_TIMEOUT, $timeout = OssApi::DEFAULT_QUERY_TIMEOUT)
  {
    return $this->queryServer($this->getQueryURL($path, $params), $data, $connexionTimeout, $timeout);
  }

  /**
   * Post data to an URL and retrieve an XML
   * @param string $url
   * @param string $data Optional. If provided will use a POST method. Only accept
   *                     data as POST encoded string or raw XML string.
   * @param int $timeout Optional. Number of seconds before the query fail
   * @return SimpleXMLElement
   * Use queryServer to retrieve an XML and check its validity
   */
  protected function queryServerXML($path, $params, $data = null, $connexionTimeout = OssApi::DEFAULT_CONNEXION_TIMEOUT, $timeout = OssApi::DEFAULT_QUERY_TIMEOUT)
  {
    $result = $this->queryServerTXT($path, $params, $data, $connexionTimeout, $timeout);
    if ($result === false) {
      return false;
    }

    // Check if we have a valid XML string from the engine
    $lastErrorLevel = error_reporting(0);
    $xmlResult = simplexml_load_string(OssApi::cleanUTF8($result));
    error_reporting($lastErrorLevel);
    if (!$xmlResult instanceof \SimpleXMLElement) {
        throw new \RuntimeException("The search engine didn't return a valid XML");
    }

    return $xmlResult;
  }

  /**
   * Check if the answer is an error returned by OSS
   * @param $xml string, DOMDocument or SimpleXMLElement
   * @return boolean True if error success
   */
  protected function isOSSError($xml)
  {
    // Cast $xml param to be a SimpleXMLElement
    // If we don't find the word 'Error' in the xml string, exit immediatly
    if ($xml instanceof \SimpleXMLElement) {
      if (strpos((string) $xml, 'Error') === false) {
        return false;
      }
      $xmlDoc = $xml;
    } elseif ($xml instanceof \DOMDocument) {
      $xmlDoc = simplexml_import_dom($xml);
      if (strpos((string) $xmlDoc, 'Error') === false) {
        return false;
      }
    } else {
      if (strpos((string) $xml, 'Error') === false) {
        return false;
      }
      $previous_error_level = error_reporting(0);
      $xmlDoc = simplexml_load_string($xml);
      error_reporting($previous_error_level);
    }

    if (!$xmlDoc instanceof \SimpleXMLElement) {
      return false;
    }


    // Make sure the Error we found was a Status Error
    foreach ($xmlDoc->entry as $entry) {
      if ($entry['key'] == 'Status' && $entry == 'Error') {
        return TRUE;
      }
    }

    return false;
  }

}
