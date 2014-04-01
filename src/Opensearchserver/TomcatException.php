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
 * Open Search Server Tomcat Exception
 * @author pmercier <pmercier@open-search-server.com>
 * @package OpenSearchServer
 * FIXME Complete this documentation
 */
class TomcatException extends \RuntimeException
{
    private $status;
    protected $message;

    public function __construct($code, $html)
    {
        // Tomcat don't return a valid XHTML document, so we use preg_match
        $matches = array();
        if (!preg_match_all('/<p>(?:(?:(?!<\/p>).)*)<\/p>/mi', $html, $matches)) {
            $message = "Tomcat returned an unknown error.";
        } else {
            $message = strip_tags(end($matches[0]));
            $message = substr($message, strpos($message, ' '));
        }

        parent::__construct($message, (int) $code);
    }
}