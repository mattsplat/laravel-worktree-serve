<?php

namespace Mattcoleman\PrServe\Screens\Contracts;

use Mattcoleman\PrServe\Router;

abstract class Screen
{
    protected Router $router;

    public function render(array $params = []): void
    {
        throw new \Exception('Method not implemented');
    }

    public function setRouter(Router $param)
    {
        $this->router = $param;
    }
}