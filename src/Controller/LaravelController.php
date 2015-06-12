<?php

namespace Optimus\LaravelBatch\Controller;

use Illuminate\Routing\Controller;
use Optimus\LaravelBatch\BatchRequest;
use Optimus\LaravelBatch\Database\Adapter\Laravel as LaravelDatabase;
use Optimus\LaravelBatch\Router\Adapter\Laravel as LaravelRouter;

class LaravelController extends Controller {

    public function request()
    {
        $config = config('batchrequest');
        $router = app('router');

        $resultFormatterClass = $config['result_formatter'];
        $responseFormatterClass = $config['response_formatter'];

        $batchRequest = new BatchRequest(
                new LaravelRouter($router),
                app('request')->instance(),
                $config,
                new $resultFormatterClass,
                new $responseFormatterClass,
                new LaravelDatabase(app('db'))
        );

        $actionsKey = $config['actions_key'];
        $actions = \Request::get($actionsKey, []);

        $batchRequest->request($actions);

        $response = $batchRequest->response();

        return response()->json($response);
    }

}