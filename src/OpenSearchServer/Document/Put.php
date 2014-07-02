<?php
namespace OpenSearchServer\Document;

use OpenSearchServer\RequestJson;
use OpenSearchServer\Document\Document;

class Put extends RequestJson
{
	/**
	 * Add a document in list of documents to push to index
	 * @param array|string|OpenSearchServer\Document\Document $document
	 */
	public function addDocument($document) {
		if(is_array($document)) {
			$this->addDocumentArray($document);
		} elseif ($document instanceof Document) {
			$this->addDocumentObject($document);
		}
	}

	/**
	 * Add a document in list of documents to push to index
	 * @param array $document
	 */
	public function addDocumentArray($document) {
		if(is_array($document)) {
			$this->data[] = $document;
		}
		return $this;
	}
	
	/**
	 * Add a document in list of documents to push to index
	 * @param OpenSearchServer\Document\Document $document
	 */
	public function addDocumentObject($document) {
		if($document instanceof Document) {
			$this->data[] = $document->toArray();
		}
		return $this;
	}
	
	/******************************
	 *     HELPER AND ALIASES
	 ******************************/
	/**
	 * Add several documents
	 * @param array $documents Array of documents to add
	 */
	public function addDocuments($documents) {
		foreach((array)$documents as $document) {
			$this->addDocument($document);
		}
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
        return rawurlencode($this->options['index']).'/document';
	}
}