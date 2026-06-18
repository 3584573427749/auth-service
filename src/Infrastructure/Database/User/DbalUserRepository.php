<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\User;

use App\Domain\Entities\User;
use App\Domain\Exception\NotFoundException;
use App\Domain\Repositories\UserRepository;
use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Database\AbstractDbRepository;
use Doctrine\DBAL\Exception;

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
        $rows = $this->connection->executeQuery('SELECT * FROM ' . self::TABLE)
            ->fetchAllAssociative();

        return array_map(fn ($row) => User::fromDBRow($row), $rows);

    }

    /**
     * @throws Exception
     */
    public function getById(UserId $id) : User {
        $row = $this->connection->executeQuery('SELECT * FROM ' . self::TABLE . ' WHERE id=:id', ['id' => $id->toString()])
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
        $this->connection->executeQuery('UPDATE ' . self::TABLE . ' SET is_active=0 WHERE id=:id', ['id' => $id->toString()]);

    }
}
