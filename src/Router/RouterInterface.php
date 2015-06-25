<?php

namespace Optimus\LaravelBatch\Router;

interface RouterInterface {

    public function batch(array $requests);

}