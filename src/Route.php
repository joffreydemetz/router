<?php

namespace JDZ\Router;

use JDZ\Router\Router;
use JDZ\Router\NoRoutesException;
use JDZ\Router\RouterException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Route
{
  private Router $router;
  private Request $request;
  private array $parameters = [];
  private bool $isJson = false;

  const ATTRS_PROPS = ['_format', '_route', 'component', 'task', 'render', 'template'];
  const QUERY_PROPS = ['component', 'task'];

  public function load()
  {
    $path = trim($this->request->getPathInfo(), '/');
    $path = '/' . $path . '/';

    if (false === ($parameters = $this->router->match())) {
      if (preg_match("/\/json\/.+/", $path)) {
        $this->isJson = true;
      }

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

    if ('' === $this->request->attributes->get('component', '')) {
      $e = new NoRoutesException('Route does not contain any component to call');
      $e->setRequestUri($this->request->getRequestUri());
      $e->setRequestPath($path);
      throw $e;
    }
  }

  protected function parse(array $vars)
  {
    foreach (self::ATTRS_PROPS as $key) {
      if (isset($vars[$key])) {
        $this->request->attributes->set($key, $vars[$key]);

        if (!in_array($key, self::QUERY_PROPS)) {
          unset($vars[$key]);
        }
      }
    }

    if ($queryString = $this->request->getQueryString()) {
      $queryParts = explode('&', $queryString);

      if (count($queryParts) > 0) {
        foreach ($queryParts as $queryPart) {
          if (strpos($queryPart, '=') !== false) {
            list($k, $v) = explode('=', $queryPart, 2);
            if (!isset($vars[$k])) {
              $vars[$k] = $v;
            }
          } elseif (!isset($vars[$queryPart])) {
            $vars[$queryPart] = '';
          }
        }
      }

      if (isset($vars['debug'])) {
        $vars['debug'] = true;
        $this->request->attributes->set('debug', true);
        // unset($vars['debug']);
      }
    }

    foreach ($vars as $key => $value) {
      $this->request->query->set($key, $value);
    }
  }
}
