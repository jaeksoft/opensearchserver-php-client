<?php
namespace OpenSearchServer\MoreLikeThis;

use OpenSearchServer\Search as SearchAbstract;

abstract class MoreLikeThis extends SearchAbstract
{
	/**
	 * Set name of search template to use
	 * Optional
	 * @param string $value
	 */
	public function template($value) {
		$this->options['template'] = $value;
		return $this;
	}
	
	/**
	 * Specify the doc query
	 * @param string $query
	 * @return OpenSearchServer\MoreLikeThis\MoreLikeThis
	 */
	public function docQuery($query = NULL) {
		$this->data['docQuery'] = $query;
		return $this;
	}
	
	/**
	 * Specify the query
	 * @param string $query
	 * @return OpenSearchServer\MoreLikeThis\MoreLikeThis
	 */
	public function likeText($query = NULL) {
		$this->data['likeText'] = $query;
		return $this;
	}

	/**
	 * @return OpenSearchServer\MoreLikeThis\MoreLikeThis
	 */
	public function analyzerName($analyzer) {
		$this->data['analyzer'] = $analyzer;
		return $this;
	}

	/**
	 * @return OpenSearchServer\MoreLikeThis\MoreLikeThis
	 */
	public function fields($fields) {
		$this->data['fields'] = $fields;
		return $this;
	}
	
	/**
	 * @return OpenSearchServer\MoreLikeThis\MoreLikeThis
	 */
	public function minWordLen($value) {
		$this->data['minWordLen'] = $value;
		return $this;
	}
	
	/**
	 * @return OpenSearchServer\MoreLikeThis\MoreLikeThis
	 */
	public function maxWordLen($value) {
		$this->data['maxWordLen'] = $value;
		return $this;
	}
	
	/**
	 * @return OpenSearchServer\MoreLikeThis\MoreLikeThis
	 */
	public function minDocFreq($value) {
		$this->data['minDocFreq'] = $value;
		return $this;
	}
	
	/**
	 * @return OpenSearchServer\MoreLikeThis\MoreLikeThis
	 */
	public function minTermFreq($value) {
		$this->data['minTermFreq'] = $value;
		return $this;
	}
	
	/**
	 * @return OpenSearchServer\MoreLikeThis\MoreLikeThis
	 */
	public function maxNumTokensParsed($value) {
		$this->data['maxNumTokensParsed'] = $value;
		return $this;
	}
	
	/**
	 * @return OpenSearchServer\MoreLikeThis\MoreLikeThis
	 */
	public function maxQueryTerms($value) {
		$this->data['maxQueryTerms'] = $value;
		return $this;
	}

	/**
	 * @return OpenSearchServer\MoreLikeThis\MoreLikeThis
	 */
	public function boost($value) {
		$this->data['boost'] = $value;
		return $this;
	}
	
	/**
	 * @return OpenSearchServer\MoreLikeThis\MoreLikeThis
	 */
	public function stopWords($value) {
		$this->data['stopWords'] = $value;
		return $this;
	}
}