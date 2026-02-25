<?php

namespace App\Router;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $action)
    {
        $this->addRoute('GET', $path, $action);
    }

    public function post(string $path, callable $action)
    {
        $this->addRoute('POST', $path, $action);
    }

    public function put(string $path, callable $action)
    {
        $this->addRoute('PUT', $path, $action);
    }

    public function delete(string $path, callable $action)
    {
        $this->addRoute('DELETE', $path, $action);
    }

    private function addRoute(string $method, string $path, callable $action)
    {
        $this->routes[$method][$path] = $action;
    }

    public function dispatch(string $method, string $uri)
    {
        foreach ($this->routes[$method] ?? [] as $route => $action) {

            $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', $route);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches)) {

                array_shift($matches);
                return $action(...$matches);
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }
}
