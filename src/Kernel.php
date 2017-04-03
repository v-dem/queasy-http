<?php

namespace queasy;

use queasy\log\Logger;

class Kernel
{

    const BASE_CONTROLLER_PATH = 'app' . DIRECTORY_SEPARATOR . 'controllers';
    const BASE_CONTROLLER_NAMESPACE = 'app\\controllers';

    const DEFAULT_CONTROLLER_NAME = 'Default';

    const CONTROLLER_NAMESPACE_TEMPLATE = '%s\\%s';
    const CONTROLLER_NAME_TEMPLATE = self::CONTROLLER_NAMESPACE_TEMPLATE . 'Controller';
    const CONTROLLER_PATH_TEMPLATE = '%s' . DIRECTORY_SEPARATOR . '%sController.php';

    private $route;
    private $method;

    public function __construct(array $route, $method)
    {
        $this->route = $route;
        $this->method = $method;
    }

    public function handle(HttpRequest $request)
    {
        try {
            $controllerClass = $this->resolveRoute($this->route);
            if (false === $controllerClass) {
                throw new Exception('Can\t resolve route.'); // TODO: Change to 404 response
            }

            $controller = new $controllerClass($request);
            $method = strtolower($this->method);

            if(method_exists($controller, $method)) {
                $output = call_user_func_array(array($controller, $method), $this->route);
            } else {
                $output = call_user_func_array($controller, $this->route);
                // throw new ApplicationException(sprintf('Method "%s" doesn\'t exists in class "%s".', $method, $controllerClass));
            }

            echo $output;
        } catch (Exception $e) {
            Logger::error($e->getMessage());
        }
    }

    private function resolveRoute(array $route, $path = self::BASE_CONTROLLER_PATH, $namespace = self::BASE_CONTROLLER_NAMESPACE)
    {
        $token = array_shift($route);
        if(!empty($token)) {
            // Look for controller in subfolder
            $newPath = $path . DIRECTORY_SEPARATOR . $token;
            if (@is_dir($newPath)) {
                $result = $this->resolveRoute($route, $newPath, sprintf(self::CONTROLLER_NAMESPACE_TEMPLATE, $namespace, $token));
                if (false !== $result) {
                    return $result;
                }
            }

            // Check if controller exists in current folder
            if (preg_match('/[_a-z]/i', $token)) {
                if (@file_exists(sprintf(self::CONTROLLER_PATH_TEMPLATE, $path, ucfirst($token)))) {
                    $this->route = $route;

                    return sprintf(self::CONTROLLER_NAME_TEMPLATE, $namespace, ucfirst($token));
                }
            }
        }

        if (@file_exists(sprintf(self::CONTROLLER_PATH_TEMPLATE, $path, self::DEFAULT_CONTROLLER_NAME))) {
            $this->route = $route;

            return sprintf(self::CONTROLLER_NAME_TEMPLATE, $namespace, self::DEFAULT_CONTROLLER_NAME);
        }

        return false;
    }

}

