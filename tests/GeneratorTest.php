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
        $this->assertEquals('example', $route->getName());

        $this->assertEquals([
            'component' => 'page',
            'task' => 'display',
            'slug' => 'example',
        ], $route->getVars());
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
        $this->assertEquals('/example/', $allRoutes[0]['path']);
        $this->assertEquals('/example2/', $allRoutes[1]['path']);
    }

    public function testRoutesImmutable()
    {
        $immutableRoutes = new RoutesImmutable([
            new Route('/example/', 'Example', [
                'component' => 'page',
                'task' => 'display',
                'slug' => 'example',
            ])
        ]);

        $this->assertCount(1, $immutableRoutes->getRoutes());

        // Ensure immutability - try to add route with same URL
        $this->expectException(\Exception::class);
        $immutableRoutes->addRoute(new Route('/example/', 'Example2', [
            'component' => 'page',
            'task' => 'display',
            'slug' => 'example2',
        ]));
    }
}
