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
 * Class to access miscellaneous functions
 * @package OpenSearchServer
 */

namespace Opensearchserver;

/**
 * Store and retrieve a value from the browser (In order REQUEST, COOKIE, DEFAULT)
* @return unknown_type
*/
function config_request_value($key, $default, $request_field = null)
{
    $value = null;
    if (!empty($_REQUEST[$request_field])) {
        $value = $_REQUEST[$request_field];
        setcookie($key, $value, time() + 3600 * 365, '/');
    }
    if (!$value && isset($_COOKIE[$key])) {
        $value = $_COOKIE[$key];
    }
    if (!$value) {
        $value = $default;
    }

    return $value;
}

/**
 * Retrieve an XML feed
 * @param string $url The feed URL
 * @param array $curl_info By Reference. If given, the informations provided by curl will be returned using the provided array
 * @return SimpleXMLElement Will return false if something gone wrong
 */
function retrieve_xml($url, &$curl_info = null)
{
    $rcurl = curl_init($url);
    curl_setopt($rcurl, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($rcurl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($rcurl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($rcurl, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($rcurl, CURLOPT_TIMEOUT, 5);
    $content = curl_exec($rcurl);

    if ($curl_info !== null) {
        $curl_info = curl_getinfo($rcurl);
    }

    if ($content === false) {
        trigger_error('CURL failed to execute on URL "' . $url . '"');

        return false;
    }

    $previous_error_level = error_reporting(0);
    $xml = simplexml_load_string($content);
    error_reporting($previous_error_level);

    return (!$xml instanceof SimpleXMLElement) ? false : $xml;

}

/**
 * Wrapper for reset to use arrays returned from functions and methods
 * @param $array
 * @return mixed
 */
function array_first($array)
{
    return reset($array);
}

/**
 * Wrapper for end to use arrays returned from functions and methods
 * @param $array
 * @return mixed
 */
function array_last($array)
{
    return end($array);
}

function indentXML($string)
{
    function indentXML_pregCallback($matches)
    {
        static $indent = 0;
        static $indentExclusion = array('?');
        if (substr($matches[0], 0, 9) == "<[CDATA[!") {
            $pad = str_repeat(' ', max(0, $indent));
        } elseif ($matches[0][1] == '?') {
            $pad = str_repeat(' ', max(0, $indent));
        } elseif ($matches[0][1] == '/') {
            $indent--;
            $pad = str_repeat(' ', max(0, $indent));
        } elseif (substr($matches[0], -2, 1) != '/') {
            $indent++;
            $pad = str_repeat(' ', max(0, $indent-1));
        }

        return $pad . $matches[0] . ($indent ? "\n" : "");
    }

    return preg_replace_callback('/<[^>]+>/', "indentXML_pregCallback", $string);

}

function beautifulXML($string)
{
    function beautifulXML_tagging($string)
    {
        $string = preg_replace('/^(\w+)/i', '<span class="nodeName">$1</span>', $string);

        return $string;
    }

    function beautifulXML_pregCallback($matches)
    {
        $before = '';
        $after    = '';
        if (substr($matches[0], 0, 9) == "<![CDATA[") {
            $before    = '<div class="node"><span class="delimiter">&lt;[!CDATA[</span><span class="cdata">';
            $content = substr($matches[0], 9, -3);
            $after     = '</span><span class="delimiter">]]&gt;</span></div>';
        } elseif ($matches[0][1] == '?') {
            $before = '<div class="node"><span class="delimiter">&lt;?</span>';
            $content = beautifulXML_tagging(substr($matches[0], 2, -2));
            $after    = '<span class="delimiter">?&gt;</span></div>';
        } elseif ($matches[0][1] == '/') {
            $before = '<span class="delimiter">&lt;/</span>';
            $content = beautifulXML_tagging(substr($matches[0], 2, -1));
            $after    = '<span class="delimiter">&gt;</span></div>';
        } elseif (substr($matches[0], -2, 1) != '/') {
            $before = '<div class="node"><span class="delimiter">&lt;</span>';
            $content = beautifulXML_tagging(substr($matches[0], 1, -1));
            $after    = '<span class="delimiter">&gt;</span>';
        }

        return $before . $content . $after;
    }

    return preg_replace_callback('/<[^>]+>/', "beautifulXML_pregCallback", $string);
}
