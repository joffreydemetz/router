<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Router;

use JDZ\Router\RouterException;

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
