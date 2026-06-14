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

        $uri = is_string($uri)
            ? new Uri($uri)
            : $uri;

        return $request
            ->withMethod($method)
            ->withUri($uri)
            ->withServerParams($serverParams);
    }

    public function createServerRequestFromGlobals()
    {
        $request = $this->createServerRequest(
            isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET',
            $this->detectUri(),
            $_SERVER
        );

        $post = $_POST;
/*
        // TODO: Implement custom Uploaded Files parsing because PHP doesn't support multipart PUT requests
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
            if (stripos($contentType, 'multipart/form-data') !== false) {
                $post = $this->parseMultipart();
            } else {
                parse_str(file_get_contents("php://input"), $post);
            }
        }
*/

        return $request
            ->withCookieParams($_COOKIE)
            ->withQueryParams($_GET)
            ->withParsedBody($post)
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
            $streamFactory = new StreamFactory();
            $stream = empty($file['tmp_name'])
                ? $streamFactory->createStream()
                : $streamFactory->createStreamFromFile($file['tmp_name']);

            return new UploadedFile(
                $stream,
                $file['size'],
                $file['error'],
                isset($file['name']) ? $file['name'] : null,
                isset($file['type']) ? $file['type'] : null,
                $file['tmp_name']
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

    private function parseMultipart()
    {
        $rawData = file_get_contents('php://input');

        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
        $boundary = $matches[1];

        $blocks = preg_split("/-+$boundary/", $rawData);
        array_pop($blocks);

        $post = [];
        foreach ($blocks as $block) {
            if (empty($block)) {
                continue;
            }

            list($head, $body) = explode("\r\n\r\n", $block, 2);

            if (preg_match('/name="([^"]+)"/', $head, $nameMatches)) {
                $name = $nameMatches[1];
                $value = substr($body, 0, -2);
                $post[$name] = $value;
            }
        }

        return $post;
    }
}

