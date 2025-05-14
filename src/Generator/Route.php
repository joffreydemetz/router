<?php

namespace JDZ\Router\Generator;

use function Symfony\Component\String\u;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Route
{
  private string $url;
  private string $name;
  private bool $json = false;
  private array $vars = [];
  private array $options = [];
  private array $methods = [];

  public function __construct(string $url, string $name = '', array $vars = [], bool $json = false)
  {
    if ($name) {
      $name = u($name);
    } else {
      $name = u($url)->trim('/');
    }

    $name->camel()->truncate(50)->toString();

    $this->url = $url;
    $this->name = (string)$name;
    $this->vars = $vars;
    $this->json = $json;
  }

  public function setUrl(string $url)
  {
    $this->url = $url;
    return $this;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getTitle(): string
  {
    return (string)u($this->name)
      ->snake()
      ->replace('_', ' ')
      ->title();
  }

  public function getUrl(): string
  {
    return $this->url;
  }

  public function withJson(bool $json = true)
  {
    $this->json = $json;
    return $this;
  }

  public function isJson(): bool
  {
    return $this->json;
  }

  public function getVars(): array
  {
    return $this->vars;
  }

  public function setVars(array $vars)
  {
    foreach ($vars as $key => $value) {
      $this->setVar($key, $value);
    }
    return $this;
  }

  public function setVar(string $key, mixed $value)
  {
    $this->vars[$key] = $value;
    return $this;
  }

  public function getVar(string $key): mixed
  {
    return $this->vars[$key] ?? null;
  }

  public function setOptions(array $options)
  {
    foreach ($options as $key => $value) {
      $this->setOption($key, $value);
    }
    return $this;
  }

  public function getOptions(): array
  {
    return $this->options;
  }

  public function setOption(string $key, mixed $value)
  {
    $this->options[$key] = $value;
    return $this;
  }

  public function getOption(string $key): mixed
  {
    return $this->options[$key] ?? null;
  }

  public function setMethods(array $methods)
  {
    $this->methods = $methods;
    return $this;
  }

  public function getMethods(): array
  {
    return $this->methods;
  }

  public function export(): array
  {
    return [
      $this->name,
      $this->toArray()
    ];
  }

  public function toArray(): array
  {
    $route = [
      'path' => $this->url,
      'defaults' => [],
      'methods' => [],
      'requirements' => [],
      'options' => [],
    ];

    foreach ($this->vars as $key => $value) {
      if (null === $value) {
        continue;
      }
      $route['defaults'][$key] = $value;
    }

    foreach ($this->options as $key => $value) {
      if (null === $value) {
        continue;
      }
      $route['options'][$key] = $value;
    }

    if (true === $this->json) {
      $route['defaults']['_format'] = 'json';
      $route['requirements']['_format'] = 'json';
    }

    if ($this->methods) {
      $route['methods'] = $this->methods;
    }

    if (empty($route['defaults'])) {
      unset($route['defaults']);
    }

    if (empty($route['requirements'])) {
      unset($route['requirements']);
    }

    if (empty($route['methods'])) {
      unset($route['methods']);
    }

    if (empty($route['options'])) {
      unset($route['options']);
    }

    return $route;
  }
}
