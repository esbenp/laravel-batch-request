<?php

namespace Optimus\BatchRequest\ResponseFormatter;

interface ResponseFormatterInterface {

    public function formatResponse($status, array $successes, array $errors);

}