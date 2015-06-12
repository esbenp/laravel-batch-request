<?php

namespace Optimus\LaravelBatch\Database;

interface TransactionInterface {

    public function beginTransaction();

    public function rollback();

    public function commit();

}