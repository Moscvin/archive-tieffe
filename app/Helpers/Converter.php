<?php

namespace App\Helpers;
class Converter
{
    public static function toMinutes($val)
    {
        if($val) {
            $timeArray = explode(':', $val);
            return ($timeArray[0] ?? 0) * 60 + ($timeArray[1] ?? 0);
        }
        return 0;
    }
    
    public static function toHours($val)
    {
        $hours =  intdiv($val, 60);
        $minutes = $val % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}