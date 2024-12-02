<?php

namespace App\Helpers;

class VatNumber
{
    public static function isNotValid($vat)
    {
        if($vat == '') return 1;
        if(strlen($vat) != 11) return 1;
        if(!preg_match("/^[0-9]+$/", $vat)) return 1;
        $vat = str_split($vat);
        $x = 0;
        $y = 0;
        
        $i = 1;
        foreach($vat as $n){
            if($i <= 10){
                if($i % 2 == 0){
                    $y += ((2 * $n) > 9)? (2 * $n) - 9: 2 * $n;
                } else {
                    $x += $n;
                }
            }
            $i++;
        }
        
        $t = ($x + $y) % 10;
        $control = (10 - $t) % 10;
        if( $vat[10] != $control){
            return 1;
        }
        return 0;
    }
}