<?php
class Router {
    private array $routes = [];

    public function add(string $method, string $path, string $controller, string $action): void {
        $this->routes[] = compact('method', 'path', 'controller', 'action');
    }

    public function dispatch(): void {
        $url    = trim($_GET['url'] ?? '/', '/');
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            $pattern = '#^' . preg_replace('/{[^}]+}/', '([^/]+)', $route['path']) . '$#';
            if ($route['method'] === $method && preg_match($pattern, $url, $matches)) {
                array_shift($matches);
                require_once ROOT_PATH . '/app/controllers/' . $route['controller'] . '.php';
                $ctrl = new $route['controller']();
                call_user_func_array([$ctrl, $route['action']], $matches);
                return;
            }
        }

        http_response_code(404);
        require_once ROOT_PATH . '/app/views/shared/404.php';
    }
}
