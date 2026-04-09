<?php

declare(strict_types=1);

namespace JDZ\Router\Contract;

use Symfony\Component\Routing\Route;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
interface RouterInterface
{
    public function url(?string $name = null, array $params = [], bool $absolute = false): string;

    public function match(?string $pathinfo = null): array|false;

    public function getRoute(?string $pathinfo = null): Route|false;

    public function getCurrentUrl(): string;

    public function getCurrentPath(): string;

    public function getRoutes(): array;
}
