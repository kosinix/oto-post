<?php 

namespace OtoPost\Std;

/**
* Gregorian calendar date time class
*
* All time is in GMT 0. Be sure to add the offset on display.
*/
class DateTime {

    protected $unixTime;

    function __construct($unixTime){
        $this->unixTime = $unixTime;
    }

    public static function create($year, $month, $day, $hour=0, $minute=0, $second=0){
        $unixTime = mktime($hour, $minute, $second, $month, $day, $year);
        return new self($unixTime);
    }

    public static function createNow(){
        $unixTime = mktime();
        return new self($unixTime);
    }

    public static function createFromSeconds($seconds){
        return new self($seconds);
    }

    public static function createFromString($dateTime){
        $unixTime = strtotime($dateTime);
        return new self($unixTime);
    }

    public function getUnixTime(){
        return $this->unixTime;
    }

    public function getStringTime(){
        return gmdate('Y-m-d H:i:s', $this->unixTime);
    }

    public function addHour($hours){
        $this->unixTime += ($hours * 3600);
    }
}