<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Kaadon\Exception\KaadonException;
use Kaadon\Exception\ValidationException;
use Kaadon\Exception\HttpException;

echo "KaadonException Library Examples\n";
echo "==================================\n\n";

// Example 1: Basic KaadonException
echo "1. Basic KaadonException:\n";
try {
    throw new KaadonException('Something went wrong', 500);
} catch (KaadonException $e) {
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   Code: " . $e->getCode() . "\n";
    echo "   JSON: " . $e->toJson() . "\n\n";
}

// Example 2: KaadonException with context
echo "2. KaadonException with context:\n";
try {
    $context = ['user_id' => 42, 'action' => 'login', 'ip' => '192.168.1.1'];
    throw KaadonException::withContext('Login failed', $context, 401);
} catch (KaadonException $e) {
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   Context: " . json_encode($e->getContext()) . "\n";
    echo "   User ID: " . $e->getContextValue('user_id') . "\n\n";
}

// Example 3: ValidationException
echo "3. ValidationException:\n";
try {
    $errors = [
        'email' => ['Email is required', 'Email must be valid'],
        'password' => ['Password is too short', 'Password must contain numbers']
    ];
    throw ValidationException::withErrors($errors, 'Form validation failed');
} catch (ValidationException $e) {
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   Email errors: " . json_encode($e->getFieldErrors('email')) . "\n";
    echo "   Has password errors: " . ($e->hasFieldErrors('password') ? 'Yes' : 'No') . "\n";
    echo "   All errors: " . json_encode($e->getErrors()) . "\n\n";
}

// Example 4: HttpException
echo "4. HttpException:\n";
try {
    throw HttpException::notFound('User not found', ['user_id' => 123]);
} catch (HttpException $e) {
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   Status Code: " . $e->getStatusCode() . "\n";
    echo "   Context: " . json_encode($e->getContext()) . "\n\n";
}

// Example 5: HttpException with headers
echo "5. HttpException with custom headers:\n";
try {
    $exception = HttpException::unauthorized('Access token expired');
    $exception->addHeader('WWW-Authenticate', 'Bearer realm="api"');
    $exception->addHeader('X-Rate-Limit-Remaining', '0');
    throw $exception;
} catch (HttpException $e) {
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   Status Code: " . $e->getStatusCode() . "\n";
    echo "   Headers: " . json_encode($e->getHeaders()) . "\n\n";
}

// Example 6: Exception chaining
echo "6. Exception chaining:\n";
try {
    try {
        throw new Exception('Database connection failed');
    } catch (Exception $original) {
        throw new KaadonException('User operation failed', 500, ['operation' => 'create_user'], $original);
    }
} catch (KaadonException $e) {
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   Previous: " . $e->getPrevious()->getMessage() . "\n";
    echo "   Context: " . json_encode($e->getContext()) . "\n\n";
}

echo "Examples completed successfully!\n";