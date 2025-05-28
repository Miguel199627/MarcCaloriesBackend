<?php

namespace Config\Database;

use PDO;

class ConnectionDB
{
    private static ?PDO $conexion = null;

    public static function getConnection(array $dataConexion)
    {
        $DRIVER = $dataConexion['driver'];
        $HOST = $dataConexion['host'];
        $PORT = $dataConexion['port'];
        $DATABASE = $dataConexion['database'];
        $USERNAME = $dataConexion['username'];
        $PASSWORD = $dataConexion['password'];
        $CHARSET = $dataConexion['charset'];

        $wearsCharset = $DRIVER === 'mysql' ? ";charset=$CHARSET" : '';

        $dsn = sprintf(
            "%s:host=%s;port=%s;dbname=%s$wearsCharset",
            $DRIVER,
            $HOST,
            $PORT,
            $DATABASE
        );

        // Usamos el Patrón Singleton para asegurar que la conexión se cree una sola vez por petición 
        if (self::$conexion) return self::$conexion;

        $pdo = new PDO($dsn, $USERNAME, $PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        self::$conexion = $pdo;

        return self::$conexion;
    }
}
