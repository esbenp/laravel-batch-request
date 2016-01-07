<?php

use Mockery as m;

class LaravelServiceProviderTestMockConf {
    public function get() {return [];}
}

class LaravelServiceProviderTestMockApp extends \Illuminate\Foundation\Application {
    private $config;
    public function __construct(){
        $this->config = new LaravelServiceProviderTestMockConf;
    }
    public function offsetGet($key){
        return $this->{$key};
    }
}

class LaravelServiceProviderTest extends Orchestra\Testbench\TestCase {

    public function setUp()
    {
        parent::setUp();

        $this->appMock = m::mock('Illuminate\Foundation\Application');
    }

    public function testBindInstanceCallsContainer()
    {
        $this->appMock->shouldReceive('singleton')->with(
            'batchrequest',
            m::type('Closure')
        );

        $provider = $this->app->make('Optimus\BatchRequest\Provider\LaravelServiceProvider', [
            $this->appMock
        ]);

        $provider->bindInstance();
    }
    
    public function testEverythingIsFired()
    {
        $mock = m::mock("Optimus\BatchRequest\Provider\LaravelServiceProvider[bindInstance,publishes]",[
            new LaravelServiceProviderTestMockApp
        ]);

        $mock->shouldReceive('bindInstance')->times(1);

        $mock->register();
    }

}
