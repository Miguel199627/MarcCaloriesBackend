<?php

namespace App\Config\Http;

class Router
{
    protected string $route;
    protected string $method;
    protected array $rutasRegistradas = [];

    public function __construct(string $route, string $method)
    {
        $this->route = trim($route, '/');
        $this->method = $method;

        $this->loadSubrutas();
    }

    protected function loadSubrutas()
    {
        $rutaDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'Routes';
        $namespaceBase = 'App\\Routes\\';

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rutaDir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $archivo) {
            if ($archivo->isFile() && $archivo->getExtension() === 'php') {
                // [Mcerquera - 20250527] Obtener ruta relativa desde la carpeta routes
                $rutaRelativa = str_replace($rutaDir . DIRECTORY_SEPARATOR, '', $archivo->getPathname());

                // [Mcerquera - 20250527] Reemplazar / por \ y eliminar .php para obtener nombre de clase completo
                $clase = $namespaceBase . str_replace(['/', '.php'], ['\\', ''], $rutaRelativa);

                if (class_exists($clase) && method_exists($clase, 'registrar')) $clase::registrar($this);
            }
        }
    }

    public function registrar(string $ruta, string $metodo, array|callable $callback)
    {
        $clave = strtoupper($metodo) . ':' . trim($ruta, '/');
        $this->rutasRegistradas[$clave] = $callback;
    }

    public function resolver()
    {
        $clave = strtoupper($this->method) . ':' . $this->route;

        if (!isset($this->rutasRegistradas[$clave])) {
            Response::response(404, 'Ruta no encontrada');
            return;
        }

        try {
            $request = new Request();
            $response = new Response();
            $validation = new Validation();
            $callback = $this->rutasRegistradas[$clave];

            if (is_array($callback) && is_string($callback[0])) {
                [$controllerClass, $method] = $callback;
                $controller = new $controllerClass($request, $response, $validation);

                $params = $this->getParamsMethod($controller, $method);
                $args = $this->prepareArguments($params, $request, $response, $validation);

                call_user_func_array([$controller, $method], $args);
            }
        } catch (\TypeError | \ArgumentCountError $e) {
            Response::response(500, "Error en el controlador: " . $e->getMessage());
        } catch (\Throwable $th) {
            Response::response(500, "Error inesperado: " . $th->getMessage());
        }
    }

    // [Mcerquera 20250528] Obtiene parámetros del método usando reflexión
    private function getParamsMethod(object $controller, string $method): array
    {
        $refMetodo = new \ReflectionMethod($controller, $method);
        return $refMetodo->getParameters();
    }

    // [Mcerquera 20250528] Prepara el array de argumentos que se pasarán al método controlador, Valida que el tipo y cantidad sean correctos (0, 1 o 2 parámetros)
    private function prepareArguments(array $params, Request $request, Response $response, Validation $validation): array
    {
        $args = [];

        if (count($params) > 3) throw new \ArgumentCountError("El método tiene más de 3 parámetros, no soportado");

        foreach ($params as $param) {
            $tipo = $param->getType()?->getName() ?? '';

            if ($tipo === Request::class) {
                $args[] = $request;
            } elseif ($tipo === Response::class) {
                $args[] = $response;
            } elseif ($tipo === Validation::class) {
                $args[] = $validation;
            } else {
                throw new \TypeError("Tipo de parámetro no soportado en el controlador: {$tipo}");
            }
        }

        return $args;
    }
}
