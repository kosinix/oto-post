<?php

namespace OtoPost\Std;

class Random {

    public static function string($length = 12, $characters='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') {
        $token = "";
        for($i=0; $i < $length; $i++){
            $token .= $characters[self::randomInt(0, strlen($characters) - 1)];
        }
        return $token;
    }

    public static function randomInt($min, $max) {
        if(function_exists('openssl_random_pseudo_bytes')){
            return self::openSslRand($min, $max);
        } else {
            return rand($min, $max);
        }
    }
    public static function openSslRand($min, $max) {
        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }

}