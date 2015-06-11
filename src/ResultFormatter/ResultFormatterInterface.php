<?php

namespace Optimus\LaravelBatch\ResultFormatter;

use Optimus\LaravelBatch\Response;

interface ResultFormatterInterface {

    public function formatResult(Response $response);

}