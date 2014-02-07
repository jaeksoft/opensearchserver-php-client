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

/**
 * @file
 * Class to access OpenSearchServer API
 */

require_once(dirname(__FILE__).'/oss_abstract.class.php');

class OssAutocompletion extends OssAbstract {

  public function __construct($enginePath, $index = NULL, $login = NULL, $apiKey = NULL) {
    $this->init($enginePath, $index, $login, $apiKey);
  }

  /**
   *
   * @param string $query the characters to pass to the autocompletion query
   * @param int $rows The number of row to return
   */
  public function autocompletionQuery($query, $rows = 10) {
    $params = array('query' => $query, 'rows' => $rows);
    $return = $this->queryServerTXT(OssApi::API_AUTOCOMPLETION, $params);
    if ($return === FALSE) {
      return FALSE;
    }
    return $return;
  }

  /*
   * @deprecated  Use autocompleteQuery
  */
  public function autocomplete($query, $rows = 10) {
    return $this->autocompletionQuery($query, $rows);
  }

  /**
   * Build the autocompletion index
   * @param int $bufferSize the size of the buffer
   */
  public function autocompletionBuild($bufferSize = 1000) {
    $params = array('cmd' => 'build', 'bufferSize' => $bufferSize);
    $return = $this->queryServerXML(OssApi::API_AUTOCOMPLETION, $params);
    if ($return === FALSE) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Set the field used by the autocompletion index
   * @param string $field the field name
   */
  public function autocompletionSet($field) {
    $params = array('cmd' => 'set', 'field' => $field);
    $return = $this->queryServerXML(OssApi::API_AUTOCOMPLETION, $params);
    if ($return === FALSE) {
      return FALSE;
    }
    return TRUE;
  }
  
  /**
   * Create new autocompletion instance
   * @param string $autocompletion_name the name of the autocompletion instance
   * @param string $field_name the name of the autocompletion instance
   * @param int $rows the name of the autocompletion instance
   */
  public function createAutocompletion($autocompletion_name, $field_name = 'autocomplete', $rows = 10) {
  	$path_parameters = array(
  			"{index_name}" => $this->index,
  			"{autocompletion_name}" => $autocompletion_name,
  			"{rows}" => $rows,
  			"{field_name}" => $field_name
  	);
  	$path = strtr(OssApi::REST_API_AUTOCOMPLETION, $path_parameters);
  	$return = $this->queryServerREST($path,NULL,NULL, OssApi::DEFAULT_CONNEXION_TIMEOUT, OssApi::DEFAULT_QUERY_TIMEOUT,'PUT');
  	if ($return === FALSE) {
  		return FALSE;
  	}
  	return TRUE;
  }
  
  /**
   * Build the autocompletion index with REST API
   * @param string $autocompletion_name the name of the autocompletion instance
   */
  public function autocompletionBuildREST($autocompletion_name) {
  	$path_parameters = array(
  			"{index_name}" => $this->index,
  			"{autocompletion_name}" => $autocompletion_name
  	);
  	$path = strtr(OssApi::REST_API_AUTOCOMPLETION_BUILD, $path_parameters);
  	$return = $this->queryServerREST($path,NULL,NULL, OssApi::DEFAULT_CONNEXION_TIMEOUT, OssApi::DEFAULT_QUERY_TIMEOUT,'PUT');
  	if ($return === FALSE) {
  		return FALSE;
  	}
  	return TRUE;
  }
}
?>