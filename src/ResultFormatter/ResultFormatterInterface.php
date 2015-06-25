<?php

namespace Optimus\LaravelBatch\ResultFormatter;

use Symfony\Component\HttpFoundation\Response;

interface ResultFormatterInterface {

    public function formatResult(Response $response);

}