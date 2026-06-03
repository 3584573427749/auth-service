<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Infrastructure\Database\Connection;
use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Query\QueryBuilder;

abstract class AbstractRepository {
    protected DBALConnection $connection;

    /**
     * Varje konkret repository måste ange sitt tabellnamn
     */
    protected string $table;

    public function __construct(Connection $connection) {
        $this->db = $connection->get();
    }

    /**
     * Skapa en ny QueryBuilder
     */
    protected function qb(): QueryBuilder {
        return $this->db->createQueryBuilder();
    }
}
