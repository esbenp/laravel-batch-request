<?php

use Optimus\BatchRequest\Facade\BatchRequest as BatchRequestFacade;

class StubFacade extends BatchRequestFacade {
    public function getAccessor()
    {
        return parent::getFacadeAccessor();
    }
}

class BatchRequestFacadeTest extends Orchestra\Testbench\TestCase {

    public function testFacadeIsWorking()
    {
        $facade = new StubFacade;

        $this->assertEquals('batchrequest', $facade->getAccessor());
    }
    
}