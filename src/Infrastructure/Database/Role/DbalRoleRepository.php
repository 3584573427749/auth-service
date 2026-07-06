<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Role;

use App\Domain\Entities\Role;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\RoleAlreadyExistsException;
use App\Domain\Repositories\RoleRepository;
use App\Domain\ValueObjects\RoleId;
use App\Infrastructure\Database\AbstractDbRepository;
use Doctrine\DBAL\Exception;

class DbalRoleRepository extends AbstractDbRepository implements RoleRepository {
    private const TABLE = 'roles';

    /**
     * @throws RoleAlreadyExistsException
     */
    public function save(Role $role) : void {
        try {
            if ($role->getUpdatedAt() !== null) {
                $this->connection->update(self::TABLE, $role->asDBRow(), ['id' => $role->getId()->toString()]);
            } else {
                $this->connection->insert(self::TABLE, $role->asDBRow());
            }
        } catch (Exception\UniqueConstraintViolationException $e) {
            throw new RoleAlreadyExistsException('Rollen finns redan');
        }
    }

    /**
     * @return list<Role>
     * @throws Exception
     */
    public function getAll() : array {
        $rows = $this->connection->executeQuery('SELECT * FROM ' . self::TABLE)
            ->fetchAllAssociative();

        return array_map(fn ($row) => Role::fromDBRow($row), $rows);

    }

    /**
     * @throws Exception
     */
    public function getById(RoleId $id) : Role {
        $row = $this->connection->executeQuery('SELECT * FROM ' . self::TABLE . ' WHERE id=:id', ['id' => $id->toString()])
            ->fetchAssociative();

        if ($row === false) {
            throw new NotFoundException('Roll med id ' . $id->toString() . ' hittades inte');
        }

        return Role::fromDBRow($row);
    }

    /**
     * @throws Exception
     */
    public function delete(RoleId $id) : void {
        $rows = $this->connection
            ->executeQuery('DELETE FROM ' . self::TABLE . ' WHERE id=:id', ['id' => $id->toString()])
            ->rowCount();

        if ($rows === 0) {
            throw new NotFoundException('Roll med id ' . $id->toString() . ' hittades inte');
        }
    }
}
