<?php

namespace App\Controllers;

use App\Models\PerfilModel;

class PerfilController extends BaseController
{
    public function save()
    {
        $datos = $this->validation::validate($this->request, [
            "perfil_codigo" => "numeric",
            "perfil_nombre" => "required",
        ]);

        $model = new PerfilModel();
        $model->save($datos);

        $this->response::response(200);
    }
}
