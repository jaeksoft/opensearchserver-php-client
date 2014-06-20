<?php
namespace OpenSearchServer;

use Buzz\Message\Response as BuzzResponse;

class Response
{
	protected $rawContent;
	protected $success;
	protected $info;
	protected $jsonValues;
	
    public function __construct(BuzzResponse $response)
    {
		$this->rawContent = $response->getContent();
		if(!empty($this->rawContent)) {
			$jsonValues = json_decode($this->rawContent);
			if(!empty($jsonValues->successful) && $jsonValues->successful == 'true') {
				$this->success = true;
			} else {
				$this->success = false;
			}
			if(!empty($jsonValues->info)) {
				$this->info = $jsonValues->info;
			} elseif(!$this->success) {
				$this->info = $this->rawContent;
			}
			$this->jsonValues = $jsonValues;
		}
    }    

    public function getSuccess() {
    	return $this->success;
    }
    public function getInfo() {
    	return $this->info;
    }
    public function getRawContent() {
    	return $this->rawContent;
    }
    public function getJsonValues() {
    	return $this->jsonValues;
    }
}