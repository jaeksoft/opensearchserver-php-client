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

namespace Opensearchserver\NewsFeedParser;

use Opensearchserver\NewsFeedParser;

class RSS extends NewsFeedParser
{
    /**
     * @param SimpleXMLElement $xml
     * @return \Opensearchserver\NewsFeedParser
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->feedFormat = 'RSS';

        // Misc informations
        $this->channelTitle    = (string) $xml->channel->title;
        $this->channelSubtitle = (string) $xml->channel->description;
        $this->channelHome     = (string) $xml->channel->link;

        // Entries
        $items = (array) $xml->xpath('channel/item');
        foreach ($items as $item) {
            $this->append(new RSS\Entry($item));
        }
    }

}