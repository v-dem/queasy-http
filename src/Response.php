<?php

namespace queasy\http;

use Psr\Http\Message\ResponseInterface;

/**
 * Representation of an outgoing, server-side response.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - Status code and reason phrase
 * - Headers
 * - Message body
 *
 * Responses are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 */
class Response extends Message implements ResponseInterface
{
    const RESPONSE_FORMAT = 'HTTP/%s %d%s%s%s';

    private $statusCode = 200;
    private $reasonPhrase = '';

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return static
     * @throws \InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $clone = clone $this;
        $clone->statusCode = $code;
        $clone->reasonPhrase = $reasonPhrase;

        return $clone;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    public function __toString()
    {
        http_response_code($this->getStatusCode());

        foreach ($this->getHeaders() as $header => $values) {
            if (is_array($values)) {
                foreach ($values as $value) {
                    header(sprintf('%s: %s', $header, $value));
                }

                continue;
            }

            header(sprintf('%s: %s', $header, $values));
        }

        return (string) $this->getBody();
/*
        $headerLines = array();
        foreach ($this->getHeaders() as $header => $values) {
            foreach ($values as $value) {
                $headerLines[] = sprintf('%s: %s', $header, $value);
            }
        }

        $headers = implode("\r\n", $headerLines);

        $body = (string) $this->getBody();

        return sprintf(
            static::RESPONSE_FORMAT,
            $this->getProtocolVersion(),
            $this->getStatusCode(),
            $this->getReasonPhrase()
                ? ' ' . $this->getReasonPhrase()
                : '',
            empty($headers)
                ? ''
                : "\r\n" . $headers,
            empty($body)
                ? ''
                : "\r\n\r\n" . $this->getBody()
        );
*/
    }
}

