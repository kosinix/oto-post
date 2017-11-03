<?php 

namespace OtoPost\Log;

/**
* A simple logger that does one thing well. Logging.
*/
class Logger {
    
    protected $transports = array();
    protected $globalFormatter;

    public function __construct($transports, $globalFormatter=null){
        if(!$globalFormatter){
            $globalFormatter = function($message){
                return sprintf('%s%s', $message, "\n");
            };
        }

        $this->transports = $transports;
        $this->globalFormatter = $globalFormatter;
        
    }
    public function log($message){
        foreach($this->transports as $transport){
            $transport->log($message, $this->globalFormatter);
        }
    }
}