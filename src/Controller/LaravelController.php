<?php

namespace Optimus\LaravelBatch\Controller;

use Illuminate\Routing\Controller;

class LaravelController extends Controller {

    public function request()
    {
        $app = app();

        $actionsKey = $app['config']->get('batchrequest.actions_key');
        $actions = $app['request']->get($actionsKey, []);

        $response = $app['batchrequest']->request($actions);

        return response()->json($response);
    }

}