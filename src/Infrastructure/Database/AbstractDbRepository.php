<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use Doctrine\DBAL\Connection;

abstract class AbstractDbRepository {
    public function __construct(protected Connection $connection) {
    }

    /**
     * Starta transaktion
     */
    public function beginTransaction() : void {
        $this->connection->beginTransaction();
    }

    /**
     * Commit transaktion
     */
    public function commit() : void {
        $this->connection->commit();
    }

    /**
     * Rollback transaktion
     */
    public function rollBack() : void {
        $this->connection->rollBack();
    }
}
