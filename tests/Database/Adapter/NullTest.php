<?php

use Optimus\BatchRequest\Database\Adapter\Null;

class NullTest extends Orchestra\Testbench\TestCase {

    public function testNullIsReturned()
    {
        $database = new Null();

        $this->assertNull($database->beginTransaction());
        $this->assertNull($database->rollback());
        $this->assertNull($database->commit());
    }
    
}