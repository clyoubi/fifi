<?php

interface IRequest {
    public function get_params();
}

class Router {
    public $request;
    private $supportedHttpMethods = ["GET", "POST", "DELETE", "PUT"];
    private $routes = [];
    private $middleware = [];

    public function __construct(IRequest $request) {
        $this->request = $request;
    }

    public function __call($method, $args) {
        $method = strtoupper($method);
        if (!in_array($method, $this->supportedHttpMethods)) {
            return $this->invalidMethodHandler();
        }

        $route = $this->formatRoute($args[0]);
        $callback = $args[1];
        $middleware = $args[2] ?? null;

        $this->routes[$method][] = [
            'pattern' => $route,
            'callback' => $callback
        ];

        if ($middleware) {
            $this->middleware[$route] = $middleware;
        }
    }

    private function formatRoute($route) {
        $r = explode('?', $route)[0];
        $result = rtrim($r, '/');
        return $result === '' ? '/' : $result;
    }

    private function invalidMethodHandler() {
        header("{$this->request->serverProtocol} 405 Method Not Allowed");
        echo "405 Method Not Allowed";
    }

    private function defaultRequestHandler() {
        header("{$this->request->serverProtocol} 404 Not Found");
        http_response_code(404);
        if (file_exists(__DIR__. '/resources/views/404.php')) {
            return view('404');
        }
        return Response::send(null, 404, 'Invalid route');
    }

    public function resolve() {
        $method = strtoupper($this->request->requestMethod);
        $formattedRoute = $this->formatRoute($this->request->requestUri);
        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $route) {
            $pattern = "#^" . preg_replace('#:([\w]+)#', '(?P<\1>[\w-]+)', $route['pattern']) . "$#";

            if (preg_match($pattern, $formattedRoute, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $queryParams = $this->request->get_params();
                $allParams = array_merge($params, $queryParams);

                $callback = $route['callback'];

                if (isset($this->middleware[$route['pattern']])) {
                    echo call_user_func($this->middleware[$route['pattern']], $callback, [$allParams]);
                } else {
                    echo call_user_func_array($callback, [$allParams]);
                }
                return;
            }
        }

        $this->defaultRequestHandler();
    }

    public function __destruct() {
        $this->resolve();
    }

    public function get_request() {
        return $this->request;
    }
}




class Request implements IRequest
{
    public $requestMethod;
    public $requestUri;
    public $serverProtocol;
    public $headers = [];

    function __construct()
    {
        $this->bootstrapSelf();
        $this->headers = $this->getHeaders();
    }

    private function bootstrapSelf()
    {
        foreach ($_SERVER as $key => $value) {
            $camelKey = $this->toCamelCase($key);
            if (property_exists($this, $camelKey)) {
                $this->{$camelKey} = $value;
            }
        }
    }

    private function toCamelCase($string)
    {
        $result = strtolower($string);
        preg_match_all('/_[a-z]/', $result, $matches);

        foreach ($matches[0] as $match) {
            $c = str_replace('_', '', strtoupper($match));
            $result = str_replace($match, $c, $result);
        }

        return $result;
    }

    private function getHeaders(): array
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        // Fallback for environments where getallheaders() is unavailable (e.g., CLI, non-Apache)
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', ucwords(strtolower(substr($key, 5)), '_'));
                $headers[$name] = $value;
            }
        }
        return $headers;
    }

    public function get_params(): array
    {
        $body = [];

        if ($this->requestMethod === "GET" || $this->requestMethod === "DELETE" || $this->requestMethod === "PUT") {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->requestMethod === "POST") {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }
}
