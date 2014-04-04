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
namespace Opensearchserver\NewsFeedParser\Feed;

abstract class Entry
{
    protected $author;
    protected $content;
    protected $id;
    protected $link;
    protected $published;
    protected $summary;
    protected $title;
    protected $language;

    public function getLanguage()
    {
        return $this->language;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function getSummary()
    {
        return $this->summary;
    }

    public function getTitle()
    {
        return $this->title;
    }
}