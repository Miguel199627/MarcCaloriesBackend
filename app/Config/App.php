<?php

namespace App\Config;

use App\Config\Http\{Header, Cors, Response, Router};

class App
{
    private static $baseApi = "api/";

    public static function initConfig()
    {
        ErrorLog::activateErrorLog();
        Header::activeHeaders();
        Cors::activeCors();

        $route = $_GET['route'] ?? '';
        $method = $_SERVER['REQUEST_METHOD'];

        // [Mcerquera - 20250527] Solo se manejan rutas con baseApi
        if (strpos($route, self::$baseApi) !== 0) Response::response(200, 'Bienvenido a la app');

        $route = str_replace(self::$baseApi, '', $route);

        // [Mcerquera - 20250527] Instanciar el router
        $router = new Router($route, $method);

        // [Mcerquera - 20250527] Resolver la ruta (llama al controlador si existe)
        $router->resolver();
    }
}
