<?php
namespace OpenSearchServer\Crawler\Web;

use OpenSearchServer\Request;

class Start extends Request
{
    /******************************
     * INHERITED METHODS OVERRIDDEN
     ******************************/
    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return self::METHOD_PUT;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        $this->checkPathIndexNeeded();
        return rawurlencode($this->options['index']) . '/crawler/web/run';
    }

    /**
     * @param bool $once
     * @return $this
     */
    public function once($once = false)
    {
        if ($once === true) {
            $this->parameters['once'] = 'true';
        } elseif ($once === false) {
            $this->parameters['once'] = 'false';
        } else {
            $this->parameters['once'] = $once;
        }
        return $this;
    }
}