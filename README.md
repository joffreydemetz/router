# JDZ Router

`Router` is a PHP routing library built on top of the **Symfony Routing** component. It provides a clean and intuitive API for managing routes, generating URLs, and handling HTTP requests in your web applications.

## Features

- Built on the **Symfony Routing** component for robust routing capabilities.
- Generate routes programmatically and export to YAML format.
- Load routes from YAML files with support for multiple file sources.
- Route matching with automatic request parameter parsing.
- URL generation for named routes (relative and absolute).
- Redirect path management for legacy URL handling.
- Support for JSON endpoints with automatic format detection.
- HTTP method constraints (GET, POST, etc.).
- Route parameter requirements and defaults.
- Immutable route collections for read-only configurations.
- Type-safe with PHP 8.2+ features.

## Installation

Add the package to your project using **Composer**:

```bash
composer require jdz/router
```

## Requirements

- PHP 8.2 or higher
- symfony/config
- symfony/http-foundation
- symfony/routing
- symfony/string
- symfony/yaml (for YAML support)

## Usage

For a complete example, check the `examples` folder in the repository.

### Basic Router Setup

```php
use JDZ\Router\Router;
use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();
$router = new Router(__DIR__ . '/routes/', $request);

// Load routes from YAML files
$router->addYml('routes.yml');
$router->addYml('api-routes.yml');
```

### Generating Routes

```php
use JDZ\Router\Generator\Routes;
use JDZ\Router\Generator\Route;

$routes = new Routes();

// Add routes using Route objects
$routes->addRoutes([
    new Route('/home/', 'Home', [
        'component' => 'main',
        'task' => 'home',
    ]),
    
    new Route('/search/', 'Search', [
        'component' => 'search',
        'task' => 'display',
    ]),
]);

// Add route using array notation
$routes->addRoutes([
    [
        'url' => '/contact/',
        'name' => 'Contact',
        'vars' => [
            'component' => 'contact',
            'task' => 'form',
        ],
    ],
]);

// Create and add a route
$route = $routes->createRoute('/about/', 'About', [
    'component' => 'page',
    'task' => 'display',
    'slug' => 'about',
]);
$routes->addRoute($route);
```

### Export Routes to YAML

```php
use Symfony\Component\Yaml\Yaml;

// Export routes to array format
$routesArray = $routes->export();

// Write to YAML file
$yamlContent = Yaml::dump($routesArray, 4, 2);
file_put_contents('routes.yml', $yamlContent);
```

Example YAML output:

```yaml
home:
    path: /home/
    defaults:
        component: main
        task: home
    methods: [GET]

search:
    path: /search/
    defaults:
        component: search
        task: display
    methods: [GET]
```

### Route Matching

```php
// Match the current request
$match = $router->match();

if ($match !== false) {
    $component = $match['component'];
    $task = $match['task'];
    // Handle the matched route
}

// Match a specific path
$match = $router->match('/search/');
```

### URL Generation

```php
// Generate URL for a named route
$url = $router->url('search');
// Returns: /search/

// Generate URL with parameters
$url = $router->url('userProfile', ['id' => 123]);
// Returns: /user/123/

// Generate absolute URL
$absoluteUrl = $router->url('search', [], true);
// Returns: https://example.com/search/
```

### Loading Routes into Application

```php
use JDZ\Router\Route;

$route = new Route($router, $request);

try {
    $route->load();
    
    // Check if it's a JSON endpoint
    if ($route->isJson()) {
        header('Content-Type: application/json');
    }
    
    // Access parsed parameters
    $component = $request->query->get('component');
    $task = $request->query->get('task');
    
} catch (\JDZ\Router\RouterException $e) {
    // Handle route not found
    http_response_code(404);
    echo 'Page not found';
}
```

### Redirect Management

```php
// Add redirect paths for legacy URLs
$router->addRedirectPaths([
    '/old-path/' => '/new-path/',
    '/legacy/page/' => '/page/',
]);
```

### JSON Routes

```php
// Routes with JSON format
$route = new Route('/json/api/', 'ApiEndpoint', [
    'component' => 'api',
    'task' => 'getData',
], true); // true indicates JSON format

// In YAML:
# jsonApi:
#     path: /json/api/
#     defaults:
#         _format: json
#         component: api
#         task: getData
#     requirements:
#         _format: json
```

### HTTP Method Constraints

```php
$route = new Route('/form/', 'ContactForm', [
    'component' => 'contact',
    'task' => 'form',
]);
$route->setMethods(['GET', 'POST']);
```

### Route Options

```php
$route = new Route('/vault/', 'Vault', [
    'component' => 'secure',
]);
$route->setOption('ignoreLastUrl', true);
```

### Immutable Routes

```php
use JDZ\Router\Generator\RoutesImmutable;

// Create immutable route collection
$immutableRoutes = new RoutesImmutable([
    new Route('/home/', 'Home'),
    new Route('/about/', 'About'),
]);

// Add new routes (allowed)
$immutableRoutes->addRoute(new Route('/contact/', 'Contact'));

// Try to modify existing route (throws exception)
try {
    $immutableRoutes->addRoute(new Route('/home/', 'HomeModified'));
} catch (\Exception $e) {
    // Routes are immutable
}
```

## Testing

The package includes a comprehensive test suite with 100 tests covering all functionality.

To run the tests:

```bash
composer test
```

Or directly with PHPUnit:

```bash
vendor/bin/phpunit --colors=always --testdox
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.
