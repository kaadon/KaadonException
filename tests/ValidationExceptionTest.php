<?php

declare(strict_types=1);

namespace Kaadon\Exception\Tests;

use Kaadon\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

class ValidationExceptionTest extends TestCase
{
    public function testBasicValidationException(): void
    {
        $exception = new ValidationException();
        
        $this->assertEquals('Validation failed', $exception->getMessage());
        $this->assertEquals(422, $exception->getCode());
        $this->assertEmpty($exception->getErrors());
    }

    public function testValidationExceptionWithErrors(): void
    {
        $errors = [
            'email' => ['Email is required', 'Email must be valid'],
            'password' => ['Password is too short']
        ];
        
        $exception = new ValidationException('Custom message', $errors, 400);
        
        $this->assertEquals('Custom message', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
        $this->assertEquals($errors, $exception->getErrors());
    }

    public function testGetFieldErrors(): void
    {
        $errors = [
            'email' => ['Email is required'],
            'password' => ['Password is too short', 'Password must contain numbers']
        ];
        
        $exception = new ValidationException('Validation failed', $errors);
        
        $this->assertEquals(['Email is required'], $exception->getFieldErrors('email'));
        $this->assertEquals(['Password is too short', 'Password must contain numbers'], $exception->getFieldErrors('password'));
        $this->assertEmpty($exception->getFieldErrors('nonexistent'));
    }

    public function testHasFieldErrors(): void
    {
        $errors = [
            'email' => ['Email is required'],
            'name' => []
        ];
        
        $exception = new ValidationException('Validation failed', $errors);
        
        $this->assertTrue($exception->hasFieldErrors('email'));
        $this->assertFalse($exception->hasFieldErrors('name'));
        $this->assertFalse($exception->hasFieldErrors('nonexistent'));
    }

    public function testAddFieldErrors(): void
    {
        $exception = new ValidationException();
        
        $exception->addFieldErrors('email', 'Email is required');
        $exception->addFieldErrors('email', 'Email must be valid');
        $exception->addFieldErrors('password', ['Password is too short', 'Password needs uppercase']);
        
        $expected = [
            'email' => ['Email is required', 'Email must be valid'],
            'password' => ['Password is too short', 'Password needs uppercase']
        ];
        
        $this->assertEquals($expected, $exception->getErrors());
    }

    public function testToArray(): void
    {
        $errors = ['name' => ['Name is required']];
        $exception = new ValidationException('Validation failed', $errors);
        
        $array = $exception->toArray();
        
        $this->assertEquals('Validation failed', $array['message']);
        $this->assertEquals(422, $array['code']);
        $this->assertEquals($errors, $array['errors']);
        $this->assertArrayHasKey('file', $array);
        $this->assertArrayHasKey('line', $array);
    }

    public function testWithErrors(): void
    {
        $errors = ['username' => ['Username already exists']];
        $exception = ValidationException::withErrors($errors, 'Registration failed', 409);
        
        $this->assertEquals('Registration failed', $exception->getMessage());
        $this->assertEquals(409, $exception->getCode());
        $this->assertEquals($errors, $exception->getErrors());
    }

    public function testExtendsKaadonException(): void
    {
        $exception = new ValidationException();
        $this->assertInstanceOf(\Kaadon\Exception\KaadonException::class, $exception);
    }
}