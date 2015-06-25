<?php

namespace Optimus\BatchRequest\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Optimus\BatchRequest\BatchRequest
 */
class BatchRequest extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'batchrequest';
    }
}
