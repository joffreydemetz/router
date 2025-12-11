<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Router;

use JDZ\Router\Router;
use JDZ\Router\RouterException;
use Symfony\Component\HttpFoundation\Request;

class Route
{
	protected Router $router;
	protected Request $request;
	private array $parameters = [];
	private bool $isJson = false;

	protected array $attrs_props = ['_format', '_route', 'controller'];
	protected array $query_props = ['controller'];

	public function __construct(Router $router, Request $request)
	{
		$this->router = $router;
		$this->request = $request;
	}

	public function isJson(): bool
	{
		return $this->isJson;
	}

	public function load(): void
	{
		$path = trim($this->request->getPathInfo(), '/');
		$path = '/' . $path . '/';

		if (false === ($parameters = $this->router->match())) {
			$e = new RouterException('Route not found');
			$e->setRequestUri($this->request->getRequestUri());
			$e->setRequestPath($path);
			throw $e;
		}

		$this->parameters = $parameters;

		$this->parse($this->parameters);

		if ('json' === $this->request->attributes->get('_format')) {
			$this->isJson = true;
		}
	}

	protected function parse(array $vars): void
	{
		foreach ($this->attrs_props as $key) {
			if (isset($vars[$key])) {
				$this->request->attributes->set($key, $vars[$key]);

				if (!in_array($key, $this->query_props, true)) {
					unset($vars[$key]);
				}
			}
		}

		if ($queryString = $this->request->getQueryString()) {
			$queryParts = explode('&', $queryString);

			if (count($queryParts) > 0) {
				foreach ($queryParts as $queryPart) {
					if (str_contains($queryPart, '=')) {
						list($k, $v) = explode('=', $queryPart, 2);
						if (!isset($vars[$k])) {
							$vars[$k] = $v;
						}
					} elseif (!isset($vars[$queryPart])) {
						$vars[$queryPart] = '';
					}
				}
			}
		}

		foreach ($vars as $key => $value) {
			$this->request->query->set($key, $value);
		}
	}
}
