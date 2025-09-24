# KaadonException

A PHP exception library providing enhanced exception handling capabilities with context data, validation errors, and HTTP-specific exceptions.

## Features

- **Enhanced Exception Handling**: Context data support for better debugging
- **Validation Exceptions**: Built-in support for validation error handling
- **HTTP Exceptions**: Pre-configured exceptions for common HTTP status codes
- **JSON Serialization**: Easy conversion to arrays and JSON for API responses
- **PSR-4 Autoloading**: Modern PHP package structure
- **Full Test Coverage**: Comprehensive PHPUnit test suite

## Installation

Install via Composer:

```bash
composer require kaadon/exception
```

## Usage

### Basic KaadonException

```php
use Kaadon\Exception\KaadonException;

// Basic usage
throw new KaadonException('Something went wrong', 500);

// With context data
$context = ['user_id' => 42, 'action' => 'login'];
throw new KaadonException('Login failed', 401, $context);

// Using static factory method
throw KaadonException::withContext('API Error', ['endpoint' => '/users'], 400);
```

### Context Management

```php
$exception = new KaadonException('Error occurred');

// Add context data
$exception->addContext('user_id', 123);
$exception->addContext('timestamp', time());

// Set entire context
$exception->setContext(['debug' => true, 'version' => '1.0']);

// Get context data
$context = $exception->getContext();
$userId = $exception->getContextValue('user_id', null);
```

### Validation Exception

```php
use Kaadon\Exception\ValidationException;

// Create with validation errors
$errors = [
    'email' => ['Email is required', 'Email must be valid'],
    'password' => ['Password is too short']
];

throw new ValidationException('Validation failed', $errors);

// Using static factory method
throw ValidationException::withErrors($errors, 'Form validation failed');

// Add field errors dynamically
$exception = new ValidationException();
$exception->addFieldErrors('name', 'Name is required');
$exception->addFieldErrors('age', ['Age must be numeric', 'Age must be positive']);

// Check for field errors
if ($exception->hasFieldErrors('email')) {
    $emailErrors = $exception->getFieldErrors('email');
}
```

### HTTP Exception

```php
use Kaadon\Exception\HttpException;

// Basic HTTP exception
throw new HttpException(404, 'Resource not found');

// With headers and context
$headers = ['Content-Type' => 'application/json'];
$context = ['resource_id' => 123];
throw new HttpException(403, 'Access denied', $headers, $context);

// Using static factory methods
throw HttpException::badRequest('Invalid input data');
throw HttpException::unauthorized('Authentication required');
throw HttpException::forbidden('Insufficient permissions');
throw HttpException::notFound('User not found');
throw HttpException::unprocessableEntity('Invalid JSON');
throw HttpException::internalServerError('Database connection failed');

// Add headers dynamically
$exception = HttpException::unauthorized();
$exception->addHeader('WWW-Authenticate', 'Bearer realm="api"');
```

### JSON Serialization

All exceptions can be easily converted to arrays or JSON for API responses:

```php
$exception = new KaadonException('Error occurred', 500, ['debug' => true]);

// Convert to array
$array = $exception->toArray();
/*
[
    'message' => 'Error occurred',
    'code' => 500,
    'file' => '/path/to/file.php',
    'line' => 42,
    'context' => ['debug' => true],
    'trace' => [...]
]
*/

// Convert to JSON
$json = $exception->toJson(JSON_PRETTY_PRINT);
```

### Exception Chaining

```php
try {
    // Some operation that might fail
    connectToDatabase();
} catch (PDOException $e) {
    // Wrap the original exception
    throw new KaadonException('Database operation failed', 500, ['table' => 'users'], $e);
}
```

## API Reference

### KaadonException

- `__construct(string $message = '', int $code = 0, array $context = [], ?Throwable $previous = null)`
- `getContext(): array`
- `setContext(array $context): self`
- `addContext(string $key, $value): self`
- `getContextValue(string $key, $default = null)`
- `toArray(): array`
- `toJson(int $options = 0): string`
- `static withContext(string $message, array $context = [], int $code = 0, ?Throwable $previous = null): self`

### ValidationException

Extends `KaadonException` with additional methods:

- `getErrors(): array`
- `getFieldErrors(string $field): array`
- `hasFieldErrors(string $field): bool`
- `addFieldErrors(string $field, $messages): self`
- `static withErrors(array $errors, string $message = 'Validation failed', int $code = 422): self`

### HttpException

Extends `KaadonException` with HTTP-specific methods:

- `getStatusCode(): int`
- `getHeaders(): array`
- `addHeader(string $name, string $value): self`
- `static badRequest(string $message = '', array $context = []): self`
- `static unauthorized(string $message = '', array $context = []): self`
- `static forbidden(string $message = '', array $context = []): self`
- `static notFound(string $message = '', array $context = []): self`
- `static unprocessableEntity(string $message = '', array $context = []): self`
- `static internalServerError(string $message = '', array $context = []): self`

## Requirements

- PHP 7.4 or higher

## Testing

Run the test suite:

```bash
composer test
```

Or with PHPUnit directly:

```bash
./vendor/bin/phpunit
```

## License

This project is licensed under the MIT License. See the LICENSE file for details.
