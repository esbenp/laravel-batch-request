<?php

namespace Optimus\BatchRequest\ResultFormatter;

use Symfony\Component\HttpFoundation\Response;

interface ResultFormatterInterface {

    public function formatResult(Response $response);

}