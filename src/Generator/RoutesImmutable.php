<?php

namespace JDZ\Router\Generator;

use JDZ\Router\Generator\Routes;
use JDZ\Router\Generator\Route;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class RoutesImmutable extends Routes
{
  public function createRoute(string $url, string $name = '', array $vars = [], bool $json = false): Route
  {
    if (isset($this->routes[$url])) {
      throw new \Exception('Routes are immutable');
    }

    return parent::createRoute($url, $name, $vars, $json);
  }

  public function addRoute(Route $route, bool $replace = false): Route
  {
    if (isset($this->routes[$route->getUrl()])) {
      throw new \Exception('Routes are immutable');
    }

    return parent::addRoute($route);
  }

  public function addRoutes(array $routes, bool $replace = false, bool $reset = false)
  {
    if (true === $reset || $replace) {
      throw new \Exception('Routes are immutable');
    }

    $errors = [];
    foreach ($routes as $route) {
      if ($route instanceof Route) {
        $url = $route->getUrl();
      } else {
        $url = $route['url'] ?? '';
      }
      if (isset($this->routes[$url])) {
        $errors[] = $url;
      }
    }

    if ($errors) {
      throw new \Exception('Routes are immutable (cannot modify): ' . ' - ' . implode("\n" . ' - ', $errors));
    }

    parent::addRoutes($routes, false, $reset);
  }
}
