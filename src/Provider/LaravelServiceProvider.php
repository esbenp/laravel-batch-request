<?php

namespace Optimus\LaravelBatch\Provider;

use Illuminate\Support\ServiceProvider as BaseProvider;
use Optimus\LaravelBatch\BatchRequest;
use Optimus\LaravelBatch\Database\Adapter\Laravel as LaravelDatabase;
use Optimus\LaravelBatch\Router\Adapter\Laravel as LaravelRouter;

class LaravelServiceProvider extends BaseProvider {

    public function register()
    {
        $this->loadConfig();
    }

    public function boot()
    {
        // Registering route in the boot method to let 
        // all router providers be done registering
        $this->registerRoute();
        $this->loadLangFile();
    }

    private function loadConfig()
    {
        if (config('batchrequest') === null) {
            app('config')->set('batchrequest', require __DIR__.'/../config/batchrequest.php');
        }
    }

    private function loadLangFile()
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'batchrequest');
    }

    private function registerRoute()
    {
        $config = config('batchrequest');

        if (!$this->app->routesAreCached() && $config['use_endpoint']) {
            $router = app('router');
            $endpointMethod = $config['endpoint_method'];
            $endpoint = $config['endpoint'];

            call_user_func_array(array($router, $endpointMethod), array($endpoint, function() use($router, $config){
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
            }));
        }
    }

}