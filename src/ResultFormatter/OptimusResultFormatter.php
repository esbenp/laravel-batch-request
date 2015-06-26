<?php

namespace Optimus\BatchRequest\ResultFormatter;

use Exception;
use Illuminate\Http\Response as LaravelResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class OptimusResultFormatter implements ResultFormatterInterface {

    public function formatResult(Response $response) {
        $result = new \StdClass;

        $result->statusCode = $response->getStatusCode();
        $result->data = $this->formatData($response);

        $etag = $response->headers->get("etag");
        if ($etag !== null) {
            $result->etag = $etag;
        }

        return $result;
    }

    private function formatData(Response $response)
    {
        if (!$response->isSuccessful()) {
            // Response will have an exception attached
            if ($response instanceof LaravelResponse && $response->exception instanceof Exception) {
                return $this->formatException($response->exception);
            }
        }

        $content = $response->getContent();

        return $response instanceof JsonResponse ? json_decode($content) : $content;
    }

    private function formatException(Exception $exception)
    {
        return (object) [
                trans('batchrequest::responses.exception_code') => $exception->getCode(),
                trans('batchrequest::responses.exception_message') => $exception->getMessage(),
                trans('batchrequest::responses.exception_exception') => (string) $exception,
                trans('batchrequest::responses.exception_line') => $exception->getLine(),
                trans('batchrequest::responses.exception_file') => $exception->getFile()
            ];
    }

}
