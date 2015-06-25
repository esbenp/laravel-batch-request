<?php

namespace Optimus\LaravelBatch\Database\Adapter;

use Optimus\LaravelBatch\Database\TransactionInterface;

class Null implements TransactionInterface {

    public function beginTransaction()
    {
        return null;
    }

    public function rollback()
    {
        return null;
    }

    public function commit()
    {
        return null;
    }

}