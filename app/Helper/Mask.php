<?php

namespace App\Helper;

class Mask
{
    public static function remove($string, $type)
    {
        $string = trim($string);
        switch ($type){
            case 'document':
                $string = str_replace(".", "", $string);
                $string = str_replace("/", "", $string);
                $string = str_replace("-", "", $string);
            break;
        }

        return trim($string);
    }

    public static function format($string, $type)
    {
        $string = trim($string);
        switch ($type){
            case 'document':
                if (strlen($string) == 11) {
                    $string =   substr($string, 0, 3) . '.' .
                                substr($string, 3, 3) . '.' .
                                substr($string, 6, 3) . '-' .
                                substr($string, 9, 2);
                } elseif (strlen($string) == 14) {
                    $string =   substr($string, 0, 2) . '.' .
                                substr($string, 2, 3) . '.' .
                                substr($string, 5, 3) . '/' .
                                substr($string, 8, 4) . '-' .
                                substr($string, -2);
                }
            break;
        }

        return trim($string);
    }
}
