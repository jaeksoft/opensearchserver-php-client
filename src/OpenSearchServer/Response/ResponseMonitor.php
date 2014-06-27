<?php
namespace OpenSearchServer\Response;

use Buzz\Message\Response as BuzzResponse;
class ResponseMonitor extends ResponseIterable
{
    public function __construct(BuzzResponse $response, \OpenSearchServer\Request $request)
    {
		parent::__construct($response, $request);
        //build array of values
		if(!empty($this->jsonValues->basic)) { 
		    $this->setValues((array)$this->jsonValues->basic);
		}
		if(!empty($this->jsonValues->properties)) { 
		    foreach($this->jsonValues->properties as $propObj) {
                $this->values[$propObj->name] = $propObj->value;
		    }
		}
    }
}