<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\User;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepository;
use App\Infrastructure\Database\AbstractDbRepository;

class DbalUserRepository extends AbstractDbRepository implements UserRepository
{
    private const TABLE = 'users';

    public function existsByEmail(string $email): bool
    {
        $db = $this->connection->createQueryBuilder();
        $row = $db->select('*')
                  ->from(self::TABLE)
                  ->where('email=:email')
                  ->setParameter('email', $email)
                  ->executeQuery()
                  ->rowCount();

        return ($row !== 0);
    }

    public function save(User $user): void
    {
        if ($user->getUpdatedAt() !== null) {
            $this->connection->update(self::TABLE, $user->asDBRow(), ['id' => $user->getId()->toString()]);
        } else {
            $this->connection->insert(self::TABLE, $user->asDBRow());
        }
    }
}
