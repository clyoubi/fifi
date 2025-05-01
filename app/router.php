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
        return view('404');
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
    // Declare properties explicitly
    public $requestMethod;
    public $requestUri;
    public $serverProtocol;
    // Add other $_SERVER keys you need as public properties

    function __construct()
    {
        $this->bootstrapSelf();
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

    public function get_params()
    {
        if ($this->requestMethod === "GET") {
            $body = [];
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }

            return $body;
        }

        if ($this->requestMethod == "POST") {
            $body = [];
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
            return $body;
        }

        if ($this->requestMethod == "DELETE") {
            $body = [];
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
            return $body;
        }

        if ($this->requestMethod == "PUT") {
            $body = [];
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
            return $body;
        }
    }
}
