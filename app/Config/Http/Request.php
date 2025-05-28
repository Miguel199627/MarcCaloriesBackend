<?php

namespace App\Config\Http;

class Request
{
    public string $method;
    public array $query;
    public array $body;
    public array $files;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->query  = $_GET;
        $this->files  = $_FILES;

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (in_array($this->method, ['POST', 'PUT', 'PATCH', 'DELETE']) && str_contains($contentType, 'application/json')) {
            $this->body = json_decode(file_get_contents('php://input'), true) ?? [];
        } elseif ($this->method === 'POST') {
            $this->body = $_POST;
        } else {
            $this->body = [];
        }
    }
}
