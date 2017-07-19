<?php

namespace queasy;

use queasy\log\Logger;

class App
{

    private $route;

    /**
     * Constructor
     * @param array $route URL parts array splitted by "/" char
     * @param string Request HTTP method
     * @return \queasy\App
     */
    public function __construct(array $route)
    {
        $this->route = $route;
    }

    /**
     * Handles request
     * @param \queasy\HttpRequest $request Request object
     * @return string Response
     */
    public function handle(HttpRequest $request)
    {
        try {
            $routeClass = Loader::load('route');

            $route = new $routeClass($this->route, $request->method());
            $controllerClassMethod = $route->resolve();
            if (false === $controllerClassMethod) {
                throw new Exception('Can\t resolve route.'); // TODO: Change to 404 response
            }

            $controllerClass = array_shift($controllerClassMethod);
            $controllerMethod = array_shift($controllerClassMethod);

            $controller = new $controllerClass($request);
            if (method_exists($controller, $controllerMethod)) {
                $output = call_user_func_array(array($controller, $controllerMethod), $route->get());
            } else {
                throw new ApplicationException(sprintf('Method "%s" doesn\'t exists in class "%s".', $method, $controllerClass)); // TODO: Change to 404 response
            }

            if ($request->isAjax()) {
                return json_encode($output);
            } else {
                return $output;
            }
        } catch (Exception $e) { // TODO: Improve exceptions handling
            Logger::error($e->getMessage());
        }
    }

}

