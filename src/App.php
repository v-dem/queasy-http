<?php

namespace queasy;

use queasy\log\Logger;

class App
{

    private $route;
    private $method;

    public function __construct(array $route, $method)
    {
        $this->route = $route;
        $this->method = $method;
    }

    /**
     * Handles request
     * @param \queasy\HttpRequest $request Request object
     * @throws FileNotFoundException File not found
     * @return null
     */
    public function handle(HttpRequest $request)
    {
        try {
            $this->before();

            $route = new Route($this->route, $this->method);
            $controllerClass = $route->resolve();
            if (false === $controllerClass) {
                throw new Exception('Can\t resolve route.'); // TODO: Change to 404 response
            }

            $controller = new $controllerClass($request);
            $method = strtolower($this->method);

            if (method_exists($controller, $method)) {
                $output = call_user_func_array(array($controller, $method), $route->route());
            } else {
                $output = call_user_func_array($controller, $route->route());
                // throw new ApplicationException(sprintf('Method "%s" doesn\'t exists in class "%s".', $method, $controllerClass));
            }

            if ($request->isAjax()) {
                echo json_encode($output);
            } else {
                echo $output;
            }

            $this->after();
        } catch (Exception $e) { // TODO: Improve exceptions handling
            Logger::error($e->getMessage());
        }
    }

    protected function before()
    {
    }

    protected function after()
    {
    }

}

