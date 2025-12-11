<?php

use PHPUnit\Framework\TestCase;
use JDZ\Router\Generator\Route;
use JDZ\Router\Generator\Routes;

class GeneratorRoutesTest extends TestCase
{
    public function testRoutesCreation(): void
    {
        $routes = new Routes();

        $this->assertInstanceOf(Routes::class, $routes);
    }

    public function testRoutesCreationWithInitialRoutes(): void
    {
        $routes = new Routes([
            new Route('/example/', 'Example'),
            new Route('/test/', 'Test'),
        ]);

        $this->assertCount(2, $routes->getRoutes());
    }

    public function testCreateRoute(): void
    {
        $routes = new Routes();
        $route = $routes->createRoute('/test/', 'TestRoute', [
            'component' => 'page',
        ]);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('/test/', $route->getUrl());
        $this->assertEquals('testRoute', $route->getName());
    }

    public function testCreateRouteUpdatesExisting(): void
    {
        $routes = new Routes();
        $route1 = $routes->createRoute('/test/', 'TestRoute', [
            'component' => 'page',
        ]);
        $routes->addRoute($route1);

        $route2 = $routes->createRoute('/test/', '', [
            'task' => 'display',
        ]);

        $this->assertEquals('display', $route2->getVar('task'));
        $this->assertEquals('page', $route2->getVar('component'));
    }

    public function testAddRoute(): void
    {
        $routes = new Routes();
        $route = new Route('/test/', 'TestRoute');

        $addedRoute = $routes->addRoute($route);

        $this->assertInstanceOf(Route::class, $addedRoute);
        $this->assertCount(1, $routes->getRoutes());
    }

    public function testAddRouteReplacesExisting(): void
    {
        $routes = new Routes();
        $route1 = new Route('/test/', 'TestRoute', ['version' => 1]);
        $route2 = new Route('/test/', 'TestRoute', ['version' => 2]);

        $routes->addRoute($route1);
        $routes->addRoute($route2, true);

        $retrieved = $routes->getRoute('/test/');
        $this->assertEquals(2, $retrieved->getVar('version'));
    }

    public function testAddRouteThrowsExceptionWhenReplaceIsFalse(): void
    {
        $routes = new Routes();
        $route1 = new Route('/test/', 'TestRoute');
        $route2 = new Route('/test/', 'TestRoute');

        $routes->addRoute($route1);

        $this->expectException(\Exception::class);
        $routes->addRoute($route2, false);
    }

    public function testAddRoutesWithRouteObjects(): void
    {
        $routes = new Routes();

        $routes->addRoutes([
            new Route('/example/', 'Example'),
            new Route('/test/', 'Test'),
            new Route('/page/', 'Page'),
        ]);

        $this->assertCount(3, $routes->getRoutes());
    }

    public function testAddRoutesWithArrays(): void
    {
        $routes = new Routes();

        $routes->addRoutes([
            [
                'url' => '/example/',
                'name' => 'Example',
                'vars' => ['component' => 'page'],
            ],
            [
                'url' => '/test/',
                'name' => 'Test',
                'vars' => ['component' => 'test'],
            ],
        ]);

        $this->assertCount(2, $routes->getRoutes());
    }

    public function testAddRoutesWithReset(): void
    {
        $routes = new Routes([
            new Route('/initial/', 'Initial'),
        ]);

        $routes->addRoutes([
            new Route('/new/', 'New'),
        ], false, true);

        $allRoutes = $routes->getRoutes();
        $this->assertCount(1, $allRoutes);

        $firstRoute = $allRoutes[0];
        $this->assertEquals('/new/', $firstRoute['path']);
    }

    public function testAddRoutesThrowsExceptionWhenReplaceIsFalse(): void
    {
        $routes = new Routes();
        $routes->addRoute(new Route('/test/', 'Test'));

        $this->expectException(\Exception::class);
        $routes->addRoutes([
            new Route('/test/', 'TestModified'),
        ], false);
    }

    public function testExport(): void
    {
        $routes = new Routes();
        $routes->addRoutes([
            new Route('/example/', 'Example', ['component' => 'page']),
            new Route('/test/', 'Test', ['component' => 'test']),
        ]);

        $export = $routes->export();

        $this->assertIsArray($export);
        $this->assertCount(2, $export);
        $this->assertArrayHasKey('example', $export);
        $this->assertArrayHasKey('test', $export);
    }

    public function testGetRoutes(): void
    {
        $routes = new Routes();
        $routes->addRoutes([
            new Route('/example/', 'Example'),
            new Route('/test/', 'Test'),
        ]);

        $allRoutes = $routes->getRoutes();

        $this->assertIsArray($allRoutes);
        $this->assertCount(2, $allRoutes);
        $this->assertEquals('/example/', $allRoutes[0]['path']);
    }

    public function testGetRoute(): void
    {
        $routes = new Routes();
        $route = new Route('/test/', 'Test');
        $routes->addRoute($route);

        $retrieved = $routes->getRoute('/test/');

        $this->assertInstanceOf(Route::class, $retrieved);
        $this->assertEquals('/test/', $retrieved->getUrl());
    }

    public function testGetRouteReturnsFalseForNonExistent(): void
    {
        $routes = new Routes();

        $retrieved = $routes->getRoute('/non-existent/');

        $this->assertFalse($retrieved);
    }

    public function testGet(): void
    {
        $routes = new Routes();
        $route = new Route('/test/', 'Test');
        $routes->addRoute($route);

        $retrieved = $routes->get('/test/');

        $this->assertInstanceOf(Route::class, $retrieved);
        $this->assertEquals('/test/', $retrieved->getUrl());
    }

    public function testGetThrowsExceptionForNonExistent(): void
    {
        $routes = new Routes();

        $this->expectException(\Exception::class);
        $routes->get('/non-existent/');
    }

    public function testAddRoutesWithJsonFlag(): void
    {
        $routes = new Routes();

        $routes->addRoutes([
            [
                'url' => '/json/data/',
                'name' => 'JsonData',
                'vars' => ['component' => 'api'],
                'json' => true,
            ],
        ]);

        $route = $routes->getRoute('/json/data/');
        $this->assertTrue($route->isJson());
    }

    public function testComplexRoutesWorkflow(): void
    {
        $routes = new Routes();

        // Add initial routes
        $routes->addRoutes([
            new Route('/home/', 'Home', ['component' => 'main', 'task' => 'home']),
            new Route('/about/', 'About', ['component' => 'page', 'task' => 'display']),
        ]);

        // Create and add a route
        $searchRoute = $routes->createRoute('/search/', 'Search', [
            'component' => 'search',
            'task' => 'display',
        ]);
        $routes->addRoute($searchRoute);

        // Add via array
        $routes->addRoutes([
            [
                'url' => '/json/api/',
                'name' => 'Api',
                'vars' => ['component' => 'api'],
                'json' => true,
            ],
        ]);

        $allRoutes = $routes->getRoutes();
        $this->assertCount(4, $allRoutes);

        $export = $routes->export();
        $this->assertCount(4, $export);
        $this->assertArrayHasKey('home', $export);
        $this->assertArrayHasKey('about', $export);
        $this->assertArrayHasKey('search', $export);
        $this->assertArrayHasKey('api', $export);
    }
}
