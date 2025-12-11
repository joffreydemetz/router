<?php

use PHPUnit\Framework\TestCase;
use JDZ\Router\Generator\Route;

class GeneratorRouteTest extends TestCase
{
    public function testRouteCreation(): void
    {
        $route = new Route('/example/', 'ExampleRoute');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('/example/', $route->getUrl());
        $this->assertEquals('exampleRoute', $route->getName());
        $this->assertEquals('ExampleRoute', $route->getTitle());
    }

    public function testRouteCreationWithVars(): void
    {
        $route = new Route('/page/', 'PageRoute', [
            'component' => 'page',
            'task' => 'display',
            'slug' => 'test-page',
        ]);

        $this->assertEquals('page', $route->getVar('component'));
        $this->assertEquals('display', $route->getVar('task'));
        $this->assertEquals('test-page', $route->getVar('slug'));
    }

    public function testRouteCreationWithJson(): void
    {
        $route = new Route('/api/data/', 'ApiData', [], true);

        $this->assertTrue($route->isJson());
    }

    public function testRouteCreationAutoName(): void
    {
        $route = new Route('/my-custom-route/');

        $this->assertEquals('myCustomRoute', $route->getName());
        $this->assertEquals('my-custom-route', $route->getTitle());
    }

    public function testSetUrl(): void
    {
        $route = new Route('/original/');
        $route->setUrl('/modified/');

        $this->assertEquals('/modified/', $route->getUrl());
    }

    public function testWithJson(): void
    {
        $route = new Route('/data/');
        $this->assertFalse($route->isJson());

        $route->withJson(true);
        $this->assertTrue($route->isJson());

        $route->withJson(false);
        $this->assertFalse($route->isJson());
    }

    public function testSetAndGetVars(): void
    {
        $route = new Route('/test/');

        $route->setVar('component', 'page');
        $route->setVar('task', 'display');

        $this->assertEquals('page', $route->getVar('component'));
        $this->assertEquals('display', $route->getVar('task'));
    }

    public function testSetVars(): void
    {
        $route = new Route('/test/');

        $route->setVars([
            'component' => 'page',
            'task' => 'display',
            'slug' => 'test',
        ]);

        $vars = $route->getVars();
        $this->assertCount(3, $vars);
        $this->assertEquals('page', $vars['component']);
    }

    public function testGetVarReturnsNullForNonExistent(): void
    {
        $route = new Route('/test/');

        $this->assertNull($route->getVar('non-existent'));
    }

    public function testSetAndGetOptions(): void
    {
        $route = new Route('/test/');

        $route->setOption('ignoreLastUrl', true);
        $route->setOption('cache', false);

        $this->assertTrue($route->getOption('ignoreLastUrl'));
        $this->assertFalse($route->getOption('cache'));
    }

    public function testSetOptions(): void
    {
        $route = new Route('/test/');

        $route->setOptions([
            'ignoreLastUrl' => true,
            'cache' => false,
        ]);

        $options = $route->getOptions();
        $this->assertCount(2, $options);
        $this->assertTrue($options['ignoreLastUrl']);
    }

    public function testGetOptionReturnsNullForNonExistent(): void
    {
        $route = new Route('/test/');

        $this->assertNull($route->getOption('non-existent'));
    }

    public function testSetAndGetMethods(): void
    {
        $route = new Route('/test/');

        $route->setMethods(['GET', 'POST']);

        $methods = $route->getMethods();
        $this->assertCount(2, $methods);
        $this->assertContains('GET', $methods);
        $this->assertContains('POST', $methods);
    }

    public function testToArrayBasic(): void
    {
        $route = new Route('/test/', 'TestRoute', [
            'component' => 'page',
            'task' => 'display',
        ]);

        $array = $route->toArray();

        $this->assertArrayHasKey('path', $array);
        $this->assertEquals('/test/', $array['path']);
        $this->assertArrayHasKey('defaults', $array);
        $this->assertEquals('page', $array['defaults']['component']);
    }

    public function testToArrayWithJson(): void
    {
        $route = new Route('/api/test/', 'JsonTest', [], true);

        $array = $route->toArray();

        $this->assertEquals('json', $array['defaults']['_format']);
        $this->assertEquals('json', $array['requirements']['_format']);
    }

    public function testToArrayWithMethods(): void
    {
        $route = new Route('/test/', 'TestRoute');
        $route->setMethods(['GET', 'POST']);

        $array = $route->toArray();

        $this->assertArrayHasKey('methods', $array);
        $this->assertCount(2, $array['methods']);
    }

    public function testToArrayWithOptions(): void
    {
        $route = new Route('/test/', 'TestRoute');
        $route->setOption('ignoreLastUrl', true);

        $array = $route->toArray();

        $this->assertArrayHasKey('options', $array);
        $this->assertTrue($array['options']['ignoreLastUrl']);
    }

    public function testToArrayOmitsEmptyArrays(): void
    {
        $route = new Route('/test/', 'TestRoute');

        $array = $route->toArray();

        $this->assertArrayNotHasKey('defaults', $array);
        $this->assertArrayNotHasKey('methods', $array);
        $this->assertArrayNotHasKey('requirements', $array);
        $this->assertArrayNotHasKey('options', $array);
    }

    public function testToArrayOmitsNullValues(): void
    {
        $route = new Route('/test/', 'TestRoute', [
            'component' => 'page',
            'task' => null,
        ]);

        $array = $route->toArray();

        $this->assertArrayHasKey('defaults', $array);
        $this->assertArrayHasKey('component', $array['defaults']);
        $this->assertArrayNotHasKey('task', $array['defaults']);
    }

    public function testExport(): void
    {
        $route = new Route('/test/', 'TestRoute', [
            'component' => 'page',
        ]);

        $export = $route->export();

        $this->assertIsArray($export);
        $this->assertCount(4, $export);
        $this->assertEquals('testRoute', $export[0]); // name
        $this->assertEquals('TestRoute', $export[1]); // title
        $this->assertFalse($export[2]); // json
        $this->assertIsArray($export[3]); // array data
    }

    public function testFluentInterface(): void
    {
        $route = new Route('/test/');

        $result = $route
            ->setUrl('/modified/')
            ->withJson(true)
            ->setVar('component', 'page')
            ->setVars(['task' => 'display'])
            ->setOption('cache', false)
            ->setOptions(['ignoreLastUrl' => true])
            ->setMethods(['GET']);

        $this->assertInstanceOf(Route::class, $result);
        $this->assertEquals('/modified/', $route->getUrl());
        $this->assertTrue($route->isJson());
    }
}
