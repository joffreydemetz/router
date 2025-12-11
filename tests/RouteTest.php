<?php

use PHPUnit\Framework\TestCase;
use JDZ\Router\Route;
use JDZ\Router\Router;
use JDZ\Router\RouterException;
use Symfony\Component\HttpFoundation\Request;

class RouteTest extends TestCase
{
    private string $routesPath;

    protected function setUp(): void
    {
        $this->routesPath = __DIR__ . '/../examples/routes/';
    }

    public function testRouteConstruction(): void
    {
        $request = Request::create('/');
        $router = new Router($this->routesPath, $request);
        $route = new Route($router, $request);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertFalse($route->isJson());
    }

    public function testLoadValidRoute(): void
    {
        $request = Request::create('/search/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $route = new Route($router, $request);
        $route->load();

        $this->assertEquals('search', $request->query->get('component'));
        $this->assertEquals('display', $request->query->get('task'));
    }

    public function testLoadJsonRoute(): void
    {
        $request = Request::create('/json/search/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $route = new Route($router, $request);
        $route->load();

        $this->assertTrue($route->isJson());
        $this->assertEquals('json', $request->attributes->get('_format'));
    }

    public function testLoadInvalidRouteThrowsException(): void
    {
        $request = Request::create('/invalid-path/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $route = new Route($router, $request);

        $this->expectException(RouterException::class);
        $route->load();
    }

    public function testLoadSetsRequestAttributes(): void
    {
        $request = Request::create('/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $route = new Route($router, $request);
        $route->load();

        $this->assertEquals('main', $request->query->get('component'));
        $this->assertEquals('home', $request->query->get('task'));
    }

    public function testLoadWithQueryString(): void
    {
        $request = Request::create('/search/?q=test&filter=active');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $route = new Route($router, $request);
        $route->load();

        $this->assertEquals('test', $request->query->get('q'));
        $this->assertEquals('active', $request->query->get('filter'));
        $this->assertEquals('search', $request->query->get('component'));
    }

    public function testJsonPathDetection(): void
    {
        $request = Request::create('/json/invalid-path/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $route = new Route($router, $request);

        try {
            $route->load();
        } catch (RouterException $e) {
            $this->assertTrue($route->isJson());
        }
    }

    public function testExceptionContainsRequestInfo(): void
    {
        $request = Request::create('/invalid-route/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $route = new Route($router, $request);

        try {
            $route->load();
            $this->fail('Expected RouterException to be thrown');
        } catch (RouterException $e) {
            $this->assertNotEmpty($e->getRequestUri());
            $this->assertNotEmpty($e->getRequestPath());
        }
    }
}
