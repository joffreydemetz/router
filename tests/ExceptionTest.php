<?php

use PHPUnit\Framework\TestCase;
use JDZ\Router\RouterException;
use JDZ\Router\NoRoutesException;

class ExceptionTest extends TestCase
{
    public function testRouterExceptionIsRuntimeException(): void
    {
        $exception = new RouterException('Test message');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
    }

    public function testRouterExceptionWithCode(): void
    {
        $exception = new RouterException('Test message', 404);

        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(404, $exception->getCode());
    }

    public function testRouterExceptionWithPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new RouterException('Test message', 0, $previous);

        $this->assertEquals($previous, $exception->getPrevious());
    }

    public function testSetAndGetRequestUri(): void
    {
        $exception = new RouterException('Test message');
        $exception->setRequestUri('/test-path/?query=value');

        $this->assertEquals('/test-path/?query=value', $exception->getRequestUri());
    }

    public function testSetAndGetRequestPath(): void
    {
        $exception = new RouterException('Test message');
        $exception->setRequestPath('/test-path/');

        $this->assertEquals('/test-path/', $exception->getRequestPath());
    }

    public function testSetRequestUriReturnsInstance(): void
    {
        $exception = new RouterException('Test message');
        $result = $exception->setRequestUri('/test/');

        $this->assertInstanceOf(RouterException::class, $result);
        $this->assertSame($exception, $result);
    }

    public function testSetRequestPathReturnsInstance(): void
    {
        $exception = new RouterException('Test message');
        $result = $exception->setRequestPath('/test/');

        $this->assertInstanceOf(RouterException::class, $result);
        $this->assertSame($exception, $result);
    }

    public function testFluentInterface(): void
    {
        $exception = new RouterException('Test message');

        $result = $exception
            ->setRequestUri('/test-uri/?param=value')
            ->setRequestPath('/test-path/');

        $this->assertInstanceOf(RouterException::class, $result);
        $this->assertEquals('/test-uri/?param=value', $exception->getRequestUri());
        $this->assertEquals('/test-path/', $exception->getRequestPath());
    }

    public function testNoRoutesExceptionExtendsRouterException(): void
    {
        $exception = new NoRoutesException('No routes loaded');

        $this->assertInstanceOf(RouterException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertEquals('No routes loaded', $exception->getMessage());
    }

    public function testNoRoutesExceptionWithRequestInfo(): void
    {
        $exception = new NoRoutesException('No routes available');
        $exception->setRequestUri('/test-uri/');
        $exception->setRequestPath('/test-path/');

        $this->assertEquals('/test-uri/', $exception->getRequestUri());
        $this->assertEquals('/test-path/', $exception->getRequestPath());
    }

    public function testEmptyRequestUriAndPath(): void
    {
        $exception = new RouterException('Test');

        $this->assertEquals('', $exception->getRequestUri());
        $this->assertEquals('', $exception->getRequestPath());
    }
}
