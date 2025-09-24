<?php

declare(strict_types=1);

namespace Kaadon\Exception\Tests;

use Exception;
use Kaadon\Exception\KaadonException;
use PHPUnit\Framework\TestCase;

class KaadonExceptionTest extends TestCase
{
    public function testBasicException(): void
    {
        $exception = new KaadonException('Test message', 123);
        
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(123, $exception->getCode());
        $this->assertEmpty($exception->getContext());
    }

    public function testExceptionWithContext(): void
    {
        $context = ['user_id' => 42, 'action' => 'login'];
        $exception = new KaadonException('Test message', 0, $context);
        
        $this->assertEquals($context, $exception->getContext());
        $this->assertEquals(42, $exception->getContextValue('user_id'));
        $this->assertEquals('default', $exception->getContextValue('nonexistent', 'default'));
    }

    public function testAddContext(): void
    {
        $exception = new KaadonException('Test message');
        $exception->addContext('key1', 'value1');
        $exception->addContext('key2', 'value2');
        
        $expected = ['key1' => 'value1', 'key2' => 'value2'];
        $this->assertEquals($expected, $exception->getContext());
    }

    public function testSetContext(): void
    {
        $exception = new KaadonException('Test message');
        $context = ['user' => 'john', 'role' => 'admin'];
        $exception->setContext($context);
        
        $this->assertEquals($context, $exception->getContext());
    }

    public function testToArray(): void
    {
        $context = ['debug' => true];
        $exception = new KaadonException('Test message', 500, $context);
        
        $array = $exception->toArray();
        
        $this->assertEquals('Test message', $array['message']);
        $this->assertEquals(500, $array['code']);
        $this->assertEquals($context, $array['context']);
        $this->assertArrayHasKey('file', $array);
        $this->assertArrayHasKey('line', $array);
        $this->assertArrayHasKey('trace', $array);
    }

    public function testToJson(): void
    {
        $exception = new KaadonException('Test message', 404);
        $json = $exception->toJson();
        
        $this->assertJson($json);
        $decoded = json_decode($json, true);
        $this->assertEquals('Test message', $decoded['message']);
        $this->assertEquals(404, $decoded['code']);
    }

    public function testWithContext(): void
    {
        $context = ['request_id' => 'abc123'];
        $exception = KaadonException::withContext('API Error', $context, 400);
        
        $this->assertEquals('API Error', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
        $this->assertEquals($context, $exception->getContext());
    }

    public function testChainedExceptions(): void
    {
        $previous = new Exception('Original error');
        $exception = new KaadonException('Wrapped error', 0, [], $previous);
        
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExtendsException(): void
    {
        $exception = new KaadonException();
        $this->assertInstanceOf(Exception::class, $exception);
    }
}