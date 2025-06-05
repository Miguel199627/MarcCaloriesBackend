<?php

namespace App\Config\Database;

use App\Config\Http\Response;

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
        self::$configDBS['driver'] = getEnv('DB_CONNECTION');
        self::$configDBS['port'] = getEnv('DB_PORT');
        self::$configDBS['charset'] = getEnv('DB_CHARSET');

        switch (getEnv('APP_ENV')) {
            case 'PROD':
                self::$configDBS['host'] = getEnv('DB_PROD_HOST');
                self::$configDBS['database'] = getEnv('DB_PROD_DATABASE');
                self::$configDBS['username'] = getEnv('DB_PROD_USERNAME');
                self::$configDBS['password'] = getEnv('DB_PROD_PASSWORD');
                break;
            case 'QA':
                self::$configDBS['host'] = getEnv('DB_QA_HOST');
                self::$configDBS['database'] = getEnv('DB_QA_DATABASE');
                self::$configDBS['username'] = getEnv('DB_QA_USERNAME');
                self::$configDBS['password'] = getEnv('DB_QA_PASSWORD');
                break;
            case 'DEV':
                self::$configDBS['host'] = getEnv('DB_DEV_HOST');
                self::$configDBS['database'] = getEnv('DB_DEV_DATABASE');
                self::$configDBS['username'] = getEnv('DB_DEV_USERNAME');
                self::$configDBS['password'] = getEnv('DB_DEV_PASSWORD');
                break;
            default:
                Response::response(500, 'Entorno no configurado o soportado por el api');
                break;
        }

        if (!in_array(self::$configDBS['driver'], self::$configDrivers, true)) Response::response(500, 'Driver no configurado o soportado por el api');

        return ConnectionDB::getConnection(self::$configDBS);
    }
}
