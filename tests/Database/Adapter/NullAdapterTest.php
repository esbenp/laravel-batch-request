<?php

use Optimus\BatchRequest\Database\Adapter\NullAdapter;

class NullAdapterTest extends Orchestra\Testbench\TestCase {

    public function testNullIsReturned()
    {
        $database = new NullAdapter();

        $this->assertNull($database->beginTransaction());
        $this->assertNull($database->rollback());
        $this->assertNull($database->commit());
    }
    
}