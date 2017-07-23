<?php

namespace queasy;

class Route implements RouteInterface
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
        $this->method = strtolower($method);
    }

    public function resolve(array $route = null)
    {
        $result = $this->resolveRecursive($route);
        if (!is_null($result)) {
            $result = array(
                $controllerClass,
                $this->method
            );
        }

        return $result;
    }

    protected function resolveRecursive(array $route = null, $path = self::BASE_CONTROLLER_PATH, $namespace = self::BASE_CONTROLLER_NAMESPACE)
    {
        if (is_null($route)) {
            $route = $this->route;
        }

        $token = array_shift($route);
        if (!empty($token)) {
            // Look for controller in subfolder
            $newPath = $path . DIRECTORY_SEPARATOR . $token;
            if (@is_dir($newPath)) {
                $result = $this->resolve($route, $newPath, sprintf(self::CONTROLLER_NAMESPACE_TEMPLATE, $namespace, $token));
                if (false !== $result) {
                    return $result;
                }
            }

            // Check if controller exists in current folder
            if (preg_match('/[_a-z]/i', $token)) {
                if (@file_exists(sprintf(self::CONTROLLER_PATH_TEMPLATE, $path, ucfirst($token)))) {
                    $this->route = $route;

                    return sprintf(self::CONTROLLER_NAME_TEMPLATE, $namespace, ucfirst($token);
                }
            }
        }

        if (@file_exists(sprintf(self::CONTROLLER_PATH_TEMPLATE, $path, self::DEFAULT_CONTROLLER_NAME))) {
            $this->route = $route;

            return sprintf(self::CONTROLLER_NAME_TEMPLATE, $namespace, self::DEFAULT_CONTROLLER_NAME);
        }

        return false;
    }

    public function get()
    {
        return $this->route;
    }

}

