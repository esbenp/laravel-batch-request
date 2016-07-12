<?php

use Mockery as m;
use Optimus\BatchRequest\Database\Adapter\LaravelAdapter;

class LaravelAdapterTest extends Orchestra\Testbench\TestCase {

    public function testUnderlyingMethodsAreCalled()
    {
        $mock = m::mock('Illuminate\Database\DatabaseManager');
        $database = new LaravelAdapter($mock);

        $mock->shouldReceive('beginTransaction')->times(1);

        $database->beginTransaction();

        $mock->shouldReceive('commit')->times(1);

        $database->commit();

        $mock->shouldReceive('rollback')->times(1);

        $database->rollback();
    }
    
}