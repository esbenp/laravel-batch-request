<?php

namespace Optimus\BatchRequest\ResultFormatter;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PlainResultFormatter implements ResultFormatterInterface {

    public function formatResult(Response $response) {
        $content = $response->getContent();

        return $response instanceof JsonResponse ? json_decode($content) : $content;
    }

}