<?php

namespace Optimus\BatchRequest\Database;

interface TransactionInterface {

    public function beginTransaction();

    public function rollback();

    public function commit();

}