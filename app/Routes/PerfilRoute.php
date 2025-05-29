<?php

namespace App\Routes;

use App\Controllers\PerfilController;

class PerfilRoute
{
    private static $base = "perfil";

    public static function registrar($router)
    {
        $router->registrar(self::$base . "/save", 'POST', [PerfilController::class, 'save']);
    }
}
