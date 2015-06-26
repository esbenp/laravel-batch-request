<?php

use Mockery as m;
use Optimus\BatchRequest\Router\Adapter\OptimusApiConsumer;

class OptimusApiConsumerTest extends Orchestra\Testbench\TestCase {

    private $routerMock;

    private $configMock;

    public function setUp()
    {
        parent::setUp();

        $this->routerMock = m::mock("Optimus\ApiConsumer\Router");
        $this->configMock = [
            'url_prefix' => false
        ];
    }

    public function tearDown()
    {
        parent::tearDown();

        m::close();
    }

    public function testThatBatchRequestWasCalledOnRouterWithCorrectParameters()
    {
        $this->configMock['url_prefix'] = false;

        $this->routerMock->shouldReceive('batchRequest')->times(1)->with(
            m::on(function($formattedRequests){
                $firstRequest = $formattedRequests[0];
                $this->assertEquals('GET', $firstRequest[0]);
                $this->assertEquals('action1', $firstRequest[1]);
                $this->assertEquals(['data'], $firstRequest[2]);
                return true;
            })
        )->andReturn([
            ['data' => [], 'status' => 200],
            ['data' => [], 'status' => 200]
        ]);

        $router = new OptimusApiConsumer($this->routerMock, $this->configMock);

        $responses = $router->batch([
            [
                'key' => 'key1',
                'method' => 'GET',
                'action' => 'action1',
                'data' => ['data']
            ],
            [
                'key' => 'key2',
                'method' => 'GET',
                'action' => 'action2',
                'data' => ['data']
            ]
        ]);

        $responseKeys = array_keys($responses);

        $this->assertEquals('key1', $responseKeys[0]);
        $this->assertEquals('key2', $responseKeys[1]);
    }

    public function testThatBatchRequestWorksWithoutDefiningMethodAndData()
    {
      $this->routerMock->shouldReceive('batchRequest')->andReturn([
          ['data' => [], 'status' => 200]
        ]);

      $router = new OptimusApiConsumer($this->routerMock, $this->configMock);

      $responses = $router->batch([
        [
          'key' => 'key1',
          'action' => 'action1'
        ]
      ]);
    }

    public function testThatActionUrlIsPrefixedCorrectly()
    {
        $this->configMock['url_prefix'] = '/prefix';

        $this->routerMock->shouldReceive('batchRequest')->times(1)->with(
            m::on(function($formattedRequests){
                $firstRequest = $formattedRequests[0];
                $this->assertEquals('/prefix/action1', $firstRequest[1]);
                return true;
            })
        )->andReturn([
            ['data' => [], 'status' => 200]
        ]);

        $router = new OptimusApiConsumer($this->routerMock, $this->configMock);

        $responses = $router->batch([
            [
                'key' => 'key1',
                'method' => 'GET',
                'action' => '/action1',
                'data' => ['data']
            ]
        ]);
    }

}
