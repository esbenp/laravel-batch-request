<?php

namespace Optimus\BatchRequest\ResponseFormatter;

class PlainResponseFormatter implements ResponseFormatterInterface {

    public function formatResponse($errorneous, array $responses)
    {
        return $responses;
    }

}
