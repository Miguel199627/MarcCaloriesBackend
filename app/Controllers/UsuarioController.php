<?php

namespace App\Controllers;

use App\Config\Http\{Request, Response};

class UsuarioController extends BaseController
{
    public function save()
    {
        print_r($this->request);
    }
}
