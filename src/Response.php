<?php

namespace Optimus\LaravelBatch;

/**
 * A simple DTO that represents a response from a 
 * batch action. Includes some meta data (the original response object, 
 * status code, api status) and the actual response data
 */
class Response {

    public $responseObject;

    public $statusCode;

    public $status;

    public $data;

}