<?php
/*
 *  This file is part of OpenSearchServer PHP Client.
*
*  Copyright (C) 2008-2014 Emmanuel Keller / Jaeksoft
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

class OssMonitor extends OssAbstract {

  public function __construct($enginePath, $login = NULL, $apiKey = NULL) {
    $this->init($enginePath, NULL, $login, $apiKey);
  }

  public function get_oss_version() {
  	$return = $this->queryServerXML(OssApi::API_MONITOR, NULL);
  	$version_xpath  = $return->xpath("system/version");
  	$version_string = $version_xpath[0]['value']; 
  	preg_match_all('/OpenSearchServer v(.*?)-/',$version_string,$matches); 
  	if($matches[1][0] != NULL) {
  		return $matches[1][0];
  	}
  	return NULL;
  }
}
?>