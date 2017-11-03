<?php

namespace OtoPost\Curly;

class CurlResult {
    public $error;
    public $httpCode;
    public $result;

    public function __construct($result, $httpCode = '', $error=''){
        $this->error = $error;
        $this->httpCode = $httpCode;
        $this->result = $result;
    }

    public function isOk(){
        if($this->httpCode >= 200 and  $this->httpCode <= 299 and $this->error===''){
            return true;
        }
        return false;
    }
    
    public function isError(){
        return ($this->isOk()===false);
    }

    // Display in string
    public function __toString(){
        return $this->result;
    }
}