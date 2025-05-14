<?php

namespace JDZ\Router;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class RouterException extends \RuntimeException
{
  private string $requestUri = '';
  private string $requestPath = '';

  public function setRequestUri(string $requestUri)
  {
    $this->requestUri = $requestUri;
    return $this;
  }

  public function getRequestUri(): string
  {
    return $this->requestUri;
  }

  public function setRequestPath(string $requestPath)
  {
    $this->requestPath = $requestPath;
    return $this;
  }

  public function getRequestPath(): string
  {
    return $this->requestPath;
  }
}
