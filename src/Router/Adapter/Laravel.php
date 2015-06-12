<?php

namespace Optimus\LaravelBatch\Router\Adapter;

use Illuminate\Routing\Router;
use Optimus\LaravelBatch\Router\RouterInterface;

class Laravel implements RouterInterface {

    private $router;

    public function __construct(Router $router) {
        $this->router = $router;
    }

    public function dispatch($request)
    {
        return $this->router->dispatch($request);
    }

}