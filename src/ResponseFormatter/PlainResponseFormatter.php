<?php

namespace Optimus\LaravelBatch\ResponseFormatter;

class PlainResponseFormatter implements ResponseFormatterInterface {

    public function formatResponse($status, array $successes, array $errors)
    {
        return array_merge($errors, $successes);
    }

}