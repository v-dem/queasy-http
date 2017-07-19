<?php

require_once(sprintf('%s%s%s', VENDOR_PATH, DIRECTORY_SEPARATOR, 'autoload.php'));

chdir(QUEASY_PATH);

// Get app route from URL, also get Queasy base URL
$routeStr = preg_replace(
    '/\?.*$/',
    '',
    str_replace(
        $queasyUrl = str_replace(
            basename(INDEX_PATH),
            '',
            $_SERVER['SCRIPT_NAME']
        ),
        '',
        $_SERVER['REQUEST_URI']
    )
);

define('QUEASY_URL', $queasyUrl);

$route = explode('/', $routeStr);

session_start();

queasy\log\Logger::info(sprintf('Request: %s %s', $_SERVER['REQUEST_METHOD'], empty($routeStr)? '/': $routeStr));

// Create request object
$request = new queasy\HttpRequest($_GET, $_POST, $_FILES, $_SESSION, $_SERVER['REQUEST_METHOD']);

// Create and run App
$appClass = queasy\Loader::load('app');
$app = new $appClass($route);
echo $app->handle($request);

queasy\log\Logger::info(sprintf('Execution time: %s', microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']));

