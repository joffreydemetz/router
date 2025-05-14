<?php

use PHPUnit\Framework\TestCase;
use JDZ\Router\Generator\Route;
use JDZ\Router\Generator\Routes;
use JDZ\Router\Generator\RoutesImmutable;

class GeneratorTest extends TestCase
{
    public function testRouteCreation()
    {
        $route = new Route('/example/', 'Example', [
            'component' => 'page',
            'task' => 'display',
            'slug' => 'example',
        ], 'example-alias');

        $this->assertEquals('/example/', $route->getUrl());
        $this->assertEquals('Example', $route->getName());

        $this->assertEquals([
            'component' => 'page',
            'task' => 'display',
            'slug' => 'example',
        ], $route->getOptions());
    }

    public function testRoutesAddAndRetrieve()
    {
        $routes = new Routes();

        $route1 = new Route('/example/', 'Example', [
            'component' => 'page',
            'task' => 'display',
            'slug' => 'example',
        ], 'example-alias');

        $route2 = new Route('/example2/', 'Example2', [
            'component' => 'page',
            'task' => 'display',
            'slug' => 'example2',
        ], 'example2-alias');

        $routes->addRoute($route1);
        $routes->addRoute($route2);

        $allRoutes = $routes->getRoutes();

        $this->assertCount(2, $allRoutes);
        $this->assertArrayHasKey('/example/', $allRoutes);
        $this->assertArrayHasKey('/example2/', $allRoutes);
        $this->assertEquals($route1, $allRoutes['/example/']);
        $this->assertEquals($route2, $allRoutes['/example2/']);
    }

    public function testRoutesImmutable()
    {
        $routes = new Routes();

        $route1 = new Route('/example/', 'Example', [
            'component' => 'page',
            'task' => 'display',
            'slug' => 'example',
        ], 'example-alias');

        $routes->addRoute($route1);

        $immutableRoutes = new RoutesImmutable($routes->getRoutes());

        $this->assertCount(1, $immutableRoutes->getRoutes());
        $this->assertArrayHasKey('/example/', $immutableRoutes->getRoutes());

        // Ensure immutability
        $this->expectException(\Exception::class);
        $immutableRoutes->addRoute(new Route('/example2/', 'Example2', [
            'component' => 'page',
            'task' => 'display',
            'slug' => 'example2',
        ], 'example2-alias'));
    }
}