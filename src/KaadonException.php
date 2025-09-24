<?php

declare(strict_types=1);

namespace Kaadon\Exception;

use Exception;
use Throwable;

/**
 * KaadonException - Enhanced exception class with additional functionality
 * 
 * This class provides enhanced exception handling capabilities including
 * error codes, context data, and improved debugging information.
 */
class KaadonException extends Exception
{
    /**
     * Additional context data associated with the exception
     *
     * @var array<string, mixed>
     */
    private array $context;

    /**
     * Create a new KaadonException instance
     *
     * @param string $message The exception message
     * @param int $code The exception code
     * @param array<string, mixed> $context Additional context data
     * @param Throwable|null $previous Previous exception for chaining
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * Get the context data associated with this exception
     *
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Set context data for this exception
     *
     * @param array<string, mixed> $context
     * @return self
     */
    public function setContext(array $context): self
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Add a single context item
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function addContext(string $key, $value): self
    {
        $this->context[$key] = $value;
        return $this;
    }

    /**
     * Get a specific context value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getContextValue(string $key, $default = null)
    {
        return $this->context[$key] ?? $default;
    }

    /**
     * Convert the exception to an array for serialization
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'context' => $this->getContext(),
            'trace' => $this->getTrace(),
        ];
    }

    /**
     * Convert the exception to JSON
     *
     * @param int $options JSON encode options
     * @return string
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options) ?: '{}';
    }

    /**
     * Create a KaadonException with context data
     *
     * @param string $message
     * @param array<string, mixed> $context
     * @param int $code
     * @param Throwable|null $previous
     * @return static
     */
    public static function withContext(
        string $message,
        array $context = [],
        int $code = 0,
        ?Throwable $previous = null
    ): self {
        return new static($message, $code, $context, $previous);
    }
}