<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Entities\User;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\UserInUseException;
use App\Domain\Repositories\UserRepository;
use App\Domain\ValueObjects\UserId;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

class DbalUserRepository extends AbstractDbRepository implements UserRepository {
    private const TABLE = 'users';

    public function existsByEmail(string $email) : bool {
        $db = $this->connection->createQueryBuilder();
        $row = $db->select('*')
            ->from(self::TABLE)
            ->where('email=:email')
            ->setParameter('email', $email)
            ->executeQuery()
            ->rowCount();

        return ($row !== 0);
    }

    public function save(User $user) : void {
        if ($user->getUpdatedAt() !== null) {
            $this->connection->update(self::TABLE, $user->asDBRow(), ['id' => $user->getId()->toString()]);
        } else {
            $this->connection->insert(self::TABLE, $user->asDBRow());
        }
    }

    /**
     * @return list<User>
     * @throws Exception
     */
    public function getAll() : array {
        $rows = $this->connection->executeQuery('SELECT * FROM ' . self::TABLE . ' WHERE deleted_at IS NULL')
            ->fetchAllAssociative();

        return array_map(fn ($row) => User::fromDBRow($row), $rows);

    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function getById(UserId $id) : User {
        $row = $this->connection->executeQuery('SELECT * FROM ' . self::TABLE . ' WHERE id=:id AND deleted_at IS NULL', ['id' => $id->toString()])
            ->fetchAssociative();

        if ($row === false) {
            throw new NotFoundException('Användare med id ' . $id->toString() . ' hittades inte');
        }

        return User::fromDBRow($row);
    }

    /**
     * @throws Exception
     */
    public function softDelete(UserId $id) : void {
        $rows = $this->connection
            ->executeQuery('UPDATE ' . self::TABLE . ' SET deleted_at=:now WHERE id=:id', ['id' => $id->toString(), 'now' => date('Y-m-d H:i:s')])
            ->rowCount();

        if ($rows === 0) {
            throw new NotFoundException('Användare med id ' . $id->toString() . ' hittades inte');
        }
    }

    public function emailExistsWithOtherUser(string $email, UserId $id) : bool {
        $db = $this->connection->createQueryBuilder();
        $row = $db->select('*')
            ->from(self::TABLE)
            ->where('email=:email')
            ->andWhere('id != :id')
            ->setParameter('email', $email)
            ->setParameter('id', $id->toString())
            ->executeQuery()
            ->rowCount();

        return ($row !== 0);
    }

    public function remove(UserId $id) : void {
        try {
            $rows = $this->connection->delete(self::TABLE, ['id' => $id->toString()]);

            if ($rows === 0) {
                throw new NotFoundException('Användare med id ' . $id->toString() . ' hittades inte');
            }
        } catch (ForeignKeyConstraintViolationException $e) {
            throw new UserInUseException('Användaren används i en eller flera andra tabeller');
        }
    }
}
