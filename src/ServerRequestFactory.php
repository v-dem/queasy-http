<?php

namespace queasy\http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;

class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * Create a new server request.
     *
     * Note that server-params are taken precisely as given - no parsing/processing
     * of the given values is performed, and, in particular, no attempt is made to
     * determine the HTTP method or URI, which must be provided explicitly.
     *
     * @param string $method The HTTP method associated with the request.
     * @param UriInterface|string $uri The URI associated with the request. If
     *     the value is a string, the factory MUST create a UriInterface
     *     instance based on it.
     * @param array $serverParams Array of SAPI parameters with which to seed
     *     the generated request instance.
     *
     * return ServerRequestInterface
     */
    public function createServerRequest(string $method, $uri, array $serverParams = array()): ServerRequestInterface
    {
        $request = new ServerRequest();

        return $request
            ->withMethod($method)
            ->withUri(new Uri($uri))
            ->withServerParams($serverParams);
    }

    public function createServerRequestFromGlobals()
    {
        $request = $this->createServerRequest(
            isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET',
            $this->detectUri(),
            $_SERVER
        );

        return $request
            ->withCookieParams($_COOKIE)
            ->withQueryParams($_GET)
            ->withParsedBody($_POST)
            ->withUploadedFiles($this->normalizeFiles($_FILES));
    }

    private function detectUri()
    {
        $scheme = $this->detectScheme();

        if (isset($_SERVER['HTTP_HOST'])) {
            $authority = $_SERVER['HTTP_HOST'];
        } else {
            $authority = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost';

            $port = (int) (isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80);

            $defaultPort = ($scheme === 'https')
                ? 443
                : 80;

            if ($port !== $defaultPort) {
                $authority .= ':' . $port;
            }
        }

        $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';

        return new Uri(
            $scheme . '://' . $authority . $requestUri
        );
    }

    private function detectScheme()
    {
        if (
            isset($_SERVER['HTTPS']) &&
            $_SERVER['HTTPS'] !== '' &&
            strtolower($_SERVER['HTTPS']) !== 'off'
        ) {
            return 'https';
        }

        return 'http';
    }

    private function normalizeFiles(array $files)
    {
        $normalized = array();
        foreach ($files as $key => $value) {
            $normalized[$key] = $this->normalizeFile($value);
        }

        return $normalized;
    }

    private function normalizeFile(array $file)
    {
        if ($this->isUploadedFileSpec($file)) {
            return new UploadedFile(
                $file['tmp_name'],
                $file['size'],
                $file['error'],
                isset($file['name']) ? $file['name'] : null,
                isset($file['type']) ? $file['type'] : null
            );
        }

        $normalized = array();
        foreach ($file as $key => $value) {
            $normalized[$key] = is_array($value)
                ? $this->normalizeFile($value)
                : $value;
        }

        return $normalized;
    }

    private function isUploadedFileSpec(array $file)
    {
        return isset(
            $file['tmp_name'],
            $file['size'],
            $file['error']
        );
    }
}

