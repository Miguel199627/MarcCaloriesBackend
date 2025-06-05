<?php

namespace App\Helpers;

use Dotenv\Dotenv;

class Env
{
    public static function getEnv(string $varEnv)
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();
        return $_ENV[$varEnv];
    }
}
