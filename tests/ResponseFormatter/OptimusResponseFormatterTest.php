<?php

use Optimus\BatchRequest\ResponseFormatter\OptimusResponseFormatter;

class OptimusResponseFormatterTest extends Orchestra\Testbench\TestCase {

    private $formatter;

    protected function getPackageProviders($app)
    {
        return ['Optimus\BatchRequest\Provider\LaravelServiceProvider'];
    }

    public function setUp()
    {
        parent::setUp();

        $this->formatter = new OptimusResponseFormatter();
    }

    public function testErrorneousResponseIsFormattedCorrectly()
    {
        $formatted = $this->formatter->formatResponse("success", [
            "success1"
        ], [
            "error1"
        ]);

        $this->assertEquals("success", $formatted["status"]);
        $this->assertEquals("error1", $formatted["responses"][0]);
        $this->assertEquals(1, count($formatted["responses"]));
    }

    public function testSuccessfulResponseIsFormattedCorrectly()
    {
        $formatted = $this->formatter->formatResponse("success", [
            "success1",
            "success2"
        ], []);

        $this->assertEquals("success", $formatted["status"]);
        $this->assertEquals("success1", $formatted["responses"][0]);
        $this->assertEquals(2, count($formatted["responses"]));
    }
    
}