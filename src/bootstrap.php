<?php

require_once(VENDOR_PATH . DIRECTORY_SEPARATOR . 'autoload.php');

chdir(QUEASY_PATH);

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

$request = new queasy\HttpRequest($_GET, $_POST, $_FILES, $_SESSION);

$appClass = queasy\Loader::load('app');
$app = new $appClass($route, $_SERVER['REQUEST_METHOD']);
$app->handle($request);

queasy\log\Logger::info(sprintf('Script execution time: %s', microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']));

