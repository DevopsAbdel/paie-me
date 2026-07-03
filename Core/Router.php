<?php

namespace Core;

class Router
{
    private static array $routes = [];

    public static function get(string $uri, array $action): void
    {
        self::$routes['GET'][] = ['uri' => $uri, 'action' => $action];
    }

    public static function post(string $uri, array $action): void
    {
        self::$routes['POST'][] = ['uri' => $uri, 'action' => $action];
    }

    public static function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = preg_replace('#^/paie-me#', '', $uri);
        $uri = trim($uri, '/');

        if ($uri === '' && isset($_GET['url'])) {
            $uri = trim($_GET['url'], '/');
        }

        foreach (self::$routes[$method] ?? [] as $route) {
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route['uri']);
            $pattern = '#^' . trim($pattern, '/') . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                [$controller, $methodName] = $route['action'];
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                if (!class_exists($controller)) {
                    throw new \RuntimeException("Controller $controller not found");
                }

                $instance = new $controller();
                call_user_func_array([$instance, $methodName], $params);
                return;
            }
        }

        http_response_code(404);
        require_once __DIR__ . '/../views/404.php';
    }
}
