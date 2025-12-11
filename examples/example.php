<?php

require_once __DIR__ . '/../vendor/autoload.php';

$routesPath = __DIR__ . '/routes/';
$resultsPath = __DIR__ . '/routes/generated/';

if (!is_dir($resultsPath)) {
    mkdir($resultsPath, 0777, true);
}

// GENERATE ROUTES

try {

    $routes_1 = new \JDZ\Router\Generator\Routes();

    $routes_1->addRoutes([
        new \JDZ\Router\Generator\Route('/example/', 'Example', [
            'component' => 'page',
            'task' => 'display',
            'slug' => 'example',
        ]),

        new \JDZ\Router\Generator\Route('/example2/', 'example-2', [
            'component' => 'page',
            'task' => 'display',
            'slug' => 'example2',
        ]),

        [
            'url' => '/example6/',
            'name' => 'example-6',
            'vars' => [
                'component' => 'page',
                'task' => 'display',
                'slug' => 'example6',
            ],
        ],
    ]);

    $routes_1->addRoute(
        $routes_1->createRoute('/example3/', 'example-3', [
            'component' => 'page',
            'task' => 'display',
            'slug' => 'example3',
        ])
    );


    // Convert routes to an array format suitable for YAML
    $routesArray = $routes_1->export(); // Assuming getRoutes() returns an array

    // Write to a YAML file
    $yamlContent = \Symfony\Component\Yaml\Yaml::dump($routesArray, 4, 2);
    file_put_contents($resultsPath . 'grouped.yml', $yamlContent);

    //echo 'Generated routes and saved to routes.yml: <br>';
    //echo "<pre>", print_r($routesArray, true), "</pre>";
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

// LOAD ROUTES 
try {
    $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

    $router = new \JDZ\Router\Router($routesPath, $request);
    $router->addYml('routes.yml');
    $router->addYml('routes2.yml');
    $router->addYml('generated/grouped.yml');
    $router->addRedirectPaths([
        '/old-search-path/' => '/search/',
        '/another-old-path/' => '/another-new-path/',
    ]);

    echo 'Loaded routes: <br>';
    echo "<pre>", print_r($router->getRoutes(), true), "</pre>";
    exit();
} catch (\JDZ\Router\RouterException $e) {
    echo 'Error: ' . $e->getMessage();
}
