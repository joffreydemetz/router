<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Router\Generator;

interface RouteInterface
{
    public function getUrl(): string;
    public function getName(): string;
}
