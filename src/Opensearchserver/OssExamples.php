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
 * Examples using the OpenSearchServer PHP API
 */

namespace Opensearchserver;

// Retrieve connection information
$oss_url = getenv('OSS_PHP_URL');
$oss_index = getenv('OSS_PHP_INDEX');
$oss_login = getenv('OSS_PHP_LOGIN');
$oss_key = getenv('OSS_PHP_KEY');

// Create an OSS_API instance
$oss_api = new OssApi($oss_url, $oss_index, $oss_login, $oss_key);

// Obtain an OSS_SEARCH instance
$oss_search = $oss_api->search();

// Searching the keyword "open", using the search template called "search"
$xmlResult = $oss_search->query('open')
->template('search')
->execute(60);

// Print the number of documents found
$oss_result = new OssResults($xmlResult);
$doc_found_number = $oss_result->getResultFound() - $oss_result->getResultCollapsedCount();
print 'Documents found: '.$doc_found_number."\n";

// Print the title of the documents
for ($i = 0; $i < $doc_found_number; $i++) {
  $title = $oss_result->getField($i, 'title');
  print '#'.$i.' '.$title."\n";
}

