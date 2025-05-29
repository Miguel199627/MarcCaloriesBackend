<?php

namespace App\Controllers;

use App\Config\Http\Request;

class UsuarioController extends BaseController
{
    public function save(Request $request)
    {
        $datos = $this->validation::validate($request, [
            "usuario_perfil" => "required|numeric",
            "usuario_email" => "required|valid_email"
        ]);

        $this->response::response(200, "Proceso Exítoso", $datos);
    }
}
