<?php

namespace Optimus\BatchRequest\Controller;

use Illuminate\Foundation\Application;
use Illuminate\Routing\Controller;

class LaravelController extends Controller {

    private $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function request()
    {
        $actionsKey = $this->app['config']->get('batchrequest.actions_key');
        $actions = $this->app['request']->get($actionsKey, []);

        $response = $this->app['batchrequest']->request($actions);

        return response()->json($response);
    }

}