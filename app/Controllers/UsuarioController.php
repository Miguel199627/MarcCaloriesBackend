<?php

namespace App\Controllers;

use App\Config\Http\Request;
use App\Libraries\Auth;
use App\Helpers\Hash;
use App\Models\UsuarioModel;

class UsuarioController extends BaseController
{
    public function login(Request $request)
    {
        $datos = $this->validation::validate($request, [
            "usuario_email" => "required|valid_email",
            "usuario_password" => "required|min:10|max:100"
        ]);

        try {
            $result = Auth::authentication($datos["usuario_email"], $datos["usuario_password"]);
        } catch (\Throwable $th) {
            $this->response::response(400, $th->getMessage());
        }

        $this->response::response(200, null, $result);
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
