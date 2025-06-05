<?php

namespace App\Config\Database;

use App\Config\Http\Response;
use App\Helpers\Env;

class ConfigDB
{
    private static $configDBS = [
        'driver' => '',
        'port' => '',
        'host' => '',
        'database' => '',
        'username' => '',
        'password' => '',
        'charset' => ''
    ];

    private static $configDrivers = [
        'mysql',
        'pgsql'
    ];

    public static function connectDB()
    {
        self::$configDBS['driver'] = Env::getEnv('DB_CONNECTION');
        self::$configDBS['port'] = Env::getEnv('DB_PORT');
        self::$configDBS['charset'] = Env::getEnv('DB_CHARSET');

        switch (Env::getEnv('APP_ENV')) {
            case 'PROD':
                self::$configDBS['host'] = Env::getEnv('DB_PROD_HOST');
                self::$configDBS['database'] = Env::getEnv('DB_PROD_DATABASE');
                self::$configDBS['username'] = Env::getEnv('DB_PROD_USERNAME');
                self::$configDBS['password'] = Env::getEnv('DB_PROD_PASSWORD');
                break;
            case 'QA':
                self::$configDBS['host'] = Env::getEnv('DB_QA_HOST');
                self::$configDBS['database'] = Env::getEnv('DB_QA_DATABASE');
                self::$configDBS['username'] = Env::getEnv('DB_QA_USERNAME');
                self::$configDBS['password'] = Env::getEnv('DB_QA_PASSWORD');
                break;
            case 'DEV':
                self::$configDBS['host'] = Env::getEnv('DB_DEV_HOST');
                self::$configDBS['database'] = Env::getEnv('DB_DEV_DATABASE');
                self::$configDBS['username'] = Env::getEnv('DB_DEV_USERNAME');
                self::$configDBS['password'] = Env::getEnv('DB_DEV_PASSWORD');
                break;
            default:
                Response::response(500, 'DB: Entorno no configurado o soportado por el api');
                break;
        }

        if (!in_array(self::$configDBS['driver'], self::$configDrivers, true)) Response::response(500, 'DB: Driver no configurado o soportado por el api');

        return ConnectionDB::getConnection(self::$configDBS);
    }
}
