<?php

declare(strict_types=1);

namespace Kaadon\Exception\Tests;

use Kaadon\Exception\HttpException;
use PHPUnit\Framework\TestCase;

class HttpExceptionTest extends TestCase
{
    public function testBasicHttpException(): void
    {
        $exception = new HttpException(404);
        
        $this->assertEquals('Not Found', $exception->getMessage());
        $this->assertEquals(404, $exception->getCode());
        $this->assertEquals(404, $exception->getStatusCode());
        $this->assertEmpty($exception->getHeaders());
    }

    public function testHttpExceptionWithCustomMessage(): void
    {
        $exception = new HttpException(500, 'Custom server error');
        
        $this->assertEquals('Custom server error', $exception->getMessage());
        $this->assertEquals(500, $exception->getStatusCode());
    }

    public function testHttpExceptionWithHeaders(): void
    {
        $headers = ['Content-Type' => 'application/json', 'X-Custom' => 'value'];
        $exception = new HttpException(400, 'Bad Request', $headers);
        
        $this->assertEquals($headers, $exception->getHeaders());
    }

    public function testAddHeader(): void
    {
        $exception = new HttpException(401);
        $exception->addHeader('WWW-Authenticate', 'Bearer');
        $exception->addHeader('X-Rate-Limit', '100');
        
        $expected = [
            'WWW-Authenticate' => 'Bearer',
            'X-Rate-Limit' => '100'
        ];
        
        $this->assertEquals($expected, $exception->getHeaders());
    }

    public function testToArray(): void
    {
        $headers = ['Content-Type' => 'application/json'];
        $context = ['request_id' => 'abc123'];
        $exception = new HttpException(403, 'Forbidden', $headers, $context);
        
        $array = $exception->toArray();
        
        $this->assertEquals('Forbidden', $array['message']);
        $this->assertEquals(403, $array['code']);
        $this->assertEquals(403, $array['status_code']);
        $this->assertEquals($headers, $array['headers']);
        $this->assertEquals($context, $array['context']);
    }

    public function testDefaultMessages(): void
    {
        $testCases = [
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

        foreach ($testCases as $statusCode => $expectedMessage) {
            $exception = new HttpException($statusCode);
            $this->assertEquals($expectedMessage, $exception->getMessage());
        }
    }

    public function testUnknownStatusCode(): void
    {
        $exception = new HttpException(999);
        $this->assertEquals('HTTP Error', $exception->getMessage());
    }

    public function testBadRequest(): void
    {
        $context = ['field' => 'invalid'];
        $exception = HttpException::badRequest('Invalid input', $context);
        
        $this->assertEquals('Invalid input', $exception->getMessage());
        $this->assertEquals(400, $exception->getStatusCode());
        $this->assertEquals($context, $exception->getContext());
    }

    public function testUnauthorized(): void
    {
        $exception = HttpException::unauthorized();
        
        $this->assertEquals('Unauthorized', $exception->getMessage());
        $this->assertEquals(401, $exception->getStatusCode());
    }

    public function testForbidden(): void
    {
        $exception = HttpException::forbidden('Access denied');
        
        $this->assertEquals('Access denied', $exception->getMessage());
        $this->assertEquals(403, $exception->getStatusCode());
    }

    public function testNotFound(): void
    {
        $exception = HttpException::notFound('Resource not found');
        
        $this->assertEquals('Resource not found', $exception->getMessage());
        $this->assertEquals(404, $exception->getStatusCode());
    }

    public function testUnprocessableEntity(): void
    {
        $exception = HttpException::unprocessableEntity('Invalid data');
        
        $this->assertEquals('Invalid data', $exception->getMessage());
        $this->assertEquals(422, $exception->getStatusCode());
    }

    public function testInternalServerError(): void
    {
        $exception = HttpException::internalServerError('Server crashed');
        
        $this->assertEquals('Server crashed', $exception->getMessage());
        $this->assertEquals(500, $exception->getStatusCode());
    }

    public function testExtendsKaadonException(): void
    {
        $exception = new HttpException();
        $this->assertInstanceOf(\Kaadon\Exception\KaadonException::class, $exception);
    }
}