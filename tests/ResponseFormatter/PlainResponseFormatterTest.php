<?php

use Illuminate\Http\Response as LaravelResponse;
use Optimus\BatchRequest\ResponseFormatter\PlainResponseFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PlainResponseFormatterTest extends Orchestra\Testbench\TestCase {

    private $formatter;

    public function setUp()
    {
        parent::setUp();

        $this->formatter = new PlainResponseFormatter();
    }

    public function testErrorneousResponseIsFormattedCorrectly()
    {
        $formatted = $this->formatter->formatResponse(true, [
            "success1",
            "error1"
        ]);

        $this->assertEquals(2, count($formatted));
    }

}
