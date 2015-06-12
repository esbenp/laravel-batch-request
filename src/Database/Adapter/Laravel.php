<?php

namespace Optimus\LaravelBatch\Database\Adapter;

use Optimus\LaravelBatch\Database\TransactionInterface;
use Illuminate\Database\DatabaseManager;

class Laravel implements TransactionInterface {

    private $db;

    public function __construct(DatabaseManager $db) {
        $this->db = $db;
    }

    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    public function rollback()
    {
        return $this->db->rollback();
    }

    public function commit()
    {
        return $this->db->commit();
    }

}