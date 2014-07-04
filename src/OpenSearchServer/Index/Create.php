<?php
namespace OpenSearchServer\Index;

use OpenSearchServer\Request;

class Create extends Request
{
    /**
     * Set name of template to use
     * Optional
     * @param string $value
     */
    public function template($value) {
    	$this->options['template'] = $value;
    	return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return self::METHOD_POST;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
    	$this->checkPathIndexNeeded();
        $path = rawurlencode($this->options['index']);
        return (!empty($this->options['template'])) ? $path.'/template/'.rawurlencode($this->options['template']) : $path;
    }
}