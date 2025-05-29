<?php

namespace App\Controllers;

class PerfilController extends BaseController
{
    public function save()
    {
        $datos = $this->validation::validate($this->request, [
            "perfil_nombre" => "required",
        ]);

        $this->response::response(200, "Proceso Exítoso", $datos);
    }
}
