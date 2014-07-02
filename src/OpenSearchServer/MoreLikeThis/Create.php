<?php
namespace OpenSearchServer\MoreLikeThis;

use OpenSearchServer\MoreLikeThis\MoreLikeThis;

class Create extends MoreLikeThis
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
        if(empty($this->options['template'])) {
            throw new \Exception('Method "template($name)" must be called before submitting request.');
        }
        return rawurlencode($this->options['index']).'/morelikethis/template/'.rawurlencode($this->options['template']);
    }
}