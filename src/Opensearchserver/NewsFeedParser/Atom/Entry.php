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

namespace Opensearchserver\NewsFeedParser\Atom;

class Entry extends \Opensearchserver\NewsFeedParser\Feed\Entry
{
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->id = md5((string) $xml->id);
        $this->link = $xml->link['href'];
        $this->published = date('Y-m-d\TH:i:sO', strtotime((string) $xml->published));
        $this->summary = (string) $xml->content;
        $this->title = $xml->title;

        // Only RSS2.0
        $this->author    = (string) $xml->author->name;
        $this->content = (string) $xml->content;
    }
}