<?php

use Mockery as m;
use Optimus\BatchRequest\Database\Adapter\Laravel;

class LaravelTest extends Orchestra\Testbench\TestCase {

    public function testUnderlyingMethodsAreCalled()
    {
        $mock = m::mock('Illuminate\Database\DatabaseManager');
        $database = new Laravel($mock);

        $mock->shouldReceive('beginTransaction')->times(1);

        $database->beginTransaction();

        $mock->shouldReceive('commit')->times(1);

        $database->commit();

        $mock->shouldReceive('rollback')->times(1);

        $database->rollback();
    }
    
}