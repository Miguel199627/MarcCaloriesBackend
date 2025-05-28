<?php

namespace App\Controllers;

use App\Config\Http\{Request, Response};

class BaseController
{
    protected Request $request;
    protected Response $response;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
    }
}
