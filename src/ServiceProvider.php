<?php

namespace Optimus\LaravelBatch;

use Illuminate\Support\ServiceProvider as BaseProvider;

class ServiceProvider extends BaseProvider {

    public function register()
    {
        $this->loadConfig();
        $this->registerRoute();
    }

    public function boot()
    {
        $this->loadLangFile();
    }

    private function loadConfig()
    {
        if (config('batchrequest') === null) {
            app('config')->set('batchrequest', require __DIR__.'/config/batchrequest.php');
        }
    }

    private function loadLangFile()
    {
        $this->loadTranslationsFrom(__DIR__.'/lang', 'batchrequest');
    }

    private function registerRoute()
    {
        if (!$this->app->routesAreCached() && config('batchrequest.use_endpoint')) {
            $router = app('router');
            $endpointMethod = config('batchrequest.endpoint_method');
            $endpoint = config('batchrequest.endpoint');

            call_user_func_array(array($router, $endpointMethod), array($endpoint, function(){
                $batchRequest = app('Optimus\LaravelBatch\BatchRequest');

                $resultFormatterClass = config('batchrequest.result_formatter');
                $batchRequest->setResultFormatter(new $resultFormatterClass);

                $responseFormatterClass = config('batchrequest.response_formatter');
                $batchRequest->setResponseFormatter(new $responseFormatterClass);

                $actionsKey = config('batchrequest.actions_key');
                $actions = \Request::get($actionsKey, []);

                $batchRequest->request($actions);

                $response = $batchRequest->response();

                return response()->json($response);
            }));
        }
    }

}