<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Mockery as m;
use Optimus\BatchRequest\BatchRequest;

class BatchRequestTest extends Orchestra\Testbench\TestCase {

    private $routerMock;

    private $responseFormatterMock;

    private $resultFormatterMock;

    private $configMock;

    private $dbMock;

    protected function getPackageProviders($app)
    {
        return ['Optimus\BatchRequest\Provider\LaravelServiceProvider'];
    }

    public function setUp()
    {
        parent::setUp();

        $this->routerMock = m::mock("Optimus\BatchRequest\Router\RouterInterface");
        $this->configMock = [
            "rollback_db_transactions_on_error" => true
        ];
        $this->resultFormatterMock = m::mock("Optimus\BatchRequest\ResultFormatter\OptimusResultFormatter");
        $this->responseFormatterMock = m::mock("Optimus\BatchRequest\ResponseFormatter\OptimusResponseFormatter");
        $this->dbMock = m::mock("Optimus\BatchRequest\Database\TransactionInterface");
    }

    public function tearDown()
    {
        parent::tearDown();

        m::close();
    }

    public function testResultsArePreparedCorrectly()
    {
        $batchRequest = new BatchRequest(
            $this->routerMock,
            $this->configMock,
            new \Optimus\BatchRequest\ResultFormatter\OptimusResultFormatter,
            new \Optimus\BatchRequest\ResponseFormatter\OptimusResponseFormatter,
            $this->dbMock
        );

        $this->dbMock->shouldReceive('rollback')->times(1);

        $errorResponse = new Response('', 500);
        $exception = new \Exception('Exception', 25);
        $errorResponse->exception = $exception;

        $results = $batchRequest->prepareResults([
            JsonResponse::create([
                'data' => 'json'
            ], 200),
            $errorResponse
        ]);

        $firstKey = array_keys($results['responses'])[0];

        $this->assertEquals('error', $results['status']);
        $this->assertEquals(500, $results['responses'][$firstKey]->statusCode);
        $this->assertEquals(25, $results['responses'][$firstKey]->data->code);
        $this->assertEquals('Exception', $results['responses'][$firstKey]->data->message);

        $this->dbMock->shouldReceive('commit')->times(1);

        $results = $batchRequest->prepareResults([
            JsonResponse::create([
                'data' => 'json'
            ], 200)
        ]);

        $this->assertEquals('success', $results['status']);
        $this->assertEquals(200, $results['responses'][0]->statusCode);
    }

    public function testRouterIsFired()
    {
        $batchRequest = m::mock('Optimus\BatchRequest\BatchRequest[prepareResults]', [
            $this->routerMock,
            $this->configMock,
            new \Optimus\BatchRequest\ResultFormatter\OptimusResultFormatter,
            new \Optimus\BatchRequest\ResponseFormatter\OptimusResponseFormatter,
            $this->dbMock
        ]);

        $this->dbMock->shouldReceive('beginTransaction')->times(1);
        $this->routerMock->shouldReceive('batch')->times(1)->with(
            m::mustBe([
                [
                    'method' => 'GET',
                    'action' => 'action1',
                    'key' => 'request'
                ]
            ])
        );
        $batchRequest->shouldReceive('prepareResults')->times(1);

        $results = $batchRequest->request([
            [
                'method' => 'GET',
                'action' => 'action1',
                'key' => 'request'
            ]
        ]);
    }

}
