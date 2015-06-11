<?php

namespace Optimus\LaravelBatch\ResponseFormatter;

interface ResponseFormatterInterface {

    public function formatResponse($status, array $successes, array $errors);

}