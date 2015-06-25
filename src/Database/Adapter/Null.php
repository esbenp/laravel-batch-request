<?php

namespace Optimus\BatchRequest\Database\Adapter;

use Optimus\BatchRequest\Database\TransactionInterface;

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