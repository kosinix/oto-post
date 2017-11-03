<?php 

namespace OtoPost\Log\Transport;

class File {

    protected $file;
    protected $localFormatter;

    function __construct($file, $localFormatter=null){
        if(!$localFormatter){
            $localFormatter = function($message){
                return sprintf('%s: %s', date('c'), $message); // Date is in ISO-8601 format
            };
        }
        $this->file = $file;
        $this->localFormatter = $localFormatter;
    }

    function log($message, $globalLFormatter){
        $fp = fopen($this->file, 'a');
        fwrite($fp, call_user_func($this->localFormatter, $globalLFormatter($message)));
        fclose($fp);
    }
}