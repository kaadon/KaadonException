<?php

declare(strict_types=1);

namespace Kaadon\Exception;

use Throwable;

/**
 * HttpException - Exception for HTTP-related errors
 * 
 * This exception is designed for web applications and includes
 * HTTP status codes and headers for proper HTTP error responses.
 */
class HttpException extends KaadonException
{
    /**
     * HTTP status code
     *
     * @var int
     */
    private int $statusCode;

    /**
     * HTTP headers
     *
     * @var array<string, string>
     */
    private array $headers;

    /**
     * Create a new HttpException instance
     *
     * @param int $statusCode HTTP status code
     * @param string $message Error message
     * @param array<string, string> $headers HTTP headers
     * @param array<string, mixed> $context Additional context data
     * @param Throwable|null $previous Previous exception
     */
    public function __construct(
        int $statusCode = 500,
        string $message = '',
        array $headers = [],
        array $context = [],
        ?Throwable $previous = null
    ) {
        if (empty($message)) {
            $message = $this->getDefaultMessage($statusCode);
        }

        parent::__construct($message, $statusCode, $context, $previous);
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Get HTTP status code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get HTTP headers
     *
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Add or update an HTTP header
     *
     * @param string $name Header name
     * @param string $value Header value
     * @return self
     */
    public function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Convert to array including HTTP-specific data
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $array = parent::toArray();
        $array['status_code'] = $this->getStatusCode();
        $array['headers'] = $this->getHeaders();
        return $array;
    }

    /**
     * Get default message for HTTP status code
     *
     * @param int $statusCode
     * @return string
     */
    private function getDefaultMessage(int $statusCode): string
    {
        $messages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            422 => 'Unprocessable Entity',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
        ];

        return $messages[$statusCode] ?? 'HTTP Error';
    }

    /**
     * Create a 400 Bad Request exception
     *
     * @param string $message
     * @param array<string, mixed> $context
     * @return static
     */
    public static function badRequest(string $message = '', array $context = []): self
    {
        return new static(400, $message, [], $context);
    }

    /**
     * Create a 401 Unauthorized exception
     *
     * @param string $message
     * @param array<string, mixed> $context
     * @return static
     */
    public static function unauthorized(string $message = '', array $context = []): self
    {
        return new static(401, $message, [], $context);
    }

    /**
     * Create a 403 Forbidden exception
     *
     * @param string $message
     * @param array<string, mixed> $context
     * @return static
     */
    public static function forbidden(string $message = '', array $context = []): self
    {
        return new static(403, $message, [], $context);
    }

    /**
     * Create a 404 Not Found exception
     *
     * @param string $message
     * @param array<string, mixed> $context
     * @return static
     */
    public static function notFound(string $message = '', array $context = []): self
    {
        return new static(404, $message, [], $context);
    }

    /**
     * Create a 422 Unprocessable Entity exception
     *
     * @param string $message
     * @param array<string, mixed> $context
     * @return static
     */
    public static function unprocessableEntity(string $message = '', array $context = []): self
    {
        return new static(422, $message, [], $context);
    }

    /**
     * Create a 500 Internal Server Error exception
     *
     * @param string $message
     * @param array<string, mixed> $context
     * @return static
     */
    public static function internalServerError(string $message = '', array $context = []): self
    {
        return new static(500, $message, [], $context);
    }
}