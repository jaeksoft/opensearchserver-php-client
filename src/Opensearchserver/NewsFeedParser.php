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

namespace Opensearchserver;

/**
 * Misc classes to parse and simplify usage of different feed format during the indexation
 */
abstract class NewsFeedParser extends \ArrayObject
{
    protected $feedFormat;
    protected $channelTitle;
    protected $channelSubtitle;
    protected $channelHome;

    /**
     * @param SimpleXMLElement $xml
     * @return \Opensearchserver\NewsFeedParser
     */
    public static function factory(\SimpleXMLElement $xml)
    {
        // Determine the format of the xml
        // RSS
        if (isset($xml->channel->item[0])) {
            return new NewsFeedParser\RSS($xml);
        }
        // Atom
        elseif (isset($xml->entry[0])) {
            return new NewsFeedParser\Atom($xml);
        }
    }

    public function getFeedFormat()
    {
        return $this->feedFormat;
    }

    public function getChannelTitle()
    {
        return $this->channelTitle;
    }

    public function getChannelSubtitle()
    {
        return $this->channelSubtitle;
    }

    public function getChannelHome()
    {
        return $this->channelHome;
    }
}