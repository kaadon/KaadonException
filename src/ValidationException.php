<?php

declare(strict_types=1);

namespace Kaadon\Exception;

use Throwable;

/**
 * ValidationException - Exception for validation errors
 * 
 * This exception is thrown when data validation fails and provides
 * convenient methods to work with validation errors.
 */
class ValidationException extends KaadonException
{
    /**
     * Validation errors
     *
     * @var array<string, string[]>
     */
    private array $errors;

    /**
     * Create a new ValidationException instance
     *
     * @param string $message
     * @param array<string, string[]> $errors
     * @param int $code
     * @param array<string, mixed> $context
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = 'Validation failed',
        array $errors = [],
        int $code = 422,
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $context, $previous);
        $this->errors = $errors;
    }

    /**
     * Get validation errors
     *
     * @return array<string, string[]>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get errors for a specific field
     *
     * @param string $field
     * @return string[]
     */
    public function getFieldErrors(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    /**
     * Check if a specific field has errors
     *
     * @param string $field
     * @return bool
     */
    public function hasFieldErrors(string $field): bool
    {
        return isset($this->errors[$field]) && !empty($this->errors[$field]);
    }

    /**
     * Add errors for a field
     *
     * @param string $field
     * @param string|string[] $messages
     * @return self
     */
    public function addFieldErrors(string $field, $messages): self
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $messages = is_array($messages) ? $messages : [$messages];
        $this->errors[$field] = array_merge($this->errors[$field], $messages);

        return $this;
    }

    /**
     * Convert to array including validation errors
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $array = parent::toArray();
        $array['errors'] = $this->getErrors();
        return $array;
    }

    /**
     * Create a ValidationException with errors
     *
     * @param array<string, string[]> $errors
     * @param string $message
     * @param int $code
     * @return static
     */
    public static function withErrors(
        array $errors,
        string $message = 'Validation failed',
        int $code = 422
    ): self {
        return new static($message, $errors, $code);
    }
}