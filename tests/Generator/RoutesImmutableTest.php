<?php

use PHPUnit\Framework\TestCase;
use JDZ\Router\Generator\Route;
use JDZ\Router\Generator\Routes;
use JDZ\Router\Generator\RoutesImmutable;

class GeneratorRoutesImmutableTest extends TestCase
{
    public function testImmutableRoutesCreation(): void
    {
        $immutableRoutes = new RoutesImmutable();

        $this->assertInstanceOf(RoutesImmutable::class, $immutableRoutes);
        $this->assertInstanceOf(Routes::class, $immutableRoutes);
    }

    public function testImmutableRoutesCreationWithInitialRoutes(): void
    {
        $immutableRoutes = new RoutesImmutable([
            new Route('/example/', 'Example'),
            new Route('/test/', 'Test'),
        ]);

        $this->assertCount(2, $immutableRoutes->getRoutes());
    }

    public function testCreateRouteThrowsExceptionIfRouteExists(): void
    {
        $immutableRoutes = new RoutesImmutable([
            new Route('/test/', 'Test'),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Routes are immutable');
        $immutableRoutes->createRoute('/test/', 'TestModified');
    }

    public function testCreateRouteWorksForNewRoute(): void
    {
        $immutableRoutes = new RoutesImmutable();

        $route = $immutableRoutes->createRoute('/new/', 'New', ['component' => 'page']);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('/new/', $route->getUrl());
    }

    public function testAddRouteThrowsExceptionIfRouteExists(): void
    {
        $immutableRoutes = new RoutesImmutable([
            new Route('/test/', 'Test'),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Routes are immutable');
        $immutableRoutes->addRoute(new Route('/test/', 'TestModified'));
    }

    public function testAddRouteWorksForNewRoute(): void
    {
        $immutableRoutes = new RoutesImmutable();

        $route = new Route('/new/', 'New');
        $addedRoute = $immutableRoutes->addRoute($route);

        $this->assertInstanceOf(Route::class, $addedRoute);
        $this->assertEquals('/new/', $addedRoute->getUrl());
    }

    public function testAddRoutesThrowsExceptionWithReset(): void
    {
        $immutableRoutes = new RoutesImmutable([
            new Route('/initial/', 'Initial'),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Routes are immutable');
        $immutableRoutes->addRoutes([
            new Route('/new/', 'New'),
        ], false, true);
    }

    public function testAddRoutesThrowsExceptionWithReplace(): void
    {
        $immutableRoutes = new RoutesImmutable([
            new Route('/initial/', 'Initial'),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Routes are immutable');
        $immutableRoutes->addRoutes([
            new Route('/new/', 'New'),
        ], true);
    }

    public function testAddRoutesThrowsExceptionIfRouteExists(): void
    {
        $immutableRoutes = new RoutesImmutable([
            new Route('/test/', 'Test'),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Routes are immutable (cannot modify)');
        $immutableRoutes->addRoutes([
            new Route('/test/', 'TestModified'),
        ]);
    }

    public function testAddRoutesThrowsExceptionIfMultipleRoutesExist(): void
    {
        $immutableRoutes = new RoutesImmutable([
            new Route('/test1/', 'Test1'),
            new Route('/test2/', 'Test2'),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Routes are immutable (cannot modify)');
        $immutableRoutes->addRoutes([
            new Route('/test1/', 'Test1Modified'),
            new Route('/test2/', 'Test2Modified'),
        ]);
    }

    public function testAddRoutesWorksForNewRoutes(): void
    {
        $immutableRoutes = new RoutesImmutable();

        $immutableRoutes->addRoutes([
            new Route('/example/', 'Example'),
            new Route('/test/', 'Test'),
        ]);

        $this->assertCount(2, $immutableRoutes->getRoutes());
    }

    public function testAddRoutesWithArraysThrowsExceptionIfExists(): void
    {
        $immutableRoutes = new RoutesImmutable([
            new Route('/test/', 'Test'),
        ]);

        $this->expectException(\Exception::class);
        $immutableRoutes->addRoutes([
            [
                'url' => '/test/',
                'name' => 'TestModified',
            ],
        ]);
    }

    public function testAddRoutesWithArraysWorksForNew(): void
    {
        $immutableRoutes = new RoutesImmutable();

        $immutableRoutes->addRoutes([
            [
                'url' => '/example/',
                'name' => 'Example',
                'vars' => ['component' => 'page'],
            ],
        ]);

        $this->assertCount(1, $immutableRoutes->getRoutes());
    }

    public function testGetRouteWorks(): void
    {
        $immutableRoutes = new RoutesImmutable([
            new Route('/test/', 'Test'),
        ]);

        $route = $immutableRoutes->getRoute('/test/');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('/test/', $route->getUrl());
    }

    public function testExportWorks(): void
    {
        $immutableRoutes = new RoutesImmutable([
            new Route('/example/', 'Example', ['component' => 'page']),
            new Route('/test/', 'Test', ['component' => 'test']),
        ]);

        $export = $immutableRoutes->export();

        $this->assertIsArray($export);
        $this->assertCount(2, $export);
        $this->assertArrayHasKey('example', $export);
        $this->assertArrayHasKey('test', $export);
    }

    public function testImmutabilityPreventsMutation(): void
    {
        $immutableRoutes = new RoutesImmutable([
            new Route('/initial/', 'Initial'),
        ]);

        // Try to add a route that already exists
        try {
            $immutableRoutes->addRoute(new Route('/initial/', 'Modified'));
            $this->fail('Expected exception was not thrown');
        } catch (\Exception $e) {
            $this->assertStringContainsString('immutable', $e->getMessage());
        }

        // Verify original route is unchanged
        $route = $immutableRoutes->getRoute('/initial/');
        $this->assertEquals('initial', $route->getName());
    }

    public function testComplexImmutableWorkflow(): void
    {
        // Start with initial routes
        $immutableRoutes = new RoutesImmutable([
            new Route('/home/', 'Home', ['component' => 'main']),
            new Route('/about/', 'About', ['component' => 'page']),
        ]);

        // Add new routes
        $immutableRoutes->addRoutes([
            new Route('/contact/', 'Contact', ['component' => 'contact']),
            [
                'url' => '/search/',
                'name' => 'Search',
                'vars' => ['component' => 'search'],
            ],
        ]);

        $this->assertCount(4, $immutableRoutes->getRoutes());

        // Verify we cannot modify existing routes
        $this->expectException(\Exception::class);
        $immutableRoutes->addRoute(new Route('/home/', 'HomeModified'));
    }
}
