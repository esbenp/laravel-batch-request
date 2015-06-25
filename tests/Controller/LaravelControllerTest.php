<?php

use Mockery as m;
use Optimus\BatchRequest\Controller\LaravelController;

class Conf {

    public function get()
    {
        return "actions";
    }

}

class Request {

    public function get()
    {
        return [
            [
                "method" => "GET",
                "action" => "action1",
                "data" => ["data"]
            ]
        ];
    }

}

class App extends \Illuminate\Foundation\Application {

    private $config;

    private $request;

    private $batchrequest;

    public function __construct(){
        $this->config = new Conf();
        $this->request = new Request();
        $this->batchrequest = m::mock("Optimus\BatchRequest\BatchRequest");

        $this->batchrequest->shouldReceive('request')->with(
            m::mustBe($this->request->get())
        )->andReturn(["json" => "data"]);
    }

    public function offsetGet($key)
    {
        return $this->{$key};
    }

}

class LaravelControllerTest extends Orchestra\Testbench\TestCase {

    public function testNormalResultIsFormattedProperly()
    {
        $controller = new LaravelController(new App);
        $response = $controller->request();

        $this->assertEquals('Illuminate\Http\JsonResponse', get_class($response));
    }
    
}