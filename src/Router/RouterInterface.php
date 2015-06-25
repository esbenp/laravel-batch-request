<?php

namespace Optimus\BatchRequest\Router;

interface RouterInterface {

    public function batch(array $requests);

}