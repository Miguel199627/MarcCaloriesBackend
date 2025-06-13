<?php

namespace App\Controllers;

use App\Config\Http\Request;
use App\Helpers\Hash;
use App\Libraries\Menu;
use App\Models\UsuarioModel;

class UsuarioController extends BaseController
{
    public function login(Request $request)
    {
        $datos = $this->validation::validate($request, [
            "usuario_email" => "required|valid_email",
            "usuario_password" => "required|min:10|max:100"
        ]);

        $model = new UsuarioModel();
        $query = $model->where("usuario_email = ?", [$datos["usuario_email"]])
            ->where("usuario_estado = 1");

        $user = $query->first();

        if (!$user) $this->response::response(400, "El Usuario Se Encuentra Inactivo");
        if (!Hash::verifyHash($datos["usuario_password"], $user["usuario_password"])) $this->response::response(400, "La Contraseña Del Usuario Es Incorrecta");

        $menu = Menu::getMenu($datos["usuario_email"]);

        $this->response::response(200, null, [
            "usuario" => $user,
            "menu" => $menu
        ]);
    }

    public function save(Request $request)
    {
        $datos = $this->validation::validate($request, [
            "usuario_perfil" => "required|numeric",
            "usuario_nombres" => "required",
            "usuario_apellidos" => "required",
            "usuario_email" => "required|valid_email",
            "usuario_password" => "min:10|max:100"
        ]);

        if ($datos['usuario_password']) $datos['usuario_password'] = Hash::generateHash($datos['usuario_password']);

        $model = new UsuarioModel();
        $model->save($datos);

        $this->response::response(200, null, $datos);
    }
}
