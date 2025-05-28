<?php

namespace App\Config\Http;

class Header
{

    public static function activeHeaders()
    {
        header('Content-Type: application/json');
    }
}
