<?php
namespace OpenSearchServer\Replication;

use OpenSearchServer\RequestJson;

class Create extends RequestJson
{
	/**
	 * Specify the type of replication
	 * @param string $type
	 * @return OpenSearchServer\Replication\Create
	 */
	public function replicationType($type) {
		$this->data['replicationType'] = $type;
		return $this;
	}

	/**
	 * Specify the remote url
	 * @param string $value
	 * @return OpenSearchServer\Replication\Create
	 */
	public function remoteUrl($value) {
		$this->data['remoteUrl'] = $value;
		return $this;
	}

	/**
	 * Specify the remote login
	 * @param string $value
	 * @return OpenSearchServer\Replication\Create
	 */
	public function remoteLogin($value) {
		$this->data['remoteLogin'] = $value;
		return $this;
	}

	/**
	 * Specify the remote API key
	 * @param string $value
	 * @return OpenSearchServer\Replication\Create
	 */
	public function remoteApiKey($value) {
		$this->data['remoteApiKey'] = $value;
		return $this;
	}

	/**
	 * Specify the remote name of index
	 * @param string $value
	 * @return OpenSearchServer\Replication\Create
	 */
	public function remoteIndexName($value) {
		$this->data['remoteIndexName'] = $value;
		return $this;
	}

	/**
	 * Specify the timeout in seconds
	 * @param string $value
	 * @return OpenSearchServer\Replication\Create
	 */
	public function secTimeOut($value) {
		$this->data['secTimeOut'] = $value;
		return $this;
	}
	
	
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
    	return rawurlencode($this->options['index']).'/replication';
    }
}