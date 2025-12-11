<?php

use PHPUnit\Framework\TestCase;
use JDZ\Router\Router;
use JDZ\Router\RouterException;
use Symfony\Component\HttpFoundation\Request;

class RouterTest extends TestCase
{
    private string $routesPath;

    protected function setUp(): void
    {
        $this->routesPath = __DIR__ . '/../examples/routes/';
    }

    public function testRouterConstruction(): void
    {
        $request = Request::create('/');
        $router = new Router($this->routesPath, $request);

        $this->assertInstanceOf(Router::class, $router);
    }

    public function testAddYmlLoadsRoutes(): void
    {
        $request = Request::create('/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $routes = $router->getRoutes();
        $this->assertNotEmpty($routes);
        $this->assertArrayHasKey('home', $routes);
        $this->assertEquals('/', $routes['home']);
    }

    public function testAddYmlNonExistentFile(): void
    {
        $request = Request::create('/');
        $router = new Router($this->routesPath, $request);

        // Should not throw exception for non-existent file
        $router->addYml('non-existent.yml');
        $this->assertTrue(true);
    }

    public function testMatchHomeRoute(): void
    {
        $request = Request::create('/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $match = $router->match('/');

        $this->assertIsArray($match);
        $this->assertEquals('main', $match['component']);
        $this->assertEquals('home', $match['task']);
    }

    public function testMatchSearchRoute(): void
    {
        $request = Request::create('/search/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $match = $router->match('/search/');

        $this->assertIsArray($match);
        $this->assertEquals('search', $match['component']);
        $this->assertEquals('display', $match['task']);
    }

    public function testMatchReturnsFalseForInvalidRoute(): void
    {
        $request = Request::create('/invalid-route/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $match = $router->match('/invalid-route/');

        $this->assertFalse($match);
    }

    public function testUrlGenerationWithDefaultHome(): void
    {
        $request = Request::create('/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $url = $router->url();

        $this->assertEquals('/', $url);
    }

    public function testUrlGenerationWithNamedRoute(): void
    {
        $request = Request::create('/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $url = $router->url('search');

        $this->assertEquals('/search/', $url);
    }

    public function testUrlGenerationWithParameters(): void
    {
        $request = Request::create('/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $url = $router->url('jsonPage', ['slug' => 'test-page']);

        $this->assertEquals('/json/page/test-page/', $url);
    }

    public function testUrlGenerationAbsolute(): void
    {
        $request = Request::create('https://example.com/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $url = $router->url('home', [], true);

        $this->assertStringContainsString('http', $url);
        $this->assertStringContainsString('example.com', $url);
    }

    public function testUrlGenerationThrowsExceptionForInvalidRoute(): void
    {
        $request = Request::create('/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $this->expectException(RouterException::class);
        $router->url('non-existent-route');
    }

    public function testAddRedirectPaths(): void
    {
        $request = Request::create('/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $router->addRedirectPaths([
            '/old-path/' => '/search/',
            '/another-old/' => '/vault/',
        ]);

        $this->assertInstanceOf(Router::class, $router);
    }

    public function testAddRedirectPathsInvalidSamePath(): void
    {
        $request = Request::create('/');
        $router = new Router($this->routesPath, $request);

        $this->expectException(RouterException::class);
        $router->addRedirectPaths([
            '/same/' => '/same/',
        ]);
    }

    public function testAddRedirectPathsInvalidEmptyPaths(): void
    {
        $request = Request::create('/');
        $router = new Router($this->routesPath, $request);

        $this->expectException(RouterException::class);
        $router->addRedirectPaths([
            '' => '',
        ]);
    }

    public function testGetCurrentUrl(): void
    {
        $request = Request::create('https://example.com/search/');
        $router = new Router($this->routesPath, $request);

        $currentUrl = $router->getCurrentUrl();

        $this->assertEquals('https://example.com/search/', $currentUrl);
    }

    public function testGetCurrentPath(): void
    {
        $request = Request::create('/search/page/');
        $router = new Router($this->routesPath, $request);

        $currentPath = $router->getCurrentPath();

        $this->assertEquals('search/page/', $currentPath);
    }

    public function testGetCurrentPathRoot(): void
    {
        $request = Request::create('/');
        $router = new Router($this->routesPath, $request);

        $currentPath = $router->getCurrentPath();

        $this->assertEquals('', $currentPath);
    }

    public function testGetRoutes(): void
    {
        $request = Request::create('/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');
        $router->addYml('routes2.yml');

        $routes = $router->getRoutes();

        $this->assertIsArray($routes);
        $this->assertGreaterThan(0, count($routes));
    }

    public function testMultipleYmlFiles(): void
    {
        $request = Request::create('/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');
        $router->addYml('routes2.yml');
        $router->addYml('grouped.yml');

        $routes = $router->getRoutes();

        $this->assertIsArray($routes);
        $this->assertArrayHasKey('home', $routes);
    }

    public function testGetRouteMethod(): void
    {
        $request = Request::create('/search/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $route = $router->getRoute('/search/');

        $this->assertNotFalse($route);
        $this->assertEquals('/search/', $route->getPath());
    }

    public function testGetRouteReturnsFalseForInvalidPath(): void
    {
        $request = Request::create('/invalid/');
        $router = new Router($this->routesPath, $request);
        $router->addYml('routes.yml');

        $route = $router->getRoute('/invalid/');

        $this->assertFalse($route);
    }
}
