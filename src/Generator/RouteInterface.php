<?php

namespace JDZ\Router\Generator;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
interface RouteInterface
{
    public function getUrl(): string;
    public function getName(): string;
}
