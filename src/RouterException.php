<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Router;

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
