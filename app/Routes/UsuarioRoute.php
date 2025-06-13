<?php

namespace App\Routes;

use App\Controllers\UsuarioController;

class UsuarioRoute
{
    private static $base = "usuario";

    public static function registrar($router)
    {
        $router->registrar(self::$base . "/save", 'POST', [UsuarioController::class, 'save']);
        $router->registrar(self::$base . "/login", 'POST', [UsuarioController::class, 'login']);
    }
}
