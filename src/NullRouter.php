<?php

declare(strict_types=1);

namespace JDZ\Router;

use JDZ\Router\Contract\RouterInterface;
use Symfony\Component\Routing\Route;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class NullRouter implements RouterInterface
{
    public function url(?string $name = null, array $params = [], bool $absolute = false): string
    {
        return '';
    }

    public function match(?string $pathinfo = null): array|false
    {
        return false;
    }

    public function getRoute(?string $pathinfo = null): Route|false
    {
        return false;
    }

    public function getCurrentUrl(): string
    {
        return '';
    }

    public function getCurrentPath(): string
    {
        return '';
    }

    public function getRoutes(): array
    {
        return [];
    }
}
