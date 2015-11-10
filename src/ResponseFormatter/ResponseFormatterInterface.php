<?php

namespace Optimus\BatchRequest\ResponseFormatter;

interface ResponseFormatterInterface {

    public function formatResponse($errorneous, array $responses);

}
