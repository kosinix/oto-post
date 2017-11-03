<?php 

namespace OtoPost\Log\Transport;

class Out {
    protected $localFormatter;
    function __construct($localFormatter=null){
        if(!$localFormatter){
            $localFormatter = function($message){
                return sprintf('%s%s', $message, "<br>");
            };
        }
        $this->localFormatter = $localFormatter;
    }
    function log($message, $globalLFormatter) {
        echo call_user_func($this->localFormatter, $globalLFormatter($message));
    }
}