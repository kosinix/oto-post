<?php 

namespace OtoPost\Std;

/**
* Provides a more consistent string API
*/
class Str {
    public static function find($str, $needle, $offset=0){
        return strpos($str, $needle, $offset);
    }
}