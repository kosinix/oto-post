<?php 

namespace OtoPost\Std;

/**
* Provides a more consistent array API than built-in array functions
*/
class Collection {

    /**
    * Randomize the array values. Index are not preserved.
    */
    public static function shuffle($array, $preserveKey=false){
        if(!$preserveKey){
            shuffle($array);
            return $array;
             
        } else {
            $keys = array_keys($array); 
            shuffle($keys); 
            $random = array(); 
            foreach ($keys as $key) { 
                $random[$key] = $array[$key]; 
            }
            return $random; 
        }
    }

    /**
    * Reverse array order
    */
    public static function reverse($array, $preserveKey=false){
        return array_reverse($array, $preserveKey);
    }

    /**
    * Get first element and remove it from array
    */
    public static function shift(&$array){
        return array_shift($array);
    }

    /**
    * Get last element and remove it from array
    */
    public static function pop(&$array){
        return array_pop($array);
    }

    /**
    * Alias for shift.
    */
    public static function first(&$array){
         return self::shift($array);
    }

    /**
    * Alias for pop.
    */
    public static function last(&$array){
         return self::pop($array);
    }

    /**
    * Add element at the beginning of array
    * @return array New array with added element
    */
    public static function unshift($array, $new){
         $length = array_unshift($array, $new);
         return $array;
    }

    /**
    * Add element at the end of array
    * @return array New array with added element
    */
    public static function push($array, $new){
        $length = array_push($array, $new);
        return $array;
    }

    /**
    * Alias for unshift.
    */
    public static function prepend($array, $new){
         return self::unshift($array, $new);
    }

    /**
    * Alias for push.
    */
    public static function append($array, $new){
         return self::push($array, $new);
    }

    public static function merge($array1, $array2){
        return array_merge($array1, $array2);
    }


    public static function filter($array, $fn){
        return array_filter($array, $fn);
    }

    
}