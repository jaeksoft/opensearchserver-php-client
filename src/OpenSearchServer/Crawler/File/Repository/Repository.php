<?php
namespace OpenSearchServer\Crawler\File\Repository;

use OpenSearchServer\Crawler\File\RequestFileCrawler;

abstract class Repository extends RequestFileCrawler
{
    public function path($path) {
        $this->parameters['path'] = $path;
        return $this;
    }

    public function ignoreHiddenFile($ignoreHiddenFile = true) {
        if($ignoreHiddenFile === true) {
            $this->parameters['ignoreHiddenFile'] = 'true';
        } elseif($ignoreHiddenFile === false) {
            $this->parameters['ignoreHiddenFile'] = 'false';
        } else {
            $this->parameters['ignoreHiddenFile'] = $ignoreHiddenFile;
        }
        return $this;
    }

    public function includeSubDirectory($includeSubDirectory = true) {
        if($includeSubDirectory === true) {
            $this->parameters['includeSubDirectory'] = 'true';
        } elseif($includeSubDirectory === false) {
            $this->parameters['includeSubDirectory'] = 'false';
        } else {
            $this->parameters['includeSubDirectory'] = $includeSubDirectory;
        }
        return $this;
    }

    public function enabled($enabled = true) {
        if($enabled === true) {
            $this->parameters['enabled'] = 'true';
        } elseif($enabled === false) {
            $this->parameters['enabled'] = 'false';
        } else {
            $this->parameters['enabled'] = $enabled;
        }
        return $this;
    }

    public function delay($delay) {
        $this->parameters['delay'] = $delay;
        return $this;
    }
}