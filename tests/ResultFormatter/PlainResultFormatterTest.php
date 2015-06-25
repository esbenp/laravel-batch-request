<?php

use Optimus\BatchRequest\ResultFormatter\PlainResultFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PlainResultFormatterTest extends Orchestra\Testbench\TestCase {

    private $formatter;

    public function setUp()
    {
        parent::setUp();

        $this->formatter = new PlainResultFormatter();
    }

    public function testNormalResultIsFormattedProperly()
    {
        $response = Response::create('<html>', 200);

        $formatted = $this->formatter->formatResult($response);

        $this->assertEquals('<html>', $formatted);
    }

    public function testJsonResultIsFormattedProperly()
    {
        $response = JsonResponse::create([
            "format" => "json"
        ], 200);

        $formatted = $this->formatter->formatResult($response);

        $this->assertEquals('json', $formatted->format);
    }
    
}