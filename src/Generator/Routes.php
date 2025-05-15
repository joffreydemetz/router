<?php

namespace JDZ\Router\Generator;

use JDZ\Router\Generator\Route;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Routes 
{
	protected array $routes = [];

	public function __construct(array $routes = [])
	{
		if (!empty($routes)) {
			$this->addRoutes($routes);
		}
	}

	public function createRoute(string $url, string $name = '', array $vars = [], bool $json = false): Route
	{
		if (isset($this->routes[$url])) {
			$route = $this->routes[$url];
			if ($vars) {
				$route->setVars($vars);
			}
		} else {
			$route = new Route($url, $name, $vars, $json);
		}
		return $route;
	}

	public function addRoute(Route $route, bool $replace = true): Route
	{
		if (isset($this->routes[$route->getUrl()])) {
			if (false === $replace) {
				throw new \Exception('Route ' . $route->getUrl() . ' is already set and cannot be replaced');
			}
		}

		$this->routes[$route->getUrl()] = $route;

		return $this->routes[$route->getUrl()];
	}

	public function addRoutes(array $routes, bool $replace = false, bool $reset = false)
	{
		if ($reset) {
			$this->routes = [];
		}

		foreach ($routes as $route) {
			if (!($route instanceof RouteInterface)) {
				$route = $this->createRoute($route['url'], $route['name'] ?? '', $route['vars'] ?? [], $route['json'] ?? false);
			}

			if (isset($this->routes[$route->getUrl()])) {
				if (false === $replace) {
					throw new \Exception('Route ' . $route->getUrl() . ' is already set');
				}
				unset($this->routes[$route->getUrl()]);
			}

			$this->addRoute($route);
		}
	}

	public function export(): array
	{
		$routes = [];
		foreach ($this->routes as $route) {
			list($routeName, $routeTitle, $routeJson, $routeData) = $route->export();
			$routes[$routeName] = $routeData;
		}
		return $routes;
	}

	public function getRoutes(): array
	{
		$routes = [];
		foreach($this->routes as $route){
			$routes[] = $route->toArray();
		}
		return $routes;
	}
	
	public function getRoute(string $url): Route|false
	{
		return $this->routes[$url] ?? false;
	}

	public function get(string $url): Route
	{
		if (false === ($route = $this->getRoute($url))) {
			throw new \Exception('Route ' . $url . ' not found');
		}

		return $route;
	}
}
