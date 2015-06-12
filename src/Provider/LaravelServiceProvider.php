<?php

namespace Optimus\LaravelBatch\Provider;

use Illuminate\Support\ServiceProvider as BaseProvider;

class LaravelServiceProvider extends BaseProvider {

    public function register()
    {
        $this->loadConfig();
        $this->registerAssets();
    }

    public function boot()
    {
        $this->loadLangFile();
    }

    private function registerAssets()
    {
        $this->publishes([
            __DIR__.'/../config/batchrequest.php' => config_path('batchrequest.php')
        ]);
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

}