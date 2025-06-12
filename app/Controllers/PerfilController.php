<?php

namespace App\Controllers;

use App\Models\PerfilModel;

class PerfilController extends BaseController
{
    public function list()
    {
        $model = new PerfilModel();
        $result = $model->all();

        $this->response::response(200, null, $result);
    }

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
