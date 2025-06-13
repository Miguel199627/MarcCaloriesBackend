<?php

namespace App\Libraries;

use App\Models\UsuarioModel;
use App\Helpers\Hash;

class Auth
{
    public static function authentication(string $usuarioEmail, string $usuarioPassword)
    {
        $model = new UsuarioModel();
        $query = $model->select("
            usuario_nombres,
            usuario_apellidos,
            usuario_email,
            usuario_password,
            perfil_nombre
        ")
            ->join("perfil", "perfil_codigo = usuario_perfil")
            ->where("usuario_email = ?", [$usuarioEmail])
            ->where("usuario_estado = 1");

        $user = $query->first();

        if (!$user) throw new \Throwable("El Usuario Se Encuentra Inactivo");
        if (!Hash::verifyHash($usuarioPassword, $user["usuario_password"])) throw new \Throwable("La Contraseña Del Usuario Es Incorrecta");

        $menu = Menu::getMenu($usuarioEmail);
        $jwt = JwtService::getSignedJWTForUser($usuarioEmail);

        unset($user['usuario_password']);

        return [
            "usuario" => $user,
            "menu" => $menu,
            "jwt" => $jwt
        ];
    }
}
