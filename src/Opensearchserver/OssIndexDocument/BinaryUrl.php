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
 * @author Emmanuel Keller
 * @package OpenSearchServer
 */
class BinaryUrl
{
    /**    @var The URL for retrieving the file to index */
    private $uri;

    /**    @var The behavior in case of error when indexing the file */
    private $faultTolerant;

    public function __construct($uri, $faultTolerant = true)
    {
        $this->uri = $uri;
        $this->faultTolerant = $faultTolerant;
    }

    public function __toString()
    {
        return '<binary url="'.$this->uri.'" faultTolerant="'.($this->faultTolerant ? 'yes' : 'no').'"/>';
    }
}