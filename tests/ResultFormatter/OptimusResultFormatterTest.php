<?php

use Illuminate\Http\Response as LaravelResponse;
use Optimus\BatchRequest\ResultFormatter\OptimusResultFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OptimusResultFormatterTest extends Orchestra\Testbench\TestCase {

    private $formatter;

    protected function getPackageProviders($app)
    {
        return ['Optimus\BatchRequest\Provider\LaravelServiceProvider'];
    }

    public function setUp()
    {
        parent::setUp();

        $this->formatter = new OptimusResultFormatter();
    }

    public function testNormalResultIsFormattedProperly()
    {
        $response = Response::create('<html>', 200);

        $formatted = $this->formatter->formatResult($response);

        $this->assertEquals('stdClass', get_class($formatted));
        $this->assertEquals(200, $formatted->statusCode);
        $this->assertEquals('<html>', $formatted->data);
    }

    public function testJsonResultIsFormattedProperly()
    {
        $response = JsonResponse::create([
            "format" => "json"
        ], 200);

        $formatted = $this->formatter->formatResult($response);

        $this->assertEquals('stdClass', get_class($formatted));
        $this->assertEquals(200, $formatted->statusCode);
        $this->assertEquals('json', $formatted->data->format);
    }

    public function testExceptionResultIsFormattedProperly()
    {
        try {
            throw new \Exception("Message", 25);
        } catch(Exception $e) {
            $response = new LaravelResponse('Response', 500);
            $response->exception = $e;

            $formatted = $this->formatter->formatResult($response);

            $this->assertEquals(500, $formatted->statusCode);
            $this->assertEquals(25, $formatted->data->code);
            $this->assertEquals("Message", $formatted->data->message);
            $this->assertEquals("integer", gettype($formatted->data->line));
        }
    }
    
}