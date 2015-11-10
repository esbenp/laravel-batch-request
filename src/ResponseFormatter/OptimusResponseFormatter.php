<?php

namespace Optimus\BatchRequest\ResponseFormatter;

class OptimusResponseFormatter implements ResponseFormatterInterface
{

    public function formatResponse($errorneous, array $responses)
    {
        return [
            'status' => $errorneous === true ? 'error' : 'success',
            'responses' => $responses
        ];
    }
}
