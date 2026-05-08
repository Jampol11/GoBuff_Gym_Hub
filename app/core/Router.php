<?php
/**
 * Router - Clean URL routing
 */
class Router
{
    private array $routes = [];
    private array $namedRoutes = [];

    public function get(string $path, string $handler, string $name = ''): void
    {
        $this->addRoute('GET', $path, $handler, $name);
    }

    public function post(string $path, string $handler, string $name = ''): void
    {
        $this->addRoute('POST', $path, $handler, $name);
    }

    public function any(string $path, string $handler, string $name = ''): void
    {
        $this->addRoute('GET|POST', $path, $handler, $name);
    }

    private function addRoute(string $method, string $path, string $handler, string $name): void
    {
        $pattern = $this->pathToRegex($path);
        $this->routes[] = [
            'method'  => $method,
            'path'    => $path,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
        if ($name) {
            $this->namedRoutes[$name] = $path;
        }
    }

    private function pathToRegex(string $path): string
    {
        // Convert {param} to named capture groups
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    public function dispatch(string $uri, string $method): void
    {
        // Strip query string
        $uri = strtok($uri, '?');
        // Remove base path prefix
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($basePath && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = '/' . ltrim($uri, '/');

        foreach ($this->routes as $route) {
            $methods = explode('|', $route['method']);
            if (!in_array($method, $methods)) continue;

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named params
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->callHandler($route['handler'], $params);
                return;
            }
        }

        // 404
        http_response_code(404);
        if (file_exists(VIEWS_PATH . '/errors/404.php')) {
            require VIEWS_PATH . '/errors/404.php';
        } else {
            echo '<h1>404 - Page Not Found</h1>';
        }
    }

    private function callHandler(string $handler, array $params): void
    {
        [$controllerName, $method] = explode('@', $handler);
        if (!class_exists($controllerName)) {
            die("Controller not found: {$controllerName}");
        }
        $controller = new $controllerName();
        if (!method_exists($controller, $method)) {
            die("Method not found: {$controllerName}@{$method}");
        }
        call_user_func_array([$controller, $method], $params);
    }

    public function route(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) return '#';
        $path = $this->namedRoutes[$name];
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', $value, $path);
        }
        return base_url($path);
    }
}
