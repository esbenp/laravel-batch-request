<?php

namespace Optimus\BatchRequest\Provider;

use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Routing\Router as LaravelRouter;
use Illuminate\Support\ServiceProvider as BaseProvider;
use Optimus\ApiConsumer\Router as OptimusApiConsumerRouter;
use Optimus\BatchRequest\BatchRequest;
use Optimus\BatchRequest\Database\Adapter\Laravel as LaravelDatabase;
use Optimus\BatchRequest\Database\Adapter\Null as NullDatabase;
use Optimus\BatchRequest\Router\Adapter\OptimusApiConsumer as OptimusApiConsumerRouterAdapter;

class LaravelServiceProvider extends BaseProvider {

    public function register()
    {
        $this->loadConfig();
        $this->registerAssets();
        $this->bindInstance();
    }

    public function bindInstance()
    {
        $this->app->singleton('batchrequest', function(){
            $config = $this->app['config']->get('batchrequest');
            $database = $this->app['db'];
            $request = $this->app['request'];
            $router = $this->app['router'];

            $router = $this->createRouterAdapter($this->app, $request, $router, $config);
            $databaseManager = $this->createDatabaseAdapter($database, $config);
            
            $resultFormatterClass = $config['result_formatter'];
            $responseFormatterClass = $config['response_formatter'];

            $batchRequest = new BatchRequest(
                $router,
                $config,
                new $resultFormatterClass,
                new $responseFormatterClass,
                $databaseManager
            );

            return $batchRequest;
        });
    }

    private function createRouterAdapter(Application $app, LaravelRequest $request, LaravelRouter $router, array $config)
    {
        $OptimusApiConsumerRouter = new OptimusApiConsumerRouter($app, $request, $router);
        $adapter = new OptimusApiConsumerRouterAdapter($OptimusApiConsumerRouter, $config);

        return $adapter;
    }

    private function createDatabaseAdapter(DatabaseManager $databaseManager, array $config)
    {
        return $databaseManager = $config['wrap_in_database_transaction'] ? 
                                    new LaravelDatabase($databaseManager) : new NullDatabase();
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
        if ($this->app['config']->get('batchrequest') === null) {
            $this->app['config']->set('batchrequest', require __DIR__.'/../config/batchrequest.php');
        }
    }

    private function loadLangFile()
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'batchrequest');
    }

}
