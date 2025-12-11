<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Router;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Router
{
	private string $basePath;
	private Request $request;
	private RouteCollection $collection;
	private RequestContext $requestContext;
	private array $redirectPaths = []; // Collects oldRoute/newRoute associations.
	private array $allow = []; // Collects HTTP methods that would be allowed for the request.
	private array $allowSchemes = []; // Collects URI schemes that would be allowed for the request.

	public function __construct(string $basePath, Request $request)
	{
		$this->basePath = $basePath;
		$this->request = $request;

		$this->requestContext = new RequestContext();
		$this->requestContext->fromRequest($this->request);

		$this->collection = new RouteCollection();
	}

	public function addYml(string $path): self
	{
		if (file_exists($this->basePath . $path)) {
			try {
				$locator = new FileLocator([$this->basePath]);
				$loader	= new YamlFileLoader($locator);

				$collection = $loader->load($path);

				$this->collection->addCollection($collection);
			} catch (\Throwable $e) {
				throw new RouterException('Error loading routes from "' . $path . '"', 0, $e);
			}
		}

		return $this;
	}

	public function addRedirectPaths(array $redirects): self
	{
		foreach ($redirects as $from => $to) {
			$from = trim($from, '/');
			$to = trim($to, '/');

			if (empty($from) && empty($to)) {
				throw new RouterException('Invalid redirect path: "' . $from . '" => "' . $to . '"');
			}

			if ($from === $to) {
				throw new RouterException('Invalid redirect path: "' . $from . '" => "' . $to . '"');
			}

			$from = '' === $from ? '/' : '/' . $from . '/';
			$to = '' === $to ? '/' : '/' . $to . '/';

			$this->redirectPaths[$from] = $to;
		}

		return $this;
	}

	public function match(?string $pathinfo = null): array|false
	{
		$pathinfo ??= $this->request->getPathInfo();

		$matcher = new UrlMatcher($this->collection, $this->requestContext);

		try {
			if ($vars = $matcher->match($pathinfo)) {
				return $vars;
			}
		} catch (\Throwable) {
		}

		return false;
	}

	public function url(?string $name = null, array $params = [], bool $absolute = false): string
	{
		$name ??= 'home';

		try {
			$generator = new UrlGenerator($this->collection, $this->requestContext);

			if (true === $absolute) {
				return $generator->generate($name, $params, UrlGenerator::ABSOLUTE_URL);
			}

			return $generator->generate($name, $params);
		} catch (RouteNotFoundException $e) {
			throw new RouterException('Url not found for "' . $name . '"', 0, $e);
		}
	}

	public function getRoute(?string $pathinfo = null): Route|false
	{
		$pathinfo ??= $this->request->getPathInfo();

		foreach ($this->collection as $route) {
			$compiledRoute = $route->compile();

			// check the static prefix of the URL first. Only use the more expensive preg_match when it matches
			if ('' !== $compiledRoute->getStaticPrefix() && !str_starts_with($pathinfo, $compiledRoute->getStaticPrefix())) {
				continue;
			}

			if (!preg_match($compiledRoute->getRegex(), $pathinfo, $matches)) {
				continue;
			}

			$hostMatches = [];
			if ($compiledRoute->getHostRegex() && !preg_match($compiledRoute->getHostRegex(), $this->requestContext->getHost(), $hostMatches)) {
				continue;
			}

			$hasRequiredScheme = !$route->getSchemes() || $route->hasScheme($this->requestContext->getScheme());
			if ($requiredMethods = $route->getMethods()) {
				$method = $this->requestContext->getMethod();

				// HEAD and GET are equivalent as per RFC
				if ('HEAD' === $method) {
					$method = 'GET';
				}

				if (!in_array($method, $requiredMethods, true)) {
					if ($hasRequiredScheme) {
						$this->allow = array_merge($this->allow, $requiredMethods);
					}
					continue;
				}
			}

			if (!$hasRequiredScheme) {
				$this->allowSchemes = array_merge($this->allowSchemes, $route->getSchemes());
				continue;
			}

			return $route;
		}

		$redirect = $this->isAnOldRoute($pathinfo);

		if ($redirect) {
			$this->request->attributes->set('_redirect', $redirect);
			return $this->getRoute($redirect);
		}

		return false;
	}

	public function getCurrentUrl(): string
	{
		return 'https://' . $this->request->getHost() . '/' . $this->getCurrentPath();
	}

	public function getCurrentPath(): string
	{
		$path = trim($this->request->getPathInfo(), '/');

		if ($path) {
			$path .= '/';
		}

		return $path;
	}

	public function getRoutes(): array
	{
		$routes = [];
		foreach ($this->collection as $name => $route) {
			$routes[$name] = $route->getPath();
		}
		return $routes;
	}

	private function isAnOldRoute(string $path): string
	{
		return $this->redirectPaths[$path] ?? '';
	}
}
