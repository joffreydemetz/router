<?php

namespace JDZ\Router;

use JDZ\Router\RouterException;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class ChangeRouteException extends RouterException
{
  protected string $newRoute = '';

  public function setNewRoute(string $newRoute)
  {
    $this->newRoute = $newRoute;
    return $this;
  }

  public function getNewRoute(): string
  {
    return $this->newRoute;
  }
}
