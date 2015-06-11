<?php

namespace Optimus\LaravelBatch\ResultFormatter;

use Optimus\LaravelBatch\Response;

class OptimusResultFormatter implements ResultFormatterInterface {

    public function formatResult(Response $response) {
        $result = new \StdClass;

        $result->statusCode = $response->statusCode;
        $result->data = $response->data;

        $etag = $response->responseObject->headers->get("etag");
        if ($etag !== null) {
            $result->etag = $etag;
        }

        return $result;
    }

}