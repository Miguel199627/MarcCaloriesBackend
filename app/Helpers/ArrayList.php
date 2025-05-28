<?php

namespace App\Helpers;

class ArrayList
{
    public static function getSegment($value, $key, $array)
    {
        foreach ($array as $respuesta)
            if ($respuesta[$key] === $value) return $respuesta;

        return null;
    }
}
