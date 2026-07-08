<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Entities\Role;
use App\Domain\Entities\User;
use App\Domain\Entities\UserRole;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\UserRoleAlreadyExistsException;
use App\Domain\Repositories\UserRoleRepository;
use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class DbalUserRoleRepository extends AbstractDbRepository implements UserRoleRepository {
    private const TABLE = 'userroles';

    /**
     * @return list<Role>
     * @throws Exception
     */
    public function getRoles(UserId $id) : array {
        $rows = $this->connection->executeQuery(
            'SELECT * FROM roles 
    INNER JOIN ' . self::TABLE . ' ON roles.id = ' . self::TABLE . '.role_id 
    WHERE ' . self::TABLE . '.user_id=:user_id',
            ['user_id' => $id->toString()],
        )
            ->fetchAllAssociative();

        return array_map(fn ($row) => Role::fromDBRow($row), $rows);

    }

    /**
     * @return list<User>
     * @throws Exception
     */
    public function getUsers(RoleId $id) : array {
        $rows = $this->connection->executeQuery(
            'SELECT * FROM users 
    INNER JOIN ' . self::TABLE . ' ON users.id = ' . self::TABLE . '.user_id 
    WHERE ' . self::TABLE . '.role_id=:role_id',
            ['role_id' => $id->toString()],
        )
            ->fetchAllAssociative();

        return array_map(fn ($row) => User::fromDBRow($row), $rows);

    }

    /**
     * @throws Exception
     * @throws NotFoundException
     */
    public function delete(UserRole $userRole) : void {
        $rows = $this->connection->delete(self::TABLE, $userRole->asDBRow());

        if ($rows === 0) {
            throw new NotFoundException('Användare med rollen hittades inte');
        }
    }

    public function save(UserRole $userRole) : void {
        try {
            $this->connection->insert(self::TABLE, $userRole->asDBRow());
        } catch (UniqueConstraintViolationException $e) {
            throw new UserRoleAlreadyExistsException('Rollen är redan tilldelad användaren');
        }
    }
}
