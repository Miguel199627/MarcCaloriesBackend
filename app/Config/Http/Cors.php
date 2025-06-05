<?php

namespace App\Config\Http;

class Cors
{
    private static $cors = [
        "Access-Control-Allow-Origin" => "",
        "Access-Control-Allow-Methods" => ""
    ];

    public static function activeCors()
    {
        switch (getEnv('APP_ENV')) {
            case 'PROD':
                self::$cors["Access-Control-Allow-Origin"] = "";
                self::$cors["Access-Control-Allow-Methods"] = "";
                break;
            case 'QA':
                self::$cors["Access-Control-Allow-Origin"] = "";
                self::$cors["Access-Control-Allow-Methods"] = "";
                break;
            case 'DEV':
                self::$cors["Access-Control-Allow-Origin"] = "*";
                self::$cors["Access-Control-Allow-Methods"] = "*";
                break;
            default:
                Response::response(500, 'Entorno no configurado o soportado por el api');
                break;
        }

        self::applyCors();
    }

    private static function applyCors()
    {
        header("Access-Control-Allow-Origin: " . self::$cors["Access-Control-Allow-Origin"]);
        header("Access-Control-Allow-Methods: " . self::$cors["Access-Control-Allow-Methods"]);
    }
}
