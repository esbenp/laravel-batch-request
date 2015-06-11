<?php

namespace Optimus\LaravelBatch\ResultFormatter;

use Optimus\LaravelBatch\Response;

class PlainResultFormatter implements ResultFormatterInterface {

    public function formatResult(Response $response) {
        return $response->data;
    }

}