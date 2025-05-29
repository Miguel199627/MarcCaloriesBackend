<?php

namespace App\Controllers;

use App\Config\Http\{Request, Response, Validation};

class BaseController
{
    protected Request $request;
    protected Response $response;
    protected Validation $validation;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->validation = new Validation();
    }
}
