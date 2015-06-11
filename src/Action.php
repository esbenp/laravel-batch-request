<?php

namespace Optimus\LaravelBatch;

/**
 * A class representing a batch action. A DTO.
 * See BatchRequest for more
 */
class Action {

    public $url;

    public $method = "GET";

    public $headers = [];

    public $data = [];

    public $key = null;

    /**
     * Statically create the action from an array of
     * parameters 
     * 
     * @param  array $parameters
     * @return Action            
     */
    public static function createFromArray(array $parameters)
    {
        $class = new self();

        if (isset($parameters["action"])) {
            $class->url = $parameters["action"];
        }

        if (isset($parameters["method"])) {
            $class->method = $parameters["method"];
        }

        if (isset($parameters["headers"])) {
            $class->headers = $parameters["headers"];
        }

        if (isset($parameters["data"])) {
            $class->data = $parameters["data"];
        }

        if (isset($parameters["key"])) {
            $class->key = $parameters["key"];
        }

        return $class;
    }

}